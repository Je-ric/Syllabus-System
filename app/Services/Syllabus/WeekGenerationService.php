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

// Owns the full lifecycle of SyllabusWeek rows for a syllabus.
//
// Public API:
//   generate()       — first-time creation (idempotent guard included)
//   hardReset()      — destroy ALL content then recreate fresh from the calendar
//   deleteAllWeeks() — hard-delete weeks + all dependent rows (called by hardReset)
//
// For updating dates while keeping existing faculty content, use
// WeekReconciliationService::reconcile() instead.
//
// Break weeks are SKIPPED (no row created, week numbers stay sequential).
// Exam / non-teaching labels are written into WeekContent rows at creation
// time so WeekLockService remains a pure read.
//
// generate() and hardReset() throw \RuntimeException on validation failure
// (no calendar, no components) — the caller (WeeklyCoverageStep) catches
// and dispatches the appropriate toast. Services must not depend on Livewire.
class WeekGenerationService
{
    public function __construct(
        private readonly CalendarWeekSequenceBuilder $sequenceBuilder,
    ) {}

    // Generate weeks for the first time.
    // Idempotent — exits cleanly if rows already exist.
    // @throws \RuntimeException when prerequisites are not met
    public function generate(Syllabus $syllabus, array $courseComponents): bool
    {
        if (! $syllabus->academic_calendar_id) {
            throw new \RuntimeException('Select an academic calendar first.');
        }

        return $this->createWeekRows($syllabus, $courseComponents);
    }

    // Destroy every existing week + all faculty content, then recreate fresh.
    // This is the destructive "Hard Reset" path — call reconcile() to preserve content.
    // @throws \RuntimeException when prerequisites are not met
    public function hardReset(Syllabus $syllabus, array $courseComponents): bool
    {
        $this->deleteAllWeeks($syllabus);
        return $this->createWeekRows($syllabus, $courseComponents);
    }

    // Hard-delete all SyllabusWeek rows and every dependent data row for a syllabus.
    // Evaluation items are deleted first to avoid FK violations on week_contents.
    public function deleteAllWeeks(Syllabus $syllabus): void
    {
        $weekIds = SyllabusWeek::where('syllabus_id', $syllabus->id)->pluck('id')->all();

        if (empty($weekIds)) {
            return;
        }

        // Evaluation items reference week_contents — delete them first.
        $weekContentIds = WeekContent::whereIn('syllabus_week_id', $weekIds)->pluck('id')->all();
        if (! empty($weekContentIds)) {
            SyllabusEvaluationItem::whereIn('week_content_id', $weekContentIds)->delete();
        }

        WeekContent::whereIn('syllabus_week_id', $weekIds)->delete();
        Reference::where('syllabus_id', $syllabus->id)->whereIn('syllabus_week_id', $weekIds)->delete();
        OnlineMaterial::where('syllabus_id', $syllabus->id)->whereIn('syllabus_week_id', $weekIds)->delete();
        SyllabusWeek::whereIn('id', $weekIds)->delete();
    }

    // ── Private ───────────────────────────────────────────────────────────────

    // Core week-row creation loop.
    // Idempotent: exits immediately if rows already exist for this syllabus.
    // The entire loop runs inside a DB transaction so a mid-loop failure
    // never leaves a partial week set.
    // @throws \RuntimeException when the calendar has no dates or no components exist
    private function createWeekRows(Syllabus $syllabus, array $courseComponents): bool
    {
        if (SyllabusWeek::where('syllabus_id', $syllabus->id)->exists()) {
            return true;
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

        $sequence = $this->sequenceBuilder->build($calendar, (int) $syllabus->academic_calendar_id);

        if (empty($sequence)) {
            throw new \RuntimeException('The selected calendar produced no teachable weeks. Check the calendar dates and break events.');
        }

        // Exam labels assigned in encounter order across weeks.
        $examTermLabels = ['1st Term', '2nd Term', 'Final Term'];
        $examsSeen      = 0;

        $totalCreated = DB::transaction(function () use (
            $syllabus, $hasLEC, $hasLAB, $sequence, $examTermLabels, &$examsSeen
        ) {
            foreach ($sequence as $index => $slot) {
                $weekNo = $index + 1;

                $syllabusWeek = SyllabusWeek::create([
                    'syllabus_id'  => $syllabus->id,
                    'week_no'      => $weekNo,
                    'start_date'   => $slot['start'],
                    'end_date'     => $slot['end'],
                    'is_exam_week' => false,
                ]);

                // Resolve assessment_task labels for locked weeks.
                // Written at creation time — WeekLockService stays a pure read.
                $lockingEvent = $slot['lockingEvent'];
                $lecTask      = '';
                $labTask      = '';

                if ($lockingEvent?->type === 'exam') {
                    $termLabel = $examTermLabels[min($examsSeen, 2)];
                    $examsSeen++;
                    $lecTask = $termLabel . ' Exam';
                    $labTask = $termLabel . ' Practical Exam';
                } elseif ($lockingEvent?->type === 'non_teaching') {
                    $lecTask = 'Non-Teaching Week';
                    $labTask = 'Non-Teaching Week';
                }

                if ($hasLEC) {
                    WeekContent::create([
                        'syllabus_week_id'  => $syllabusWeek->id,
                        'component_type'    => 'LEC',
                        'learning_outcomes' => '',
                        'assessment_task'   => $lecTask,
                        'topics'            => '',
                        'tla'               => '',
                    ]);
                }

                if ($hasLAB) {
                    WeekContent::create([
                        'syllabus_week_id'  => $syllabusWeek->id,
                        'component_type'    => 'LAB',
                        'learning_outcomes' => '',
                        'assessment_task'   => $labTask,
                        'topics'            => '',
                        'tla'               => '',
                    ]);
                }
            }

            return count($sequence);
        });

        Log::info('[WeekGenerationService] weeks created', [
            'syllabusId' => $syllabus->id,
            'total'      => $totalCreated,
        ]);

        return $totalCreated > 0;
    }
}
