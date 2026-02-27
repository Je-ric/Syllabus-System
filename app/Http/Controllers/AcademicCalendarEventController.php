<?php

namespace App\Http\Controllers;

use App\Models\AcademicCalendar;
use App\Models\AcademicCalendarEvent;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AcademicCalendarEventController extends Controller
{
    public function index($academicYear)
    {
        // Retain only the index method for loading the view.
        $semesters = AcademicCalendar::with('events')
                        ->where('academic_year', $academicYear)
                        ->orderBy('semester')
                        ->get();

        if ($semesters->isEmpty()) {
            return redirect()->route('academic.calendars.index')
                                ->with('error', 'Academic year not found.');
        }

        return view('AcademicCalendarEvent.index', compact('semesters', 'academicYear'));
    }

    public function store(Request $request, AcademicCalendar $semester)
    {
        $request->validate([
            'type' => ['required', Rule::in(AcademicCalendarEvent::TYPES)],
            'name' => 'required|string',
            'date' => [
                'required',
                'date',
                'after_or_equal:' . $semester->start_date,
                'before_or_equal:' . $semester->end_date,
                // Custom unique validation
                function($attribute, $value, $fail) use ($semester) {
                    if ($semester->events()->where('date', $value)->exists()) {
                        $fail('An event for this date already exists in this semester.');
                    }
                }
            ],
        ]);

        $event = $semester->events()->create($request->all());

        // LOGS
        AuditLog::record(
            action: 'created',
            module: 'Academic Calendar Event',
            referenceId: $event->id,
            description: "Created {$event->type} event '{$event->name}' on {$event->date} for {$semester->academic_year} {$semester->semester} semester."
        );

        return redirect()->route('academic.calendar.events.index', $semester->academic_year)
                        ->with('toast', [
                            'message' => 'Event added successfully.',
                            'type' => 'success'
                        ]);
    }

    public function update(Request $request, AcademicCalendarEvent $event)
    {
        $semester = $event->calendar;

        $request->validate([
            'type' => ['required', Rule::in(AcademicCalendarEvent::TYPES)],
            'name' => 'required|string',
            'date' => [
                'required',
                'date',
                'after_or_equal:' . $semester->start_date,
                'before_or_equal:' . $semester->end_date,
                // Custom unique validation (exclude current event)
                function($attribute, $value, $fail) use ($semester, $event) {
                    if ($semester->events()->where('date', $value)->where('id', '!=', $event->id)->exists()) {
                        $fail('An event for this date already exists in this semester.');
                    }
                }
            ],
        ]);

        $event->update($request->only(['type', 'name', 'date']));

        // LOGS
        AuditLog::record(
            action: 'updated',
            module: 'Academic Calendar Event',
            referenceId: $event->id,
            description: "Updated {$event->type} event '{$event->name}' on {$event->date} for {$semester->academic_year} {$semester->semester} semester."
        );

        return redirect()->route('academic.calendar.events.index', $semester->academic_year)
                        ->with('toast', [
                    'message' => 'Event updated successfully.',
                    'type' => 'success'
                ]);
    }

    public function destroy(AcademicCalendarEvent $event)
    {
        $academicYear = $event->calendar->academic_year;
        $semester = $event->calendar->semester;
        $eventId = $event->id;
        $eventType = $event->type;
        $eventName = $event->name;
        $eventDate = $event->date;
        $event->delete();

        // LOGS
        AuditLog::record(
            action: 'deleted',
            module: 'Academic Calendar Event',
            referenceId: $eventId,
            description: "Deleted {$eventType} event '{$eventName}' on {$eventDate} for {$academicYear} {$semester} semester."
        );

        return redirect()->route('academic.calendar.events.index', $academicYear)
                        ->with('toast', [
                    'message' => 'Event deleted successfully.',
                    'type' => 'success'
                ]);
    }

}
