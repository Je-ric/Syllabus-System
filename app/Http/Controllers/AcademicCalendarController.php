<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveAcademicCalendarRequest;
use App\Models\AcademicCalendar;
use Illuminate\Support\Facades\DB;
// use App\Models\AcademicCalendarEvent;

class AcademicCalendarController extends Controller
{
    public function index()
    {
        $calendars = AcademicCalendar::orderBy('academic_year', 'desc')
                                        ->with('events')
                                        ->get();
        return view('AcademicCalendar.index', compact('calendars'));
    }

    public function create()
    {
        return view('AcademicCalendar.form');
    }

    // create 2 semesters for a new academic year
    public function store(SaveAcademicCalendarRequest $request)
    {
        if ($request->isPrecognitive()) {
            return response()->noContent();
        }

        DB::beginTransaction();

        try {
            $validated = $request->validated();

            // Create 1st semester
            $sem1 = AcademicCalendar::create([
                'academic_year' => $validated['academic_year'],
                'semester' => '1st',
                'start_date' => $validated['start_date_1'],
                'end_date' => $validated['end_date_1'],
            ]);

            // Create 2nd semester
            AcademicCalendar::create([
                'academic_year' => $validated['academic_year'],
                'semester' => '2nd',
                'start_date' => $validated['start_date_2'],
                'end_date' => $validated['end_date_2'],
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()
                ->withErrors(['error' => 'Failed to create academic year. Please try again.'])
                ->withInput();
        }

        //
        return redirect()->route('academic.calendar.events.index', $sem1->academic_year)
            ->with('toast', [
                'message' => 'Academic year created successfully. You can now add events.',
                'type' => 'success'
            ]);

    }

    public function edit(string $academicYear)
    {
        $semesters = AcademicCalendar::where('academic_year', $academicYear)
                                        ->with('events')
                                        ->get();

        if ($semesters->isEmpty()) {
            return redirect()->route('academic.calendars.index')
                ->with('toast', [
                    'message' => 'Academic year not found.',
                    'type' => 'error'
                ]);
        }

        // Check if any semester has events
        $hasEvents = $semesters->flatMap->events->isNotEmpty();
        $firstSemester = $semesters->firstWhere('semester', '1st');
        $secondSemester = $semesters->firstWhere('semester', '2nd');

        // Pass semesters collection and academic year
        return view('AcademicCalendar.form', [
            'semesters' => $semesters,
            'academicYear' => $academicYear,
            'isEdit' => true,
            'hasEvents' => $hasEvents,
            'originalValues' => [
                'academic_year' => $academicYear,
                'start_date_1' => optional($firstSemester)->start_date,
                'end_date_1' => optional($firstSemester)->end_date,
                'start_date_2' => optional($secondSemester)->start_date,
                'end_date_2' => optional($secondSemester)->end_date,
            ],
        ]);
    }


    public function update(SaveAcademicCalendarRequest $request, string $academicYear)
    {
        $semesters = AcademicCalendar::where('academic_year', $academicYear)->get();

        if ($semesters->isEmpty()) {
            return redirect()->route('academic.calendars.index')
                ->with('toast', [
                    'message' => 'Academic year not found.',
                    'type' => 'error'
                ]);
        }

        if ($request->isPrecognitive()) {
            return response()->noContent();
        }

        DB::beginTransaction();

        try {
            $validated = $request->validated();

            // Update 1st semester
            $sem1 = $semesters->where('semester', '1st')->first();
            $sem1->update([
                'academic_year' => $validated['academic_year'],
                'start_date' => $validated['start_date_1'],
                'end_date' => $validated['end_date_1'],
            ]);

            // Update 2nd semester
            $sem2 = $semesters->where('semester', '2nd')->first();
            $sem2->update([
                'academic_year' => $validated['academic_year'],
                'start_date' => $validated['start_date_2'],
                'end_date' => $validated['end_date_2'],
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()
                ->withErrors(['error' => 'Failed to update academic year. Please try again.'])
                ->withInput();
        }

        return redirect()->route('academic.calendars.index')
            ->with('toast', [
                'message' => 'Academic year updated successfully.',
                'type' => 'success'
            ]);
    }


    public function destroy($academic_year)
    {
        DB::beginTransaction();

        try {
            AcademicCalendar::where('academic_year', $academic_year)->delete();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->route('academic.calendars.index')
                ->with('toast', [
                    'message' => 'Failed to delete academic year.',
                    'type' => 'error'
                ]);
        }

        return redirect()->route('academic.calendars.index')
            ->with('toast', [
                'message' => 'Academic year deleted successfully.',
                'type' => 'success'
            ]);
    }
}
