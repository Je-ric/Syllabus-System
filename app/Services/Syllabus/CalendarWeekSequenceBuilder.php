<?php

namespace App\Services\Syllabus;

use App\Models\AcademicCalendarEvent;
use Carbon\Carbon;

// Pure utility: converts an academic calendar's date range + break events into
// an ordered sequence of teachable week slots.
//
// Both WeekGenerationService and WeekReconciliationService need identical
// date arithmetic — this class is the single source of that truth.
//
// Output shape per slot:
// [
//   'start'        => 'YYYY-MM-DD',
//   'end'          => 'YYYY-MM-DD',
//   'lockingEvent' => AcademicCalendarEvent|null,  // first exam/non_teaching event in the week
// ]
//
// Rules (must stay in sync with the original WeekGenerationService loop):
//   - Weeks are 7-day chunks starting from calendar start_date.
//   - The final chunk is capped at calendar end_date (may be shorter than 7 days).
//   - Weeks where a 'break' event falls within the range are entirely skipped.
//   - The returned array is 0-indexed; week_no = index + 1.
class CalendarWeekSequenceBuilder
{
    // @param  \App\Models\AcademicCalendar  $calendar  Must have start_date and end_date.
    // @param  int                           $calendarId  Used to load calendar events.
    // @return array<int, array{start: string, end: string, lockingEvent: AcademicCalendarEvent|null}>
    public function build($calendar, int $calendarId): array
    {
        $allEvents = AcademicCalendarEvent::where('academic_calendar_id', $calendarId)
            ->orderBy('date')
            ->get();

        $breakDates = $allEvents->where('type', 'break')
            ->map(fn ($e) => Carbon::parse($e->date)->startOfDay());

        $start  = Carbon::parse($calendar->start_date)->startOfDay();
        $end    = Carbon::parse($calendar->end_date)->startOfDay();
        $cursor = $start->copy();

        $sequence = [];

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

            $eventsThisWeek = $allEvents->filter(
                fn ($e) => Carbon::parse($e->date)->between($weekStart, $weekEnd)
            );

            $lockingEvent = $eventsThisWeek->first(
                fn ($e) => in_array($e->type, ['exam', 'non_teaching'], true)
            );

            $sequence[] = [
                'start'        => $weekStart->toDateString(),
                'end'          => $weekEnd->toDateString(),
                'lockingEvent' => $lockingEvent,
            ];

            $cursor = $weekEnd->copy()->addDay();
        }

        return $sequence;
    }
}
