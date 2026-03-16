<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Program;
use App\Services\CourseService;
use Illuminate\Http\Request;
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
            $program = Program::withOrderedOutcomes()->findOrFail($request->program_id); // helper
            $groupedCourses = $program->getCoursesGroupedByYearAndSemester(); // helper
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

        try {
            $this->courseService->updateCourse(
                $course,
                $validatedData,
                $request->input('po_mapping', [])
            );
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

    // public function destroy(Course $course)
    // {
    //     $programId = $course->program_id;
    //     $user = Auth();

    //     $course->programOutcomes()->detach();
    //     $course->delete();

    //     return redirect()
    //         ->route('courses.index', ['program_id' => $programId])
    //         ->with('toast', [
    //             'message' => 'Course deleted successfully.',
    //             'type'    => 'success',
    //         ]);
    // }

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
