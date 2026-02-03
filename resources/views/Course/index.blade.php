@extends('layouts.app')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold mb-4">Manage Courses</h1>
    <div class="border rounded-lg p-6 bg-gray-50">
        <livewire:programs.program-selector :program-id="optional($program)?->id" redirect-route="courses.index" />
    </div>
</div>

@if($program)
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-xl font-semibold">Courses in {{ $program->name }}</h2>
        <a href="{{ route('courses.create', ['program_id' => $program->id]) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            <i class="bx bx-plus"></i> Add Course
        </a>
    </div>

    @forelse($program->courses()->orderBy('course_code')->get() as $course)
        <div class="border rounded-lg p-4 mb-3 hover:shadow-md transition">
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <p class="text-gray-600 text-sm">Course Code</p>
                    <p class="font-semibold">{{ $course->course_code }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Course Title</p>
                    <p class="font-semibold">{{ $course->course_title }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Credit Units</p>
                    <p class="font-semibold">{{ $course->credit_units }}</p>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4 mt-3 text-sm">
                <p><span class="text-gray-600">Year Level:</span> {{ $course->year_level ?? 'N/A' }}</p>
                <p><span class="text-gray-600">Semester:</span> {{ $course->semester ? 'Sem ' . $course->semester : 'N/A' }}</p>
                <p><span class="text-gray-600">Lab:</span> {{ $course->has_lec_lab ? 'Yes' : 'No' }}</p>
            </div>
            @if($course->course_description)
                <p class="mt-2 text-sm text-gray-700">{{ $course->course_description }}</p>
            @endif
            <div class="mt-3">
            <button
                class="text-blue-600 hover:underline text-sm"
                onclick="document.getElementById('viewCourseModal_{{ $course->id }}').showModal()"
            >
                <i class="bx bx-show"></i> View Details
            </button>
        </div>
        </div>
    @empty
        <div class="text-center py-8 bg-gray-50 rounded-lg">
            <p class="text-gray-500 mb-3">No courses found for this program</p>
            <a href="{{ route('courses.create', ['program_id' => $program->id]) }}" class="text-blue-600 hover:underline">
                Create the first course
            </a>
        </div>
    @endforelse
@else
    <div class="text-center py-12 bg-gray-50 rounded-lg">
        <p class="text-gray-500">Select a program above to view and manage courses</p>
    </div>
@endif

@include('Course.modals.viewCourseModal', ['course' => $course])

@endsection
