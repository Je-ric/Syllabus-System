<?php

namespace App\Http\Controllers;

use App\Models\AcademicCalendar;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

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

    public function edit(string $academicYear)
    {
        $semesters = AcademicCalendar::where('academic_year', $academicYear)
            ->with('events')
            ->get();

        if ($semesters->isEmpty()) {
            return redirect()->route('academic.calendars.index')
                ->with('toast', ['message' => 'Academic year not found.', 'type' => 'error']);
        }

        $hasEvents      = $semesters->flatMap->events->isNotEmpty();
        $firstSemester  = $semesters->firstWhere('semester', '1st');
        $secondSemester = $semesters->firstWhere('semester', '2nd');

        return view('AcademicCalendar.form', [
            'semesters'      => $semesters,
            'academicYear'   => $academicYear,
            'isEdit'         => true,
            'hasEvents'      => $hasEvents,
            'originalValues' => [
                'academic_year' => $academicYear,
                'start_date_1'  => optional($firstSemester)->start_date,
                'end_date_1'    => optional($firstSemester)->end_date,
                'start_date_2'  => optional($secondSemester)->start_date,
                'end_date_2'    => optional($secondSemester)->end_date,
            ],
        ]);
    }

    // store / update are handled by Livewire AcademicCalendarForm component.

    public function destroy(string $academic_year)
    {
        DB::beginTransaction();

        try {
            AcademicCalendar::where('academic_year', $academic_year)->delete();

            AuditLog::record(
                action: 'deleted',
                module: 'Academic Calendar',
                referenceId: null,
                description: "Deleted academic calendar for {$academic_year}."
            );

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->route('academic.calendars.index')
                ->with('toast', ['message' => 'Failed to delete academic year.', 'type' => 'error']);
        }

        return redirect()->route('academic.calendars.index')
            ->with('toast', ['message' => 'Academic year deleted successfully.', 'type' => 'success']);
    }
}