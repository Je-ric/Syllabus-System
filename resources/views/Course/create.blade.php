@extends('layouts.app')

@section('content')

<div class="mb-6">
    <a href="{{ route('courses.index') }}" class="text-blue-600 hover:underline mb-4 inline-block">
        <i class="bx bx-chevron-left"></i> Back to Courses
    </a>
    <h1 class="text-2xl font-bold">Create New Course</h1>
</div>

@if(!$program)
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <p class="text-yellow-800">Please select a program from the courses page before creating a course.</p>
    </div>
@endif

<form action="{{ route('courses.store') }}" method="POST">
    @csrf

    {{-- Program Selection (Hidden if passed via URL) --}}
    <input type="hidden" name="program_id" value="{{ $program?->id ?? '' }}">

    @if($program)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <p class="text-gray-700"><span class="font-semibold">Program:</span> {{ $program->name }}</p>
        </div>
    @endif

    {{-- Course Code and Title --}}
    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block font-semibold">Course Code</label>
            <input type="text" name="code" class="border rounded px-2 py-1 w-full" value="{{ old('code') }}" required>
            @error('code')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label class="block font-semibold">Course Title</label>
            <input type="text" name="name" class="border rounded px-2 py-1 w-full" value="{{ old('name') }}" required>
            @error('name')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>
    </div>

    {{-- Course Description --}}
    <div class="mb-4">
        <label class="block font-semibold">Course Description</label>
        <textarea name="description" class="border rounded px-2 py-1 w-full" rows="3">{{ old('description') }}</textarea>
        @error('description')
            <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror
    </div>

    {{-- Credit Units, Lab, Year Level, Semester --}}
    <div class="grid grid-cols-4 gap-4 mb-4">
        <div>
            <label class="block font-semibold">Credit Units</label>
            <input type="number" name="credits" class="border rounded px-2 py-1 w-full" value="{{ old('credits') }}" min="0" required>
            @error('credits')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label class="block font-semibold">Has Laboratory</label>
            <div class="flex gap-4 mt-2">
                <label><input type="radio" name="has_lec_lab" value="1" {{ old('has_lec_lab') == '1' ? 'checked' : '' }}> Yes</label>
                <label><input type="radio" name="has_lec_lab" value="0" {{ old('has_lec_lab') == '0' ? 'checked' : '' }}> No</label>
            </div>
        </div>
        <div>
            <label class="block font-semibold">Year Level</label>
            <select name="year_level" class="border rounded px-2 py-1 w-full">
                <option value="">Select Year</option>
                @for ($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}" {{ old('year_level') == $i ? 'selected' : '' }}>Year {{ $i }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block font-semibold">Semester</label>
            <select name="semester" class="border rounded px-2 py-1 w-full">
                <option value="">Select Semester</option>
                <option value="1" {{ old('semester') == '1' ? 'selected' : '' }}>1st Semester</option>
                <option value="2" {{ old('semester') == '2' ? 'selected' : '' }}>2nd Semester</option>
            </select>
        </div>
    </div>

    {{-- Prerequisite and Corequisite --}}
    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block font-semibold">Prerequisite</label>
            <input type="text" name="prerequisite" class="border rounded px-2 py-1 w-full" value="{{ old('prerequisite') }}">
        </div>
        <div>
            <label class="block font-semibold">Corequisite</label>
            <input type="text" name="corequisite" class="border rounded px-2 py-1 w-full" value="{{ old('corequisite') }}">
        </div>
    </div>

    <hr class="my-6">

    {{-- Program Course (PO and IED Mapping) --}}
    @if($program)
        <hr class="my-6">
        <div class="flex justify-between mb-4">
            <h2 class="text-lg font-semibold mb-4">Program Outcomes Mapping</h2>

            <p><b>Level:</b> I – Introductory, E – Enabling, D – Demonstrative</p>
        </div>

        @if($programOutcomes->isEmpty())
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                <p class="text-yellow-800">No program outcomes defined for {{ $program->name }}. Please define outcomes first.</p>
            </div>
        @else
            <div class="border rounded-lg overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">PO Code</th>
                            <th class="px-4 py-3 text-left font-semibold">Learning Outcome</th>
                            <th class="px-4 py-3 text-left font-semibold">IED Level</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($programOutcomes as $outcome)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 font-semibold text-blue-600">
                                    {{-- {{ $outcome->po_code }} --}}
                                    PO{{ $loop->iteration }}
                                </td>
                                <td class="px-4 py-3 text-sm" >
                                    {{ $outcome->po_text }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-4">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="po_mapping[{{ $outcome->id }}]" value="I" class="mr-1">
                                            <span class="text-xs text-gray-600">I</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="po_mapping[{{ $outcome->id }}]" value="E" class="mr-1">
                                            <span class="text-xs text-gray-600">E</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="po_mapping[{{ $outcome->id }}]" value="D" class="mr-1">
                                            <span class="text-xs text-gray-600">D</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="text-sm text-gray-500 mt-2">Select IED level for each outcome that applies to this course (leave blank if not applicable)</p>
        @endif
    @endif

    <div class="flex gap-2 mt-6">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Create Course</button>
        <a href="{{ route('courses.index') }}" class="bg-gray-400 text-white px-4 py-2 rounded">Cancel</a>
    </div>
</form>

@endsection
