<?php

namespace App\Http\Controllers;

use App\Models\AcademicCalendar;
use App\Models\AcademicCalendarEvent;
use Illuminate\Http\Request;

class AcademicCalendarEventController extends Controller
{
    public function index($academicYear)
    {
        // Load both semesters for this academic year
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
            'type' => 'required|in:holiday,exam,break,other',
            'name' => 'required|string',
            'date' => 'required|date|after_or_equal:' . $semester->start_date . '|before_or_equal:' . $semester->end_date,
        ]);

        $semester->events()->create($request->all());

        return redirect()->route('academic.calendar.events.index', $semester->academic_year)
                            ->with('success', 'Event added successfully.');
    }
}
