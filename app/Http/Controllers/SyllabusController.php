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
        // Redirect to wizard instead
        return redirect()->route('syllabus.wizard', ['courseId' => $courseId]);
    }

    public function wizard(Request $request)
    {
        $syllabusId = $request->query('syllabusId');
        $courseId = $request->query('courseId');

        // At least one is required
        if (!$syllabusId && !$courseId) {
            abort(404, 'No syllabus or course specified.');
        }

        // If no existing syllabusId, check if a draft already exists
        if (!$syllabusId) {
            $existing = Syllabus::where('course_id', $courseId)
                ->where('prepared_by', Auth::id())
                ->first();

            if ($existing) {
                // Redirect to the existing syllabus wizard with info
                return redirect()->route('syllabus.wizard', ['syllabusId' => $existing->id])
                    ->with('toast', [
                        'message' => 'A syllabus for this course already exists. You can continue editing it.',
                        'type' => 'info'
                    ]);
            }

            // No existing syllabus → create a new draft
            $syllabus = Syllabus::create([
                'course_id' => $courseId,
                'prepared_by' => Auth::id(),
                'status' => 'draft',
                'current_step' => 'academic_calendar',
            ]);

            $syllabusId = $syllabus->id;
        }



        return view('Syllabus.wizard', compact('syllabusId', 'courseId'));
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

        // Check if editable
        if (!$syllabus->isEditable()) {
            return redirect()->route('syllabus.show', $syllabus->id)
                ->with('toast', [
                    'message' => 'This syllabus cannot be edited in its current status.',
                    'type' => 'error'
                ]);
        }

        // Redirect to wizard for editing (Livewire handles the editing)
        return redirect()->route('syllabus.wizard', ['syllabusId' => $syllabus->id]);
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

        // Note: Most updates are handled by Livewire wizard
        // This method is kept for compatibility but redirects to wizard
        return redirect()->route('syllabus.wizard', ['syllabusId' => $syllabus->id])
            ->with('toast', [
                'message' => 'Please use the wizard to edit the syllabus.',
                'type' => 'info'
            ]);
    }

    public function show(Syllabus $syllabus)
    {
        $syllabus->load([
            'course.program',
            'components',
            'courseOutcomes.programOutcomes',
            'academicCalendar',
            'preparer',
            'chair',
            'dean',
            'revisions'
        ]);

        return view('Syllabus.show', compact('syllabus'));
    }


    // public function destroy(Syllabus $syllabus)
    // {
    //     // Check authorization
    //     if ($syllabus->prepared_by !== Auth::id()) {
    //         abort(403, 'Unauthorized');
    //     }

    //     // Only allow deletion of draft syllabi
    //     if ($syllabus->status !== 'draft') {
    //         return back()->with('toast', [
    //             'message' => 'Only draft syllabi can be deleted.',
    //             'type' => 'error'
    //         ]);
    //     }

    //     $syllabus->delete();

    //     return redirect()->route('syllabus.index')
    //         ->with('toast', [
    //             'message' => 'Syllabus deleted successfully.',
    //             'type' => 'success'
    //         ]);
    // }
}
