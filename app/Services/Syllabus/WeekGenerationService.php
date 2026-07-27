<?php

namespace App\Services\Syllabus;

use App\Models\AcademicCalendarEvent;
use App\Models\OnlineMaterial;
use App\Models\Reference;
use App\Models\Syllabus;
use App\Models\SyllabusEvaluationItem;
use App\Models\SyllabusWeek;
use App\Models\WeekContent;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Owns the full lifecycle of SyllabusWeek rows for a syllabus.
//   generate()       — first-time creation (idempotent guard included)
//   regenerate()     — wipe all existing rows then recreate from scratch
//   deleteAllWeeks() — delete weeks + dependent rows (used by regenerate)
//
// Break weeks are SKIPPED (no row created, week numbers stay sequential).
// Exam / non-teaching labels are written into WeekContent rows at creation
// time so WeekLockService remains a pure read.
//
// generate() and regenerate() throw \RuntimeException on validation failure
// (no calendar, no components) — the caller (WeeklyCoverageStep) catches
// and dispatches the appropriate toast. Services must not depend on Livewire.
class WeekGenerationService
{
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

    // Delete every existing week then regenerate fresh from the calendar.
    // @throws \RuntimeException when prerequisites are not met
    public function regenerate(Syllabus $syllabus, array $courseComponents): bool
    {
        $this->deleteAllWeeks($syllabus);
        return $this->createWeekRows($syllabus, $courseComponents);
    }

    // Hard-delete all SyllabusWeek rows and their dependent data for a syllabus.
    // Also deletes SyllabusEvaluationItem rows so no orphaned weight records
    // remain after a regeneration.
    public function deleteAllWeeks(Syllabus $syllabus): void
    {
        $weekIds = SyllabusWeek::where('syllabus_id', $syllabus->id)->pluck('id')->all();

        if (empty($weekIds)) {
            return;
        }

        // Delete evaluation items that reference week contents under these weeks.
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
    // Break weeks are SKIPPED — no row created, no week-number gap.
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

        // Pre-load all calendar events once so the loop does not query per week.
        $allEvents = AcademicCalendarEvent::where('academic_calendar_id', $syllabus->academic_calendar_id)
            ->orderBy('date')
            ->get();

        $breakDates = $allEvents->where('type', 'break')
            ->map(fn ($e) => Carbon::parse($e->date)->startOfDay());

        // Exam labels assigned in encounter order across weeks.
        $examTermLabels = ['1st Term', '2nd Term', 'Final Term'];
        $examsSeen      = 0;

        $start  = Carbon::parse($calendar->start_date)->startOfDay();
        $end    = Carbon::parse($calendar->end_date)->startOfDay();
        $weekNo = 1;
        $cursor = $start->copy();

        $totalCreated = DB::transaction(function () use (
            $syllabus, $hasLEC, $hasLAB, $allEvents, $breakDates,
            $examTermLabels, &$examsSeen, &$weekNo, $start, $end, $cursor
        ) {
            while ($cursor->lte($end)) {
                $weekStart = $cursor->copy();
                $weekEnd   = $cursor->copy()->addDays(6);

                if ($weekEnd->gt($end)) {
                    $weekEnd = $end->copy();
                }

                // Skip break weeks — institution closed, no coverage row needed.
                $isBreak = $breakDates->contains(fn ($d) => $d->between($weekStart, $weekEnd));
                if ($isBreak) {
                    $cursor = $weekEnd->copy()->addDay();
                    continue;
                }

                $syllabusWeek = SyllabusWeek::create([
                    'syllabus_id'  => $syllabus->id,
                    'week_no'      => $weekNo,
                    'start_date'   => $weekStart->toDateString(),
                    'end_date'     => $weekEnd->toDateString(),
                    'is_exam_week' => false,
                ]);

                // Determine if this week has a locking event and write the
                // assessment_task label immediately on creation (Issue #5 fix:
                // label-writing belongs here, not in WeekLockService reads).
                $eventsThisWeek = $allEvents->filter(
                    fn ($e) => Carbon::parse($e->date)->between($weekStart, $weekEnd)
                );

                $lockingEvent = $eventsThisWeek->first(
                    fn ($e) => in_array($e->type, ['exam', 'non_teaching'], true)
                );

                $lecTask = '';
                $labTask = '';

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

                $weekNo++;
                $cursor = $weekEnd->copy()->addDay();
            }

            return $weekNo - 1;
        });

        Log::info('[WeekGenerationService] weeks created', [
            'syllabusId' => $syllabus->id,
            'total'      => $totalCreated,
        ]);

        return $totalCreated > 0;
    }
}
