<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\AcademicCalendar;
use App\Models\AcademicCalendarEvent;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AcademicCalendarEventController extends Controller
{
    public function index(string $academicYear)
    {
        $semesters = AcademicCalendar::with('events')
            ->where('academic_year', $academicYear)
            ->orderBy('semester')
            ->get();

        if ($semesters->isEmpty()) {
            return redirect()->route('academic.calendars.index')
                ->with('toast', ['message' => 'Academic year not found.', 'type' => 'error']);
        }

        return view('Academic.AcademicCalendarEvent.index', compact('semesters', 'academicYear'));
    }

    // store / update / destroy stay as plain form POSTs.
    // Real-time validation on the event forms is done with Livewire (AcademicCalendarEventForm).

    public function destroy(AcademicCalendarEvent $event)
    {
        $event->loadMissing('calendar');
        $academicYear = $event->calendar->academic_year;
        $semester     = $event->calendar->semester;
        $eventId      = $event->id;
        $eventType    = $event->type;
        $eventName    = $event->name;
        $eventDate    = $event->date;

        $event->delete();

        AuditLog::record(
            action: 'deleted',
            module: 'Academic Calendar Event',
            referenceId: $eventId,
            description: "Deleted {$eventType} event '{$eventName}' on {$eventDate} for {$academicYear} {$semester} semester."
        );

        return redirect()->route('academic.calendar.events.index', $academicYear)
            ->with('toast', ['message' => 'Event deleted successfully.', 'type' => 'success']);
    }
}
