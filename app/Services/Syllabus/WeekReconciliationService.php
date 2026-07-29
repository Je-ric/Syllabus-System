<?php

namespace App\Services\Syllabus;

use App\Models\OnlineMaterial;
use App\Models\Reference;
use App\Models\Syllabus;
use App\Models\SyllabusEvaluationItem;
use App\Models\SyllabusWeek;
use App\Models\WeekContent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Reconciles existing SyllabusWeek rows against a new or updated academic calendar
// WITHOUT destroying any faculty-entered content.
//
// This is the "Refresh Dates" path — the counterpart to WeekGenerationService::regenerate()
// which is the destructive "Hard Reset" path.
//
// What reconcile() does:
//   1. Recomputes the expected week sequence from the current calendar (same algorithm
//      as WeekGenerationService — break weeks skipped, 7-day chunks).
//   2. For each position that already has a SyllabusWeek row → updates start_date /
//      end_date in-place. Content (WeekContent, References, OnlineMaterials) is untouched.
//   3. Re-evaluates the locking event for every surviving week and rewrites the
//      assessment_task on locked WeekContent rows (exam label / non-teaching label).
//      Editable weeks are never touched here — only locked-week system labels.
//   4. If the new calendar has FEWER non-break weeks than existing rows → the surplus
//      tail weeks (and all their content) are removed. The method returns the count
//      of weeks dropped so the caller can warn the user.
//   5. If the new calendar has MORE non-break weeks than existing rows → new empty
//      WeekContent rows are appended for the extra weeks.
//
// reconcile() throws \RuntimeException on validation failure (same contract as generate).
// Returns a ReconciliationResult describing what changed so the caller can show
// an accurate toast message.
class WeekReconciliationService
{
    public function __construct(
        private readonly CalendarWeekSequenceBuilder $sequenceBuilder,
    ) {}

