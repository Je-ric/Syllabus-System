<?php

namespace App\Services\Syllabus\Weeks;

use App\Models\AcademicCalendarEvent;
use App\Models\Syllabus;
use Carbon\Carbon;
use Illuminate\Support\Collection;

// Reads calendar events for every SyllabusWeek and decides which weeks are
// locked (exam or non_teaching). Returns two arrays the Livewire component
// stores as reactive state:
//
//   lockedWeeks — [ weekNo => 'exam' | 'non_teaching' ]
//   weekEvents  — [ weekNo => [ ['name', 'type', 'date_display'], … ] ]
//
// This service is intentionally read-only. Exam/non-teaching labels are
// written into WeekContent rows once at generation time by WeekGenerationService,
// not on every render cycle here.
class WeekLockService
{
    // @param  Collection  $syllabusWeeks  Ordered SyllabusWeek models.
    // @return array{ lockedWeeks: array<int,string>, weekEvents: array<int,array> }
    public function computeLockedWeeks(Syllabus $syllabus, Collection $syllabusWeeks): array
    {
        $lockedWeeks = [];
        $weekEvents  = [];

        if (! $syllabus->academic_calendar_id || $syllabusWeeks->isEmpty()) {
            return compact('lockedWeeks', 'weekEvents');
        }

        $allEvents = AcademicCalendarEvent::where('academic_calendar_id', $syllabus->academic_calendar_id)
            ->orderBy('date')
            ->get();

        foreach ($syllabusWeeks as $week) {
            $weekStart = Carbon::parse($week->start_date);
            $weekEnd   = Carbon::parse($week->end_date);

            $eventsThisWeek = $allEvents->filter(
                fn ($event) => Carbon::parse($event->date)->between($weekStart, $weekEnd)
            );

            $weekEvents[$week->week_no] = $eventsThisWeek->map(fn ($event) => [
                'name'         => $event->name,
                'type'         => $event->type,
                'date_display' => Carbon::parse($event->date)->format('M d'),
            ])->values()->all();

            // First exam / non_teaching event in the week locks it.
            $lockingEvent = $eventsThisWeek->first(
                fn ($event) => in_array($event->type, ['exam', 'non_teaching'], true)
            );

            if ($lockingEvent) {
                $lockedWeeks[$week->week_no] = $lockingEvent->type;
            }
        }

        return compact('lockedWeeks', 'weekEvents');
    }
}
