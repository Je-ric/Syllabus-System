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
        return view('AcademicCalendar.form');
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

        //
        return redirect()->route('academic.calendar.events.index', $sem1->academic_year)
            ->with('success', 'Academic year created successfully. You can now add events.');
    }

    public function edit(string $academicYear)
    {
        $semesters = AcademicCalendar::where('academic_year', $academicYear)->get();

        if ($semesters->isEmpty()) {
            return redirect()->route('academic.calendars.index')
                ->with('error', 'Academic year not found.');
        }

        // Pass semesters collection and academic year
        return view('AcademicCalendar.form', [
            'semesters' => $semesters,
            'academicYear' => $academicYear,
            'isEdit' => true
        ]);
    }


    public function update(Request $request, string $academicYear)
    {
        $semesters = AcademicCalendar::where('academic_year', $academicYear)->get();

        if ($semesters->isEmpty()) {
            return redirect()->route('academic.calendars.index')
                ->with('error', 'Academic year not found.');
        }

        $request->validate([
            'academic_year' => 'required|string|unique:academic_calendars,academic_year,' . $academicYear . ',academic_year',
            'start_date_1' => 'required|date',
            'end_date_1' => 'required|date|after_or_equal:start_date_1',
            'start_date_2' => 'required|date',
            'end_date_2' => 'required|date|after_or_equal:start_date_2',
        ]);

        // Update 1st semester
        $sem1 = $semesters->where('semester', '1st')->first();
        $sem1->update([
            'academic_year' => $request->academic_year,
            'start_date' => $request->start_date_1,
            'end_date' => $request->end_date_1,
        ]);

        // Update 2nd semester
        $sem2 = $semesters->where('semester', '2nd')->first();
        $sem2->update([
            'academic_year' => $request->academic_year,
            'start_date' => $request->start_date_2,
            'end_date' => $request->end_date_2,
        ]);

        return redirect()->route('academic.calendars.index')
            ->with('success', 'Academic year updated successfully.');
    }



    public function destroy($academic_year)
    {
        AcademicCalendar::where('academic_year', $academic_year)->delete();

        return redirect()->route('academic.calendars.index')
            ->with('success', 'Academic year and its semesters deleted successfully.');
    }
}
