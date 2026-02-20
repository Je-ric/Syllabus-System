<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveCourseRequest;
use App\Models\Course;
use App\Models\Program;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


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


    public function store(SaveCourseRequest $request)
    {
        $validatedData = $request->validated();

        DB::beginTransaction();

        try {
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

            $program = Program::with('departments.college')->find($course->program_id);
            $primaryDepartment = $program?->departments->first();
            $collegeName = $primaryDepartment?->college?->name ?? 'N/A';
            $departmentName = $primaryDepartment?->name ?? 'N/A';

            // LOGS
            AuditLog::record(
                action: 'created',
                module: 'Course',
                referenceId: $course->id,
                description: "Created course {$course->course_code} ({$course->course_title}) for program {$program?->name}; college: {$collegeName}; department: {$departmentName}."
            );

            DB::commit();

            return redirect()
                ->route('courses.index', [
                    'program_id' => $validatedData['program_id'],
                ])
                ->with('toast', [
                    'message' => 'Course created successfully.',
                    'type'    => 'success',
                ]);

        } catch (\Exception $e) {
            DB::rollBack();

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

    public function update(SaveCourseRequest $request, Course $course)
    {
        $validatedData = $request->validated();

        DB::beginTransaction();

        try {
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

            $program = Program::with('departments.college')->find($course->program_id);
            $primaryDepartment = $program?->departments->first();
            $collegeName = $primaryDepartment?->college?->name ?? 'N/A';
            $departmentName = $primaryDepartment?->name ?? 'N/A';

            // LOGS
            AuditLog::record(
                action: 'updated',
                module: 'Course',
                referenceId: $course->id,
                description: "Updated course {$course->course_code} ({$course->course_title}); program {$program?->name}; college: {$collegeName}; department: {$departmentName}."
            );

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

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

}
