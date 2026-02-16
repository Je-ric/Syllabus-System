<?php

namespace App\Http\Controllers;

use App\Models\AcademicCalendar;
use App\Models\AcademicCalendarEvent;
// use Illuminate\Http\Request;

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

    // public function store(Request $request, AcademicCalendar $semester)
    // {
    //     $request->validate([
    //         'type' => 'required|in:holiday,exam,break,other',
    //         'name' => 'required|string',
    //         'date' => [
    //             'required',
    //             'date',
    //             'after_or_equal:' . $semester->start_date,
    //             'before_or_equal:' . $semester->end_date,
    //             // Custom unique validation
    //             function($attribute, $value, $fail) use ($semester) {
    //                 if ($semester->events()->where('date', $value)->exists()) {
    //                     $fail('An event for this date already exists in this semester.');
    //                 }
    //             }
    //         ],
    //     ]);

    //     $semester->events()->create($request->all());

    //     return redirect()->route('academic.calendar.events.index', $semester->academic_year)
    //                         ->with('success', 'Event added successfully.');
    // }

    // public function update(Request $request, AcademicCalendarEvent $event)
    // {
    //     $semester = $event->calendar;

    //     $request->validate([
    //         'type' => 'required|in:holiday,exam,break,other',
    //         'name' => 'required|string',
    //         'date' => [
    //             'required',
    //             'date',
    //             'after_or_equal:' . $semester->start_date,
    //             'before_or_equal:' . $semester->end_date,
    //             // Custom unique validation (exclude current event)
    //             function($attribute, $value, $fail) use ($semester, $event) {
    //                 if ($semester->events()->where('date', $value)->where('id', '!=', $event->id)->exists()) {
    //                     $fail('An event for this date already exists in this semester.');
    //                 }
    //             }
    //         ],
    //     ]);

    //     $event->update($request->only(['type', 'name', 'date']));

    //     return redirect()->route('academic.calendar.events.index', $semester->academic_year)
    //                         ->with('success', 'Event updated successfully.');
    // }

    // public function destroy(AcademicCalendarEvent $event)
    // {
    //     $academicYear = $event->calendar->academic_year;
    //     $event->delete();

    //     return redirect()->route('academic.calendar.events.index', $academicYear)
    //                         ->with('success', 'Event deleted successfully.');
    // }

}
