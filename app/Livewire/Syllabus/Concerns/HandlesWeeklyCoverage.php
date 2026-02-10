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

        $validTypes = ['first_term', 'second_term', 'final_term'];
        if (!in_array($type, $validTypes, true)) {
            return;
        }

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
    }

    private function refreshWeeklyCoverage(): void
    {
        if (!$this->syllabus || !$this->syllabus->academic_calendar_id) {
            return;
        }

        $this->syllabus->loadMissing('academicCalendar.events');
        $this->ensureWeeksGenerated();
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
            $weekEnd = $cursor->copy()->addDays(6); // Assuming a 5-day week (Mon-Fri)
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
    }

    private function loadWeeks(): void
    {
        $this->syllabusWeeks = SyllabusWeek::where('syllabus_id', $this->syllabus->id)
            ->orderBy('week_no')
            ->get();
    }

    private function syncActiveWeekTab(): void
    {
        if ($this->activeWeekTab) {
            return;
        }

        $firstWeek = $this->syllabusWeeks->first();
        if ($firstWeek) {
            $this->activeWeekTab = 'week_' . $firstWeek->week_no;
        }
    }

    private function loadExamWeeks(): void
    {
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
        $calendarId = $this->syllabus->academic_calendar_id;
        if (!$calendarId) {
            $this->weekEvents = [];
            return;
        }

        $events = AcademicCalendarEvent::where('academic_calendar_id', $calendarId)
            ->orderBy('date')
            ->get();

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
