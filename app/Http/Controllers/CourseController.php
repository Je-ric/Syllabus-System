<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        return view('Course.index');
    }

    public function create()
    {
        return view('Course.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:courses,code',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'credits' => 'required|integer|min:0',
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
