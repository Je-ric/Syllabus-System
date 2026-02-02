@extends('layouts.app')

@section('content')

<form action="{{ route('courses.store') }}" method="POST">
    @csrf
    
    {{-- Program Selection --}}
    <div class="mb-4">
        <label class="block font-semibold">Program</label>
        <select name="program_id" class="border rounded px-2 py-1 w-full" required>
            <option value="">Select a Program</option>
            @forelse($programs as $program)
                <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                    {{ $program->name }}
                </option>
            @empty
                <option disabled>No programs available</option>
            @endforelse
        </select>
        @error('program_id')
            <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror
    </div>

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
    <h2 class="text-lg font-semibold mb-4">Program Outcomes Mapping</h2>

    <div class="grid grid-cols-2 gap-6 mb-4">
        {{-- Left Column - Program Outcomes List --}}
        <div>
            <label class="block font-semibold mb-2">Select Program Outcomes</label>
            <div class="border rounded p-3 bg-gray-50 h-64 overflow-y-auto">
                <div id="program-outcomes" class="space-y-2">
                    {{-- PO checkboxes will be populated here --}}
                    <p class="text-gray-500 text-sm">Select a program above to see outcomes</p>
                </div>
            </div>
        </div>

        {{-- Right Column - IED Radio Buttons --}}
        <div>
            <label class="block font-semibold mb-2">Integrated Educational Design (IED)</label>
            <div class="border rounded p-3 bg-gray-50">
                <div class="space-y-3">
                    <div>
                        <label class="block font-semibold text-sm mb-2">Select IED Level for Checked Outcomes:</label>
                        <div class="space-y-2">
                            <label><input type="radio" name="ied" value="1" {{ old('ied') == '1' ? 'checked' : '' }}> 1 - Introduced</label>
                            <label><input type="radio" name="ied" value="2" {{ old('ied') == '2' ? 'checked' : '' }}> 2 - Developed</label>
                            <label><input type="radio" name="ied" value="3" {{ old('ied') == '3' ? 'checked' : '' }}> 3 - Mastered</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex gap-2 mt-6">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Create Course</button>
        <a href="{{ route('courses.index') }}" class="bg-gray-400 text-white px-4 py-2 rounded">Cancel</a>
    </div>
</form>

@endsection
