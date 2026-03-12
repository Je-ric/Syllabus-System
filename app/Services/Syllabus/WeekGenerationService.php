<?php

namespace App\Services\Syllabus;

use App\Models\AcademicCalendarEvent;
use App\Models\OnlineMaterial;
use App\Models\Reference;
use App\Models\Syllabus;
use App\Models\SyllabusWeek;
use App\Models\WeekContent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

/**
 * WeekGenerationService
 *
 * Owns the full lifecycle of SyllabusWeek rows for a syllabus:
 *   generate()       — first-time creation (idempotent guard included).
 *   regenerate()     — wipe all existing rows then recreate from scratch.
 *   deleteAllWeeks() — delete weeks + dependent rows (used by regenerate).
 *
 * Break weeks are SKIPPED (no row created, week numbers stay sequential).
 * Exam / non-teaching weeks ARE created; WeekLockService labels them later.
 */
class WeekGenerationService
{
    /**
     * Generate weeks for the first time.
     * Idempotent — exits cleanly if rows already exist.
     *
     * @param  Syllabus   $syllabus
     * @param  array      $courseComponents  Keyed 'LEC' / 'LAB'.
     * @param  Component  $livewire          For dispatching error toasts.
     * @return bool  true = rows now exist (created or already present).
     */
    public function generate(Syllabus $syllabus, array $courseComponents, Component $livewire): bool
    {
        if (! $syllabus->academic_calendar_id) {
            $livewire->dispatch('lw-toast', type: 'error', message: 'Select an academic calendar first.');
            return false;
        }

        return $this->createWeekRows($syllabus, $courseComponents, $livewire);
    }

    /**
     * Delete every existing week then regenerate fresh from the calendar.
     */
    public function regenerate(Syllabus $syllabus, array $courseComponents, Component $livewire): bool
    {
        $this->deleteAllWeeks($syllabus);
        return $this->createWeekRows($syllabus, $courseComponents, $livewire);
    }

    /**
     * Hard-delete all SyllabusWeek rows and their dependent data for a syllabus.
     */
    public function deleteAllWeeks(Syllabus $syllabus): void
    {
        $weekIds = SyllabusWeek::where('syllabus_id', $syllabus->id)->pluck('id')->all();

        if (empty($weekIds)) {
            return;
        }

        WeekContent::whereIn('syllabus_week_id', $weekIds)->delete();
        Reference::where('syllabus_id', $syllabus->id)->whereIn('syllabus_week_id', $weekIds)->delete();
        OnlineMaterial::where('syllabus_id', $syllabus->id)->whereIn('syllabus_week_id', $weekIds)->delete();
        SyllabusWeek::whereIn('id', $weekIds)->delete();
    }

    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Core week-row creation loop.
     * Idempotent: exits immediately if rows already exist for this syllabus.
     *
     * Break weeks are SKIPPED — no row created, no week-number gap.
     * Exam / non-teaching weeks are created; WeekLockService labels them later.
     *
     * @return bool  true when at least one week was created (or already existed).
     */
    private function createWeekRows(Syllabus $syllabus, array $courseComponents, Component $livewire): bool
    {
        if (SyllabusWeek::where('syllabus_id', $syllabus->id)->exists()) {
            return true;
        }

        $calendar = $syllabus->academicCalendar;
        if (! $calendar || ! $calendar->start_date || ! $calendar->end_date) {
            $livewire->dispatch('lw-toast', type: 'error', message: 'Academic calendar has no start/end date.');
            return false;
        }

        $hasLEC = isset($courseComponents['LEC']);
        $hasLAB = isset($courseComponents['LAB']);

        if (! $hasLEC && ! $hasLAB) {
            $livewire->dispatch('lw-toast', type: 'error', message: 'Complete the Course Components step first.');
            return false;
        }

        // Pre-load break-event dates so we can skip break weeks in the loop.
        $breakDates = AcademicCalendarEvent::where('academic_calendar_id', $syllabus->academic_calendar_id)
            ->where('type', 'break')
            ->orderBy('date')
            ->pluck('date')
            ->map(fn ($d) => Carbon::parse($d)->startOfDay());

        $start  = Carbon::parse($calendar->start_date)->startOfDay();
        $end    = Carbon::parse($calendar->end_date)->startOfDay();
        $weekNo = 1;
        $cursor = $start->copy();

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

            if ($hasLEC) {
                WeekContent::create([
                    'syllabus_week_id'  => $syllabusWeek->id,
                    'component_type'    => 'LEC',
                    'learning_outcomes' => '',
                    'assessment_task'   => '',
                    'topics'            => '',
                    'tla'               => '',
                ]);
            }

            if ($hasLAB) {
                WeekContent::create([
                    'syllabus_week_id'  => $syllabusWeek->id,
                    'component_type'    => 'LAB',
                    'learning_outcomes' => '',
                    'assessment_task'   => '',
                    'topics'            => '',
                    'tla'               => '',
                ]);
            }

            $weekNo++;
            $cursor = $weekEnd->copy()->addDay();
        }

        Log::info('[WeekGenerationService] weeks created', [
            'syllabusId' => $syllabus->id,
            'total'      => $weekNo - 1,
        ]);

        return ($weekNo - 1) > 0;
    }
}