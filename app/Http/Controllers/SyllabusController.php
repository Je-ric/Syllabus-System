<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Syllabus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $programId = $request->query('program_id');
        return view('Syllabus.selectCourse', $this->buildProgramSelectionData($programId));
    }

    public function showCourses(int $programId)
    {
        return view('Syllabus.selectCourse', $this->buildProgramSelectionData($programId));
    }

    public function showForm($courseId)
    {
        return redirect()->route('syllabus.wizard', ['courseId' => $courseId]);
    }

    public function wizard(Request $request)
    {
        $syllabusId = $request->query('syllabusId');
        $courseId = $request->query('courseId');

        if (!$syllabusId && !$courseId) {
            abort(404, 'No syllabus or course specified.');
        }

        if (!$syllabusId) {
            $existing = Syllabus::where('course_id', $courseId)
                ->where('prepared_by', Auth::id())
                ->first();

            if ($existing) {
                return redirect()->route('syllabus.wizard', ['syllabusId' => $existing->id])
                    ->with('toast', [
                        'message' => 'A syllabus for this course already exists. You can continue editing it.',
                        'type' => 'info',
                    ]);
            }

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

        $existing = Syllabus::where('course_id', $validated['course_id'])
            ->where('academic_calendar_id', $validated['academic_calendar_id'])
            ->first();

        if ($existing) {
            return redirect()->route('syllabus.edit', $existing->id)
                ->with('toast', [
                    'message' => 'Syllabus for this course already exists.',
                    'type' => 'info',
                ]);
        }

        $syllabus = Syllabus::create([
            'course_id' => $validated['course_id'],
            'academic_calendar_id' => $validated['academic_calendar_id'],
            'status' => 'draft',
            'prepared_by' => Auth::id(),
        ]);

        return redirect()->route('syllabus.edit', $syllabus->id)
            ->with('toast', [
                'message' => 'Syllabus created successfully.',
                'type' => 'success',
            ]);
    }

    public function edit(Syllabus $syllabus)
    {
        if ($syllabus->prepared_by !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if (!$syllabus->isEditable()) {
            return redirect()->route('syllabus.show', $syllabus->id)
                ->with('toast', [
                    'message' => 'This syllabus cannot be edited in its current status.',
                    'type' => 'error',
                ]);
        }

        return redirect()->route('syllabus.wizard', ['syllabusId' => $syllabus->id]);
    }

    public function update(Syllabus $syllabus, Request $request)
    {
        if ($syllabus->prepared_by !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if (!$syllabus->isEditable()) {
            return back()->with('toast', [
                'message' => 'This syllabus cannot be edited in its current status.',
                'type' => 'error',
            ]);
        }

        return redirect()->route('syllabus.wizard', ['syllabusId' => $syllabus->id])
            ->with('toast', [
                'message' => 'Please use the wizard to edit the syllabus.',
                'type' => 'info',
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
            'revisions',
        ]);

        return view('Syllabus.show', compact('syllabus'));
    }

    public function preview(Syllabus $syllabus)
    {
        $syllabus->load([
            'course.program.peos',
            'course.program.outcomes',
            'course.program.departments.college.goals',
        ]);

        $program = $syllabus->course->program;
        $department = $program->departments->first();
        $college = $department?->college;

        $collegeName = $college?->name ?? 'College';
        $collegeGoals = $college?->goals?->sortBy('college_goals_code') ?? collect();
        $peos = $program->peos?->sortBy('peo_code') ?? collect();
        $pos = $program->outcomes?->sortBy('po_code') ?? collect();

        return view('Syllabus.preview', compact('syllabus', 'collegeName', 'collegeGoals', 'peos', 'pos'));
    }

    private function buildProgramSelectionData($programId = null): array
    {
        $program = null;
        $groupedCourses = collect();

        if ($programId) {
            $program = Program::withOrderedOutcomes()->findOrFail($programId);
            $groupedCourses = $program->getCoursesGroupedByYearAndSemester();
        }

        return [
            'program' => $program,
            'groupedCourses' => $groupedCourses,
        ];
    }
}
