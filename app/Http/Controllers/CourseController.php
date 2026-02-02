<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Program;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        return view('Course.index');
    }

    public function create()
    {
        $programs = Program::all();
        return view('Course.create', compact('programs'));
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
            'ied' => 'nullable|integer|in:1,2,3',
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
            'created_by' => Auth::auth()->id(),
        ]);

        return redirect()->route('courses.index')
                        ->with('toast', [
                            'message' => 'Course created successfully.',
                            'type' => 'success'
                        ]);
    }

    public function show($id)
    {
        //
    }

}
