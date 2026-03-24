<?php

namespace App\Services\Syllabus;

use App\Models\AcademicCalendarEvent;
use App\Models\Syllabus;
use App\Models\WeekContent;
use Carbon\Carbon;
use Illuminate\Support\Collection;

// Reads calendar events for every SyllabusWeek, decides which weeks are
// locked (exam or non_teaching), writes the correct assessment_task labels
// into WeekContent rows, and returns two arrays the Livewire component
// stores as reactive state:
//
//   lockedWeeks — [ weekNo => 'exam' | 'non_teaching' ]
//   weekEvents  — [ weekNo => [ ['name', 'type', 'date_display'], … ] ]
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

        // Exam labels are assigned in the order exams are encountered across weeks
        $examTermLabels = ['1st Term', '2nd Term', 'Final Term'];
        $examsSeen      = 0;

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

            // First exam / non_teaching event in the week locks it
            $lockingEvent = $eventsThisWeek->first(
                fn ($event) => in_array($event->type, ['exam', 'non_teaching'], true)
            );

            if (! $lockingEvent) {
                continue;
            }

            $lockedWeeks[$week->week_no] = $lockingEvent->type;

            // Write exam label into both LEC and LAB WeekContent rows
            if ($lockingEvent->type === 'exam') {
                $termLabel = $examTermLabels[min($examsSeen, 2)];
                $examsSeen++;

                WeekContent::where('syllabus_week_id', $week->id)
                    ->where('component_type', 'LEC')
                    ->update(['assessment_task' => $termLabel . ' Exam']);

                WeekContent::where('syllabus_week_id', $week->id)
                    ->where('component_type', 'LAB')
                    ->update(['assessment_task' => $termLabel . ' Practical Exam']);
            }

            if ($lockingEvent->type === 'non_teaching') {
                WeekContent::where('syllabus_week_id', $week->id)
                    ->update(['assessment_task' => 'Non-Teaching Week']);
            }
        }

        return compact('lockedWeeks', 'weekEvents');
    }
}
