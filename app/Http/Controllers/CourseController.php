<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
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
        // Validate input
        $validatedData = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'code' => 'required|string|unique:courses,course_code',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'credits' => 'required|integer|min:1',
            'has_lec_lab' => 'nullable|boolean',
            'year_level' => 'nullable|integer|between:1,5',
            'semester' => 'nullable|integer|in:1,2',
            'prerequisite' => 'nullable|string',
            'corequisite' => 'nullable|string',
            'po_mapping' => 'nullable|array',
            'po_mapping.*' => 'nullable|in:I,E,D',
        ]);

        // Create course
        $course = new Course();
        $course->program_id = $validatedData['program_id'];
        $course->course_code = $validatedData['code'];
        $course->course_title = $validatedData['name'];
        $course->course_description = $validatedData['description'] ?? null;
        $course->prerequisite = $validatedData['prerequisite'] ?? null;
        $course->corequisite = $validatedData['corequisite'] ?? null;
        $course->credit_units = $validatedData['credits'];
        $course->year_level = $validatedData['year_level'] ?? null;
        $course->semester = $validatedData['semester'] ?? null;
        $course->created_by = Auth::id();
        $course->has_lec_lab = $validatedData['has_lec_lab'] ?? false;
        $course->save();

        // Handle PO mapping using model helper
        if ($request->filled('po_mapping')) {
            $course->syncPoMappings(
                $request->input('po_mapping')
            );
        }

        return redirect()
            ->route('courses.index', [
                'program_id' => $validatedData['program_id'],
            ])
            ->with('toast', [
                'message' => 'Course created successfully.',
                'type'    => 'success',
            ]);

    }

    public function show(Course $course)
    {
        $course->loadMissing(['program', 'programOutcomes', 'creator']);
        return view('Course.show', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $validatedData = $request->validate([
            'code'           => 'required|string|unique:courses,course_code,' . $course->id,
            'name'           => 'required|string',
            'description'    => 'nullable|string',
            'credits'        => 'required|integer|min:1',
            'has_lec_lab'    => 'nullable|boolean',
            'year_level'     => 'nullable|integer|between:1,5',
            'semester'       => 'nullable|integer|in:1,2',
            'prerequisite'   => 'nullable|string',
            'corequisite'    => 'nullable|string',
            'po_mapping'     => 'nullable|array',
            'po_mapping.*'   => 'nullable|in:I,E,D',
        ]);

        $course->update([
            'course_code'        => $validatedData['code'],
            'course_title'       => $validatedData['name'],
            'course_description' => $validatedData['description'] ?? null,
            'prerequisite'       => $validatedData['prerequisite'] ?? null,
            'corequisite'        => $validatedData['corequisite'] ?? null,
            'credit_units'       => $validatedData['credits'],
            'year_level'         => $validatedData['year_level'] ?? null,
            'semester'           => $validatedData['semester'] ?? null,
            'has_lec_lab'        => $validatedData['has_lec_lab'] ?? false,
        ]);

        // Sync PO mappings
        $poMapping = $request->input('po_mapping', []);
        $syncData  = [];

        foreach ($poMapping as $outcomeId => $iedLevel) {
            if (in_array($iedLevel, ['I', 'E', 'D'], true)) {
                $syncData[$outcomeId] = ['ied' => $iedLevel];
            }
        }

        $course->programOutcomes()->sync($syncData);

        return redirect()
            ->route('courses.index', ['program_id' => $course->program_id])
            ->with('toast', [
                'message' => 'Course updated successfully.',
                'type'    => 'success',
            ]);
    }

    public function destroy(Course $course)
    {
        $programId = $course->program_id;

        $course->programOutcomes()->detach();
        $course->delete();

        return redirect()
            ->route('courses.index', ['program_id' => $programId])
            ->with('toast', [
                'message' => 'Course deleted successfully.',
                'type'    => 'success',
            ]);
    }

}
