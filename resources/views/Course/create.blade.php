@extends('layouts.app')

@section('content')

<form action="{{ route('courses.store') }}" method="POST">
    @csrf
    <div class="flex">
        <div>
            <label>Course Code</label>
            <input type="text" name="course_code" class="border rounded px-2 py-1 w-full" value="{{ old('course_code') }}">
        </div>
        <div>
            <label>Course Title</label>
            <input type="text" name="course_title" class="border rounded px-2 py-1 w-full" value="{{ old('course_title') }}">
        </div>
    </div>
    <div>
        <label>Course Description</label>
        <input type="text" name="course_description" class="border rounded px-2 py-1 w-full" value="{{ old('course_description') }}">
    </div>
   <div class="grid grid-cols-4 gap-4">
        <div>
            <label>Credit Units</label>
            <input type="text" name="credit_units" class="border rounded px-2 py-1 w-full" value="{{ old('credit_units') }}">
        </div>
         <div>
            <label>Has Laboratory</label>
            <input type="radio" name="has_laboratory" class="border rounded px-2 py-1 w-full" value="{{ old('has_laboratory') }}">
            {{-- YES or NO --}}
        </div>
        <div>
            <label>Year Level</label>
            <input type="radio" name="year_level" class="border rounded px-2 py-1 w-full" value="{{ old('year_level') }}">
            {{-- 1-4 or sometimes 5 --}}
        </div>
        <div>
            <label>Semester</label>
            <input type="radio" name="semester" class="border rounded px-2 py-1 w-full" value="{{ old('semester') }}">
            {{-- 1 or 2 --}}
        </div>
   </div>
   <div class="flex">
        <div>
            <label>Prerequisite</label>
            <input type="text" name="prerequisite" class="border rounded px-2 py-1 w-full" value="{{ old('prerequisite') }}">
        </div>
        <div>
            <label>Corequisite</label>
            <input type="text" name="corequisite" class="border rounded px-2 py-1 w-full" value="{{ old('corequisite') }}">
        </div>
   </div>


    <h1>Program Course</h1>

    {{-- Two Columns --}}
    {{-- Left - PO lists of that program --}}
    {{-- Right - IED radio buttons. --}}


    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded mt-2">Create Course</button>
</form>


@endsection
