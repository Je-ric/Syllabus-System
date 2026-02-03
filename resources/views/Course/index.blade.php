{{-- @extends('layouts.app')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold mb-4">Manage Courses</h1>
    <div class="border rounded-lg p-6 bg-gray-50">
        <livewire:programs.program-selector :program-id="optional($program)?->id" redirect-route="courses.index" />
    </div>
</div>

@if ($program)
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
            @if ($course->course_description)
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

@endsection --}}

{{-- filepath: c:\Users\Janice\Desktop\csms\resources\views\Course\index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold mb-4">Manage Courses</h1>
        <div class="border rounded-lg p-6 bg-gray-50">
            <livewire:programs.program-selector :program-id="optional($program)?->id" redirect-route="courses.index" />
        </div>
    </div>

    @if ($program)
        <div class="mb-6 flex justify-between items-center">
            <h2 class="text-xl font-semibold">Courses in {{ $program->name }}</h2>
            <a href="{{ route('courses.create', ['program_id' => $program->id]) }}"
                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                <i class="bx bx-plus"></i> Add Course
            </a>
        </div>

        @forelse ($groupedCourses as $year => $semesters)
            <div class="mb-8">
                <h3 class="text-lg font-semibold mb-4 border-b pb-2">Year {{ $year ?? 'N/A' }}</h3>

                @forelse ($semesters as $semester => $courses)
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-700 mb-3">Semester {{ $semester ?? 'N/A' }}</h4>
                        <div class="overflow-x-auto rounded-lg border">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-100 border-b">
                                    <tr>
                                        <th class="px-4 py-2 text-left font-semibold">CODE</th>
                                        <th class="px-4 py-2 text-left font-semibold">COURSE TITLE</th>
                                        <th class="px-4 py-2 text-center font-semibold">UNITS</th>
                                        @foreach ($program->outcomes as $outcome)
                                            <th class="px-2 py-2 text-center font-semibold text-xs">{{ $outcome->po_code }}</th>
                                        @endforeach
                                        <th class="px-4 py-2 text-center font-semibold">ACTIONS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($courses as $course)
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="px-4 py-2 font-mono font-semibold">{{ $course->course_code }}</td>
                                            <td class="px-4 py-2">{{ $course->course_title }}</td>
                                            <td class="px-4 py-2 text-center">{{ $course->credit_units }}</td>

                                            @foreach ($program->outcomes as $outcome)
                                                @php
                                                    $mapping = $course->programOutcomes->firstWhere('id', $outcome->id);
                                                    $ied = $mapping?->pivot->ied ?? '-';
                                                @endphp
                                                <td class="px-2 py-2 text-center font-medium">
                                                    <span class="px-2 py-1 rounded text-xs {{ $ied === 'I' ? 'bg-blue-100 text-blue-700' : ($ied === 'E' ? 'bg-yellow-100 text-yellow-700' : ($ied === 'D' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500')) }}">
                                                        {{ $ied }}
                                                    </span>
                                                </td>
                                            @endforeach

                                            <td class="px-4 py-2 text-center">
                                                <div class="flex gap-2 justify-center">
                                                    <a href="{{ route('courses.edit', $course->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                        <i class="bx bx-edit"></i> Edit
                                                    </a>
                                                    <button
                                                        class="text-blue-600 hover:underline text-sm"
                                                        onclick="document.getElementById('viewCourseModal_{{ $course->id }}').showModal()"
                                                    >
                                                        <i class="bx bx-show"></i> View
                                                    </button>
                                                    <button
                                                        onclick="confirm('Delete this course?') && document.getElementById('delete-form-{{ $course->id }}').submit()"
                                                        class="text-red-600 hover:text-red-800 text-sm font-medium"
                                                    >
                                                        <i class="bx bx-trash"></i> Delete
                                                    </button>
                                                    <form id="delete-form-{{ $course->id }}" action="{{ route('courses.destroy', $course->id) }}" method="POST" style="display:none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No courses for this semester.</p>
                @endforelse
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
