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
        $courses = collect();

        if ($request->filled('program_id')) {
            $program = Program::findOrFail($request->program_id);
            // Get all courses for this program with their mappings
            $courses = Course::where('program_id', $program->id)
                ->with(['programOutcomes', 'creator'])
                ->orderBy('year_level')
                ->orderBy('semester')
                ->orderBy('course_code')
                ->get();
        }

        return view('Course.index', compact('program', 'courses'));
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
        // Validate input
        $validatedData = $request->validate([
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
            'po_mapping.*' => 'nullable|in:I,E,D',
        ]);

        // Create course first
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

        // Default to false if not present (unchecked)
        $course->has_lec_lab = $validatedData['has_lec_lab'] ?? false;

        $course->save();

        // Handle PO mapping if provided
        $poMapping = $request->input('po_mapping', []);

        foreach ($poMapping as $outcomeId => $iedLevel) {
            if (in_array($iedLevel, ['I', 'E', 'D'])) {
                $course->programOutcomes()->attach($outcomeId, [
                    'ied' => $iedLevel
                ]);
            }
        }

        // Redirect back to course listing for this program
        return redirect()->route('courses.index', ['program_id' => $validatedData['program_id']])
            ->with('toast', [
                'message' => 'Course created successfully.',
                'type' => 'success'
            ]);
    }

    public function show($id)
    {
        // Get course with all relationships
        $course = Course::with(['program', 'programOutcomes', 'creator'])
            ->findOrFail($id);

        return view('Course.show', compact('course'));
    }
}
