<?php

namespace App\Livewire\Syllabus\Concerns;

use App\Models\AcademicCalendarEvent;
use App\Models\SyllabusWeek;
use Carbon\Carbon;
use Illuminate\Support\Collection;

trait HandlesWeeklyCoverage
{
    /** @var Collection<int, SyllabusWeek> */
    public Collection $syllabusWeeks;
    public array $weekEvents = [];
    public array $examWeeks = [];
    public ?string $activeWeekTab = null;
    // ? is for nullable, it means that the variable can be either a string or null

    public function bootHandlesWeeklyCoverage(): void
    {
        $this->syllabusWeeks = collect();
    }

    public function markExamWeek(int $weekNo): void
    {
        $this->assignExamWeek('final_term', $weekNo);
    }

    public function assignExamWeek(string $type, int $weekNo): void
    {
        if (!$this->syllabus) {
            return;
        }

        // Validate exam type
        // Only allow specific exam types to be assigned
        $validTypes = ['first_term', 'second_term', 'final_term'];
        if (!in_array($type, $validTypes, true)) {
            return;
        }

        // Find the week to assign the exam type to based on the week number
        $week = SyllabusWeek::where('syllabus_id', $this->syllabus->id)
            ->where('week_no', $weekNo)
            ->first();

        if (!$week) {
            return;
        }

        // Clear existing assignment for this exam type
        SyllabusWeek::where('syllabus_id', $this->syllabus->id)
            ->where('exam_type', $type)
            ->update([
                'exam_type' => null,
                'is_exam_week' => false,
            ]);

        $week->update([
            'exam_type' => $type,
            'is_exam_week' => true,
        ]);

        $this->loadWeeks();
        $this->loadWeekEvents();
        $this->loadExamWeeks();
        $this->markStepSaved('weekly_coverage');
    }

    public function clearExamWeek(string $type): void
    {
        if (!$this->syllabus) {
            return;
        }

        $validTypes = ['first_term', 'second_term', 'final_term'];
        if (!in_array($type, $validTypes, true)) {
            return;
        }

        SyllabusWeek::where('syllabus_id', $this->syllabus->id)
            ->where('exam_type', $type)
            ->update([
                'exam_type' => null,
                'is_exam_week' => false,
            ]);

        $this->loadWeeks();
        $this->loadWeekEvents();
        $this->loadExamWeeks();
        $this->markStepSaved('weekly_coverage');
    }

    private function refreshWeeklyCoverage(bool $generate = false): void
    {

        // If syllabus or academic calendar is not set, clear state and bail
        if (!$this->syllabus || !$this->syllabus->academic_calendar_id) {
            $this->syllabusWeeks = collect();
            $this->weekEvents = [];
            $this->examWeeks = [];
            $this->activeWeekTab = null;
            return;
        }

        // Load the academic calendar with its events to ensure we have the latest data
        $this->syllabus->loadMissing('academicCalendar.events');
        if ($generate) {
            $this->ensureWeeksGenerated();
        }
        $this->loadWeeks();
        $this->loadWeekEvents();
        $this->loadExamWeeks();
        $this->syncActiveWeekTab();
    }

    private function ensureWeeksGenerated(): void
    {
        // Check if weeks already exist for this syllabus
        $existing = SyllabusWeek::where('syllabus_id', $this->syllabus->id)->exists();
        if ($existing) {
            return;
        }

        // Generate weeks based on the academic calendar
        $calendar = $this->syllabus->academicCalendar;
        if (!$calendar || !$calendar->start_date || !$calendar->end_date) {
            return;
        }

        // Generate weeks from calendar start to end date
        $start = Carbon::parse($calendar->start_date)->startOfDay();
        $end = Carbon::parse($calendar->end_date)->startOfDay();

        $weekNo = 1;
        $cursor = $start->copy(); // start from the calendar's start date
        // lte is less than or equal to
        // copy is used to avoid modifying the original $start variable
        // gt is greater than
        while ($cursor->lte($end)) {
            $weekStart = $cursor->copy();
            $weekEnd = $cursor->copy()->addDays(6); // 7-day week (Mon-Sunday)
            if ($weekEnd->gt($end)) {
                $weekEnd = $end->copy();
            }

            SyllabusWeek::create([
                'syllabus_id' => $this->syllabus->id,
                'week_no' => $weekNo,
                'start_date' => $weekStart->toDateString(),
                'end_date' => $weekEnd->toDateString(),
                'is_exam_week' => false,
            ]);

            $weekNo++;
            $cursor = $weekEnd->copy()->addDay();
        }

        // example:
        // calendar start: 2024-09-01 (Sunday)
        // calendar end: 2024-09-30 (Monday)
        // generated weeks:
        // week 1: 2024-09-01 to 2024-09-07
        // week 2: 2024-09-08 to 2024-09-14
        // week 3: 2024-09-15 to 2024-09-21
        // week 4: 2024-09-22 to 2024-09-28
        // week 5: 2024-09-29 to 2024-09-30
    }

    private function loadWeeks(): void
    {
        // Reload weeks from the database to get the latest data after any changes
        $this->syllabusWeeks = SyllabusWeek::where('syllabus_id', $this->syllabus->id)
            ->orderBy('week_no')
            ->get();
    }

    private function syncActiveWeekTab(): void
    {

        if ($this->activeWeekTab) {
            return;
        }
        // If no active tab is set, default to the first week
        $firstWeek = $this->syllabusWeeks->first();
        if ($firstWeek) {
            $this->activeWeekTab = 'week_' . $firstWeek->week_no;
        }
    }

    private function loadExamWeeks(): void
    {
        // for each week, if exam_type is not null,
        // add it to the examWeeks array with the exam type as the key and the week number as the value
        $examWeeks = [];
        foreach ($this->syllabusWeeks as $week) {
            if ($week->exam_type) {
                $examWeeks[$week->exam_type] = $week->week_no;
            }
        }
        $this->examWeeks = $examWeeks;
    }

    private function loadWeekEvents(): void
    {
        // If no academic calendar is associated, there are no events to load
        $calendarId = $this->syllabus->academic_calendar_id;
        if (!$calendarId) {
            $this->weekEvents = [];
            return;
        }

        // Load all events for the academic calendar and group them by week
        $events = AcademicCalendarEvent::where('academic_calendar_id', $calendarId)
            ->orderBy('date')
            ->get();

        // foreach weeks, filter events that fall within the week's start and end date, and assign them to the weekEvents array with the week number as the key
        // example:
        // week 1: 2024-09-01 to 2024-09-07
        // events: 2024-09-02, 2024-09-09, 2024-09-15
        // weekEvents[1] = [2024-09-02]
        // weekEvents[2] = [2024-09-09]
        // weekEvents[3] = [2024-09-15]
        $weekEvents = [];
        foreach ($this->syllabusWeeks as $week) {
            $weekEvents[$week->week_no] = $events->filter(function ($event) use ($week) {
                $date = Carbon::parse($event->date);
                return $date->between(
                    Carbon::parse($week->start_date),
                    Carbon::parse($week->end_date)
                );
            })->values();
        }

        $this->weekEvents = $weekEvents;
    }
}
