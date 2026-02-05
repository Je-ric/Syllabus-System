<?php

namespace App\Http\Controllers;

use App\Models\Syllabus;
use App\Models\Course;
use App\Models\AcademicCalendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Program;

class SyllabusController extends Controller
{
    public function index()
    {
        $syllabi = Syllabus::where('prepared_by', Auth::id())
            ->with(['course.program', 'academicCalendar', 'chair', 'dean'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('Syllabus.index', compact('syllabi'));
    }

    public function create(Request $request)
    {
        $program = null;
        $groupedCourses = collect();

        if ($request->filled('program_id')) {
            $program = Program::withOrderedOutcomes()->findOrFail($request->program_id);
            $groupedCourses = $program->getCoursesGroupedByYearAndSemester();
        }

        return view('Syllabus.selectCourse', [
            'program' => $program,
            'groupedCourses' => $groupedCourses,
        ]);
    }


    public function showCourses(Request $request)
    {
        $program = null;
        $groupedCourses = collect();

        if ($request->filled('program_id')) {
            // Load the selected program with outcomes
            $program = Program::withOrderedOutcomes()->findOrFail($request->program_id);

            // Get courses grouped by year and semester
            $groupedCourses = $program->getCoursesGroupedByYearAndSemester();
        }

        return view('Syllabus.selectCourse', [
            'program' => $program,
            'groupedCourses' => $groupedCourses,
        ]);
    }

    public function showForm($courseId)
    {
        $course = Course::with('components', 'program.outcomes', 'programOutcomes')
            ->findOrFail($courseId);

        $academicCalendars = AcademicCalendar::orderBy('academic_year', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        // Get components
        $lecComponent = $course->getLecComponent();
        $labComponent = $course->getLabComponent();
        $hasLab = $course->has_lec_lab;

        $formAction = route('syllabus.store');
        $formMethod = 'POST';
        $pageTitle = 'Create Syllabus';

        return view('Syllabus.form', compact(
            'course',
            'academicCalendars',
            'lecComponent',
            'labComponent',
            'hasLab',
            'formAction',
            'formMethod',
            'pageTitle'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'academic_calendar_id' => 'required|exists:academic_calendars,id',
        ]);

        // Check for duplicate
        $existing = Syllabus::where('course_id', $validated['course_id'])
            ->where('academic_calendar_id', $validated['academic_calendar_id'])
            ->first();

        if ($existing) {
            return redirect()->route('syllabus.edit', $existing->id)
                ->with('toast', [
                    'message' => 'Syllabus for this course already exists.',
                    'type' => 'info'
                ]);
        }

        // Create syllabus
        $syllabus = Syllabus::create([
            'course_id' => $validated['course_id'],
            'academic_calendar_id' => $validated['academic_calendar_id'],
            'status' => 'draft',
            'prepared_by' => Auth::id(),
        ]);

        return redirect()->route('syllabus.edit', $syllabus->id)
            ->with('toast', [
                'message' => 'Syllabus created successfully.',
                'type' => 'success'
            ]);
    }

    public function edit(Syllabus $syllabus)
    {
        // Check if user is the preparer
        if ($syllabus->prepared_by !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $syllabus->load([
            'course.program.outcomes',
            'course.components',
            'course.programOutcomes',
            'academicCalendar'
        ]);

        $course = $syllabus->course;
        $lecComponent = $course->getLecComponent();
        $labComponent = $course->getLabComponent();
        $hasLab = $course->has_lec_lab;

        $academicCalendars = AcademicCalendar::orderBy('academic_year', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        $formAction = route('syllabus.update', $syllabus->id);
        $formMethod = 'PUT';
        $pageTitle = 'Edit Syllabus';

        return view('Syllabus.form', compact(
            'syllabus',
            'course',
            'academicCalendars',
            'lecComponent',
            'labComponent',
            'hasLab',
            'formAction',
            'formMethod',
            'pageTitle'
        ));
    }

    public function update(Syllabus $syllabus, Request $request)
    {
        // Authorization check
        if ($syllabus->prepared_by !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Check if editable
        if (!$syllabus->isEditable()) {
            return back()->with('toast', [
                'message' => 'This syllabus cannot be edited in its current status.',
                'type' => 'error'
            ]);
        }

        $validated = $request->validate([
            // Add validation for other fields as needed
        ]);

        $syllabus->update($validated);

        return redirect()->route('syllabus.show', $syllabus->id)
            ->with('toast', [
                'message' => 'Syllabus updated successfully.',
                'type' => 'success'
            ]);
    }

    public function show(Syllabus $syllabus)
    {
        $syllabus->load([
            'course.program',
            'course.components',
            'academicCalendar',
            'preparer',
            'chair',
            'dean',
            'revisions'
        ]);

        return view('Syllabus.show', compact('syllabus'));
    }


    public function destroy(Syllabus $syllabus)
    {
        // Check authorization
        if ($syllabus->prepared_by !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Only allow deletion of draft syllabi
        if ($syllabus->status !== 'draft') {
            return back()->with('toast', [
                'message' => 'Only draft syllabi can be deleted.',
                'type' => 'error'
            ]);
        }

        $syllabus->delete();

        return redirect()->route('syllabus.index')
            ->with('toast', [
                'message' => 'Syllabus deleted successfully.',
                'type' => 'success'
            ]);
    }
}
