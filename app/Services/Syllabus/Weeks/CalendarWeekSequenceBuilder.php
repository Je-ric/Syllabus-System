<?php

namespace App\Services\Syllabus\Weeks;

use App\Models\AcademicCalendarEvent;
use Carbon\Carbon;
use Illuminate\Support\Collection;

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
//
// Event Type Categories:
//   - SKIP (break): Week is entirely skipped, no row created
//   - LOCK (exam, non_teaching): Week is created but locked for editing
//   - REFERENCE (holiday, other): Week is created normally, fully editable
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

        $breakDates = $allEvents->where('type', AcademicCalendarEvent::TYPE_SKIP)
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
                fn ($e) => in_array($e->type, AcademicCalendarEvent::TYPE_LOCK, true)
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

    /**
     * Get detailed information about what happened during week generation.
     * Useful for logging and user feedback.
     *
     * @param  \App\Models\AcademicCalendar  $calendar
     * @param  int                           $calendarId
     * @return array{totalWeeks: int, skippedWeeks: int, lockedWeeks: int, breakEvents: Collection, lockEvents: Collection}
     */
    public function getGenerationStats($calendar, int $calendarId): array
    {
        $allEvents = AcademicCalendarEvent::where('academic_calendar_id', $calendarId)
            ->orderBy('date')
            ->get();

        $breakEvents = $allEvents->where('type', AcademicCalendarEvent::TYPE_SKIP);
        $lockEvents = $allEvents->whereIn('type', AcademicCalendarEvent::TYPE_LOCK);

        $start = Carbon::parse($calendar->start_date)->startOfDay();
        $end = Carbon::parse($calendar->end_date)->startOfDay();
        
        $totalWeeks = 0;
        $skippedWeeks = 0;
        $lockedWeeks = 0;
        $cursor = $start->copy();

        while ($cursor->lte($end)) {
            $weekStart = $cursor->copy();
            $weekEnd = $cursor->copy()->addDays(6);
            
            if ($weekEnd->gt($end)) {
                $weekEnd = $end->copy();
            }

            $hasBreak = $breakEvents->contains(
                fn ($e) => Carbon::parse($e->date)->between($weekStart, $weekEnd)
            );
            
            if ($hasBreak) {
                $skippedWeeks++;
            } else {
                $totalWeeks++;
                
                $hasLock = $lockEvents->contains(
                    fn ($e) => Carbon::parse($e->date)->between($weekStart, $weekEnd)
                );
                
                if ($hasLock) {
                    $lockedWeeks++;
                }
            }

            $cursor = $weekEnd->copy()->addDay();
        }

        return [
            'totalWeeks' => $totalWeeks,
            'skippedWeeks' => $skippedWeeks,
            'lockedWeeks' => $lockedWeeks,
            'breakEvents' => $breakEvents,
            'lockEvents' => $lockEvents,
        ];
    }
}