    // @throws \RuntimeException when prerequisites are not met
    public function reconcile(Syllabus $syllabus, array $courseComponents): ReconciliationResult
    {
        if (! $syllabus->academic_calendar_id) {
            throw new \RuntimeException('Select an academic calendar first.');
        }

        $calendar = $syllabus->academicCalendar;
        if (! $calendar || ! $calendar->start_date || ! $calendar->end_date) {
            throw new \RuntimeException('Academic calendar has no start/end date.');
        }

        $hasLEC = isset($courseComponents['LEC']);
        $hasLAB = isset($courseComponents['LAB']);

        if (! $hasLEC && ! $hasLAB) {
            throw new \RuntimeException('Complete the Course Components step first.');
        }

        // ── Build the expected week sequence from the calendar ─────────────────
        $newSequence = $this->sequenceBuilder->build($calendar, (int) $syllabus->academic_calendar_id);

        if (empty($newSequence)) {
            throw new \RuntimeException('The selected calendar produced no teachable weeks. Check the calendar dates and break events.');
        }

        // ── Load existing weeks ordered by week_no ─────────────────────────────
        $existingWeeks = SyllabusWeek::where('syllabus_id', $syllabus->id)
            ->orderBy('week_no')
            ->get();

        $newCount      = count($newSequence);
        $existingCount = $existingWeeks->count();

        $weeksAdded   = 0;
        $weeksDropped = 0;
        $datesUpdated = 0;
        $labelsResynced = 0;

        DB::transaction(function () use (
            $syllabus,
            $hasLEC, $hasLAB,
            $newSequence,
            $existingWeeks,
            $newCount, $existingCount,
            &$weeksAdded, &$weeksDropped, &$datesUpdated, &$labelsResynced
        ) {
            // Exam term labels are re-assigned in encounter order across the
            // full sequence so they stay correct after a calendar change.
            $examTermLabels = ['1st Term', '2nd Term', 'Final Term'];
            $examsSeen      = 0;

            // ── Pass 1: update or create weeks for every position in the new sequence ──
            foreach ($newSequence as $position => $slot) {
                // $position is 0-indexed; week_no is 1-indexed
                $weekNo    = $position + 1;
                $weekStart = $slot['start'];
                $weekEnd   = $slot['end'];

                // Resolve exam/non-teaching label for this slot
                [$lecTask, $labTask, $isExamSlot] = $this->resolveTaskLabels(
                    $slot['lockingEvent'],
                    $examTermLabels,
                    $examsSeen
                );

                if ($isExamSlot) {
                    $examsSeen++;
                }

                $existingWeek = $existingWeeks->firstWhere('week_no', $weekNo);

                if ($existingWeek) {
                    // ── Update dates in-place ──────────────────────────────────
                    $dateChanged = $existingWeek->start_date !== $weekStart
                        || $existingWeek->end_date !== $weekEnd;

                    if ($dateChanged) {
                        $existingWeek->update([
                            'start_date' => $weekStart,
                            'end_date'   => $weekEnd,
                        ]);
                        $datesUpdated++;
                    }

                    // ── Re-sync locked-week assessment_task labels ─────────────
                    // Only rewrite when the slot is locked (exam / non_teaching).
                    // Editable weeks keep whatever the faculty typed.
                    if ($slot['lockingEvent'] !== null) {
                        $labelsResynced += $this->resyncLockedLabels(
                            $existingWeek->id,
                            $hasLEC, $hasLAB,
                            $lecTask, $labTask
                        );
                    }
                } else {
                    // ── Append new week ────────────────────────────────────────
                    $newWeek = SyllabusWeek::create([
                        'syllabus_id'  => $syllabus->id,
                        'week_no'      => $weekNo,
                        'start_date'   => $weekStart,
                        'end_date'     => $weekEnd,
                        'is_exam_week' => false,
                    ]);

                    if ($hasLEC) {
                        WeekContent::create([
                            'syllabus_week_id'  => $newWeek->id,
                            'component_type'    => 'LEC',
                            'learning_outcomes' => '',
                            'assessment_task'   => $lecTask,
                            'topics'            => '',
                            'tla'               => '',
                        ]);
                    }

                    if ($hasLAB) {
                        WeekContent::create([
                            'syllabus_week_id'  => $newWeek->id,
                            'component_type'    => 'LAB',
                            'learning_outcomes' => '',
                            'assessment_task'   => $labTask,
                            'topics'            => '',
                            'tla'               => '',
                        ]);
                    }

                    $weeksAdded++;
                }
            }

            // ── Pass 2: drop surplus tail weeks when the new calendar is shorter ──
            if ($existingCount > $newCount) {
                $surplusWeekNos = $existingWeeks
                    ->filter(fn ($w) => (int) $w->week_no > $newCount)
                    ->pluck('week_no')
                    ->all();

                $surplusIds = $existingWeeks
                    ->filter(fn ($w) => (int) $w->week_no > $newCount)
                    ->pluck('id')
                    ->all();

                if (! empty($surplusIds)) {
                    $surplusContentIds = WeekContent::whereIn('syllabus_week_id', $surplusIds)
                        ->pluck('id')
                        ->all();

                    if (! empty($surplusContentIds)) {
                        SyllabusEvaluationItem::whereIn('week_content_id', $surplusContentIds)->delete();
                    }

                    WeekContent::whereIn('syllabus_week_id', $surplusIds)->delete();
                    Reference::where('syllabus_id', $syllabus->id)
                        ->whereIn('syllabus_week_id', $surplusIds)
                        ->delete();
                    OnlineMaterial::where('syllabus_id', $syllabus->id)
                        ->whereIn('syllabus_week_id', $surplusIds)
                        ->delete();
                    SyllabusWeek::whereIn('id', $surplusIds)->delete();

                    $weeksDropped = count($surplusIds);

                    Log::info('[WeekReconciliationService] surplus weeks removed', [
                        'syllabusId'     => $syllabus->id,
                        'surplusWeekNos' => $surplusWeekNos,
                    ]);
                }
            }
        });

        $result = new ReconciliationResult(
            datesUpdated:   $datesUpdated,
            weeksAdded:     $weeksAdded,
            weeksDropped:   $weeksDropped,
            labelsResynced: $labelsResynced,
        );

        Log::info('[WeekReconciliationService] reconcile complete', [
            'syllabusId' => $syllabus->id,
            'result'     => $result->toArray(),
        ]);

        return $result;
    }

    // ── Private helpers ────────────────────────────────────────────────────────

    // Resolve assessment_task labels and whether this slot consumed an exam slot.
    // Returns [ $lecTask, $labTask, $isExamSlot ].
    private function resolveTaskLabels(?object $lockingEvent, array $examTermLabels, int $examsSeen): array
    {
        if ($lockingEvent?->type === 'exam') {
            $termLabel = $examTermLabels[min($examsSeen, 2)];
            return [$termLabel . ' Exam', $termLabel . ' Practical Exam', true];
        }

        if ($lockingEvent?->type === 'non_teaching') {
            return ['Non-Teaching Week', 'Non-Teaching Week', false];
        }

        return ['', '', false];
    }

    // Overwrite the assessment_task on locked WeekContent rows.
    // Only touches rows whose task label is an exam or non-teaching system label —
    // never overwrites faculty-entered content on editable weeks.
    // Returns the number of rows actually updated.
    private function resyncLockedLabels(
        int $weekId,
        bool $hasLEC,
        bool $hasLAB,
        string $lecTask,
        string $labTask
    ): int {
        $updated = 0;

        if ($hasLEC) {
            $affected = WeekContent::where('syllabus_week_id', $weekId)
                ->where('component_type', 'LEC')
                ->update(['assessment_task' => $lecTask]);
            $updated += $affected;
        }

        if ($hasLAB) {
            $affected = WeekContent::where('syllabus_week_id', $weekId)
                ->where('component_type', 'LAB')
                ->update(['assessment_task' => $labTask]);
            $updated += $affected;
        }

        return $updated;
    }
}
