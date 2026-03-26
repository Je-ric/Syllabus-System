<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Program;
use App\Models\UserAssignment;
use App\Services\CourseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;


class CourseController extends Controller
{
    public function __construct(
        protected CourseService $courseService
    ) {
    }

    public function index(Request $request)
    {
        $program = null;
        $groupedCourses = collect();

        if ($request->filled('program_id')) {
            $program = Program::withOrderedOutcomes()->findOrFail($request->program_id);
            if ($redirect = $this->authorizeProgram($program)) return $redirect;
            $groupedCourses = $program->getCoursesGroupedByYearAndSemester();
        }

        return view('Course.index', compact('program', 'groupedCourses'));
    }

    public function create(Request $request)
    {
        $programId = $request->query('program_id');
        $program = null;
        $programOutcomes = collect();

        if ($programId) {
            $program = Program::findOrFail($programId);
            if ($redirect = $this->authorizeProgram($program)) return $redirect;
            $programOutcomes = $program->outcomes()
                ->orderBy('po_code')
                ->get();
        }

        $poSelections = collect();

        $formAction  = route('courses.store');
        $formMethod  = 'POST';
        $pageTitle   = 'Create New Course';
        $submitLabel = 'Create Course';

        return view('Course.form', compact(
            'program',
            'programOutcomes',
            'poSelections',
            'formAction',
            'formMethod',
            'pageTitle',
            'submitLabel'
        ));
    }

    public function edit(Course $course)
    {
        $course->loadMissing(['program', 'programOutcomes']);
        if ($redirect = $this->authorizeProgram($course->program)) return $redirect;

        $program = $course->program;
        $programOutcomes = $program->outcomes()
            ->orderBy('po_code')
            ->get();

        $poSelections = $course->programOutcomes->pluck('pivot.ied', 'id');

        $formAction  = route('courses.update', $course->id);
        $formMethod  = 'PUT';
        $pageTitle   = 'Edit Course';
        $submitLabel = 'Update Course';

        return view('Course.form', compact(
            'course',
            'program',
            'programOutcomes',
            'poSelections',
            'formAction',
            'formMethod',
            'pageTitle',
            'submitLabel'
        ));
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate($this->courseRules());

        $program = Program::findOrFail($validatedData['program_id']);
        if ($redirect = $this->authorizeProgram($program)) return $redirect;

        try {
            $this->courseService->createCourse(
                $validatedData,
                $request->input('po_mapping', [])
            );

            return redirect()
                ->route('courses.index', [
                    'program_id' => $validatedData['program_id'],
                ])
                ->with('toast', [
                    'message' => 'Course created successfully.',
                    'type'    => 'success',
                ]);

        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'An error occurred while creating the course. Please try again.',
            ]);
        }

    }

    public function show(Course $course)
    {
        // Course details are presented via modal in the listing.
        // Keep this endpoint as a safe fallback redirect.
        return redirect()
            ->route('courses.index', ['program_id' => $course->program_id])
            ->with('toast', [
                'message' => 'Course details are available in the course list view modal.',
                'type' => 'info',
            ]);
    }

    public function update(Request $request, Course $course)
    {
        $validatedData = $request->validate($this->courseRules($course));
        if ($redirect = $this->authorizeProgram($course->program)) return $redirect;

        try {
            $this->courseService->updateCourse(
                $course,
                $validatedData,
                $request->input('po_mapping', [])
            );
        } catch (\RuntimeException $e) {
            return redirect()->back()->withErrors([
                'has_lec_lab' => $e->getMessage(),
            ])->withInput();
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'error' => 'An error occurred while updating the course. Please try again.',
            ])->withInput();
        }

        return redirect()
            ->route('courses.index', ['program_id' => $course->program_id])
            ->with('toast', [
                'message' => 'Course updated successfully.',
                'type'    => 'success',
            ]);
    }

    public function destroy(Course $course)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $canDelete = $user->hasRole('admin');

        if (!$canDelete && $user->hasRole('chair')) {
            $chairAssignment = $user->assignments()->where('context', 'chair')->first();
            if ($chairAssignment) {
                $course->loadMissing('program.departments');
                $programDeptIds = $course->program?->departments->pluck('id')->toArray() ?? [];
                $canDelete = in_array($chairAssignment->department_id, $programDeptIds);
            }
        }

        if (!$canDelete) {
            return redirect()->route('courses.index', ['program_id' => $course->program_id])
                ->with('toast', ['message' => 'Only admins or the department chair can delete courses.', 'type' => 'warning']);
        }

        $programId = $course->program_id;

        try {
            $this->courseService->deleteCourse($course);
        } catch (\Throwable $e) {
            return redirect()
                ->route('courses.index', ['program_id' => $programId])
                ->withErrors(['error' => 'Failed to delete the course. Please try again.']);
        }

        return redirect()
            ->route('courses.index', ['program_id' => $programId])
            ->with('toast', [
                'message' => 'Course deleted successfully.',
                'type'    => 'success',
            ]);
    }

    protected function authorizeProgram(?Program $program): ?\Illuminate\Http\RedirectResponse
    {
        if (!$program) return null;
        $user = Auth::user();
        if ($user->hasRole('admin')) return null;

        $assignment = $user->getPrimaryDepartmentAssignment();
        $allowed = $assignment && Program::whereHas('departments', fn($q) =>
            $q->where('department_id', $assignment->department_id)
        )->where('id', $program->id)->exists();

        if (!$allowed) {
            return redirect()->route('courses.index')
                ->with('toast', ['message' => 'You can only manage courses for programs in your assigned department.', 'type' => 'warning']);
        }

        return null;
    }

    protected function courseRules(?Course $course = null): array
    {
        $courseCodeRule = Rule::unique('courses', 'course_code');

        if ($course) {
            $courseCodeRule->ignore($course->id);
        }

        return [
            'program_id' => [$course ? 'sometimes' : 'required', 'exists:programs,id'],
            'confirmed_submission' => ['accepted'],
            'code' => ['required', 'string', $courseCodeRule],
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'credits' => ['required', 'integer', 'min:1'],
            'has_lec_lab' => ['nullable', 'boolean'],
            'year_level' => ['nullable', 'integer', 'between:1,5'],
            'semester' => ['nullable', 'integer', 'in:1,2'],
            'prerequisite' => ['nullable', 'string'],
            'corequisite' => ['nullable', 'string'],
            'po_mapping' => ['nullable', 'array'],
            'po_mapping.*' => ['nullable', 'in:I,E,D'],
        ];
    }

}
