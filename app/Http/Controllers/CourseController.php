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

        if ($request->filled('program_id')) {
            $program = Program::findOrFail($request->program_id);
        }

        return view('Course.index', compact('program'));
    }

    public function create(Request $request)
    {
        $programId = $request->query('program_id');
        $program = null;
        $programOutcomes = collect();

        if ($programId) {
            $program = Program::findOrFail($programId);
            $programOutcomes = $program->outcomes()->orderBy('po_code')->get();
        }

        return view('Course.create', compact('program', 'programOutcomes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,id',
            'code' => 'required|string|unique:courses,course_code',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'credits' => 'required|integer|min:0',
            'has_lec_lab' => 'nullable|boolean',
            'year_level' => 'nullable|integer|between:1,5',
            'semester' => 'nullable|integer|in:1,2',
            'prerequisite' => 'nullable|string',
            'corequisite' => 'nullable|string',
            'po_mapping' => 'nullable|array',
            'po_mapping.*' => 'nullable|integer|in:1,2,3',
        ]);

        $course = Course::create([
            'program_id' => $request->program_id,
            'course_code' => $request->code,
            'course_title' => $request->name,
            'course_description' => $request->description,
            'prerequisite' => $request->prerequisite,
            'corequisite' => $request->corequisite,
            'has_lec_lab' => $request->has_lec_lab ?? false,
            'credit_units' => $request->credits,
            'year_level' => $request->year_level,
            'semester' => $request->semester,
            'created_by' => Auth::id(),
        ]);

        // Attach outcomes with IED level (only those with selected IED level)
        $poMapping = $request->input('po_mapping', []);
        if (!empty($poMapping)) {
            $outcomeData = [];
            foreach ($poMapping as $outcomeId => $iedLevel) {
                if ($iedLevel) { // Only attach if IED level is selected
                    $outcomeData[$outcomeId] = ['ied' => $iedLevel];
                }
            }
            if (!empty($outcomeData)) {
                $course->programOutcomes()->attach($outcomeData);
            }
        }

        $program = Program::find($request->program_id);
        return redirect()->route('courses.index', ['program_id' => $program->id])
                        ->with('toast', [
                            'message' => 'Course created successfully.',
                            'type' => 'success'
                        ]);
    }

    public function show($id)
    {
    }

}
