<?php

namespace App\Http\Controllers;

use App\Models\AcademicCalendar;
use Illuminate\Http\Request;

class AcademicCalendarController extends Controller
{
    public function index()
    {
        $calendars = AcademicCalendar::orderBy('academic_year', 'desc')->get();
        return view('AcademicCalendar.index', compact('calendars'));
    }

    public function create()
    {
        return view('AcademicCalendar.create');
    }

    // create 2 semesters for a new academic year
    public function store(Request $request)
    {
        $request->validate([
            'academic_year' => 'required|string|unique:academic_calendars,academic_year',
            'start_date_1' => 'required|date',
            'end_date_1' => 'required|date|after_or_equal:start_date_1',
            'start_date_2' => 'required|date',
            'end_date_2' => 'required|date|after_or_equal:start_date_2',
        ]);

        // Create 1st semester
        $sem1 = AcademicCalendar::create([
            'academic_year' => $request->academic_year,
            'semester' => '1st',
            'start_date' => $request->start_date_1,
            'end_date' => $request->end_date_1,
        ]);

        // Create 2nd semester
        $sem2 = AcademicCalendar::create([
            'academic_year' => $request->academic_year,
            'semester' => '2nd',
            'start_date' => $request->start_date_2,
            'end_date' => $request->end_date_2,
        ]);

        // Redirect to event management page for this academic year (optional)
        return redirect()->route('academic.calendar.events.index', $sem1->academic_year)
                            ->with('success', 'Academic year created successfully. You can now add events.');
    }
}
