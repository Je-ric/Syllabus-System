<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Program;
use App\Models\User;
use App\Services\Academic\CourseService;
use App\Rules\NoInjectionRule;
use Illuminate\Http\RedirectResponse;
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
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $program = null;
        $groupedCourses = collect();

        if ($request->filled('program_id')) {
            $program = Program::withOrderedOutcomes()->findOrFail($request->program_id);
            if ($redirect = $this->authorizeProgram($program)) return $redirect;
            $status = $request->boolean('archived') ? 'archived' : 'active';
            $groupedCourses = $program->getCoursesGroupedByYearAndSemester($status);
        }

        $noAssignment = !$user->hasRole('admin')
            && $user->hasRole('chair')
            && !$user->getPrimaryDepartmentAssignment();

        return view('Academic.Course.index', compact('program', 'groupedCourses', 'noAssignment'));
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

        return view('Academic.Course.form', compact(
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

        return view('Academic.Course.form', compact(
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
        $validatedData = $request->validate($this->courseRules(), $this->courseMessages());

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
                'error' => $e->getMessage() ?: 'An error occurred while creating the course. Please try again.',
            ])->withInput();
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
        $validatedData = $request->validate($this->courseRules($course), $this->courseMessages());
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
                'error' => $e->getMessage() ?: 'An error occurred while updating the course. Please try again.',
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
        /** @var User $user */
        $user = Auth::user();

        if (! $this->courseService->canDelete($course, $user)) {
            return redirect()->route('courses.index', ['program_id' => $course->program_id])
                ->with('toast', ['message' => 'Only admins or the department chair can delete courses.', 'type' => 'warning']);
        }

        $programId = $course->program_id;

        try {
            $this->courseService->deleteCourse($course);
        } catch (\Throwable $e) {
            return redirect()
                ->route('courses.index', ['program_id' => $programId])
                ->with('toast', ['message' => 'Failed to delete the course. Please try again.', 'type' => 'error']);
        }

        return redirect()
            ->route('courses.index', ['program_id' => $programId])
            ->with('toast', ['message' => 'Course deleted successfully.', 'type' => 'success']);
    }

    public function archive(Course $course)
    {
        if ($redirect = $this->authorizeProgram($course->program)) return $redirect;
        $this->courseService->archiveCourse($course);
        return redirect()
            ->route('courses.index', ['program_id' => $course->program_id])
            ->with('toast', ['message' => 'Course archived.', 'type' => 'success']);
    }

    public function restore(Course $course)
    {
        if ($redirect = $this->authorizeProgram($course->program)) return $redirect;
        $this->courseService->restoreCourse($course);
        return redirect()
            ->route('courses.index', ['program_id' => $course->program_id])
            ->with('toast', ['message' => 'Course restored.', 'type' => 'success']);
    }

    protected function authorizeProgram(?Program $program): ?RedirectResponse
    {
        if (!$program) return null;
        $user = Auth::user();

        /** @var User $user */
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
            'program_id'           => [$course ? 'sometimes' : 'required', 'exists:programs,id'],
            'confirmed_submission' => ['accepted'],
            'code'                 => ['required', 'string', 'max:50', 'regex:/^[A-Z0-9\-\.]+$/', $courseCodeRule],
            'name'                 => ['required', 'string', 'min:2', 'max:255', 'regex:/^[\p{L}\s\-\.\,0-9]+$/u', new NoInjectionRule()],
            'description'          => ['nullable', 'string', 'max:5000', new NoInjectionRule()],
            'credits'              => ['required', 'integer', 'min:1', 'max:5'],
            'has_lec_lab'          => ['nullable', 'boolean'],
            'passing_mark'         => ['nullable', 'numeric', 'min:0', 'max:100'],
            'lec_class_hours'      => ['nullable', 'string', 'max:50'],
            'lab_class_hours'      => ['nullable', 'string', 'max:50'],
            'year_level'           => ['nullable', 'integer', 'between:1,5'],
            'semester'             => ['nullable', 'integer', 'in:1,2'],
            'prerequisite'         => ['nullable', 'string', 'max:255', 'regex:/^[A-Z0-9\-\.\,\s]*$/', new NoInjectionRule()],
            'corequisite'          => ['nullable', 'string', 'max:255', 'regex:/^[A-Z0-9\-\.\,\s]*$/', new NoInjectionRule()],
            'po_mapping'           => ['nullable', 'array'],
            'po_mapping.*'         => ['nullable', 'in:I,E,D'],
        ];
    }

    protected function courseMessages(): array
    {
        return [
            'code.regex'              => 'Course code can only contain uppercase letters, numbers, hyphens, and periods.',
            'name.regex'              => 'Course name must contain only letters, numbers, spaces, and basic punctuation.',
            'name.min'                => 'Course name must be at least 2 characters.',
            'credits.min'             => 'Credit units must be at least 1.',
            'credits.max'             => 'Credit units must not exceed 5.',
            'prerequisite.regex'      => 'Prerequisites can only contain uppercase letters, numbers, and basic punctuation.',
            'corequisite.regex'       => 'Corequisites can only contain uppercase letters, numbers, and basic punctuation.',
        ];
    }

}
