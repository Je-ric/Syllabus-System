@extends('layouts.app')

@section('content')

@php
    $modalCourses = collect();
@endphp

<div class="mb-6">
    <h1 class="text-2xl font-bold mb-4">Manage Courses</h1>

    <div class="border rounded-lg p-6 bg-gray-50">
        <livewire:programs.program-selector
            :program-id="optional($program)?->id"
            redirect-route="courses.index"
        />
    </div>
</div>

@if ($program)
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-xl font-semibold">Courses in {{ $program->name }}</h2>
        <x-button href="{{ route('courses.create', ['program_id' => $program->id]) }}"
            variant="add-button">
            <i class="bx bx-plus"></i> Add Course
        </x-button>
    </div>

    {{-- PO's for Reference kumbaga HAHAHAHHA --}}
    <div class="mb-4">
        <h3 class="text-lg font-semibold mb-2">Program Outcomes Reference</h3>

        <div class="flex flex-col gap-3">
            @foreach ($program->outcomes as $outcome)
                <div class="bg-white border rounded-lg p-3 flex items-start gap-4">
                    <div class="shrink-0">
                            {{ $outcome->po_code }}.
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-800">{{ $outcome->po_text }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Group courses by year --}}
    @forelse ($groupedCourses as $year => $semesters)
        <div class="mb-8">
            <h3 class="text-lg font-semibold mb-4 border-b border-gray-300 pb-2">Year {{ $year ?? 'N/A' }}</h3>

            {{-- Group courses by semester --}}
            @forelse ($semesters as $semester => $courses)
                <div class="mb-6 bg-gray-50 border rounded-lg p-4">
                    <h4 class="font-medium text-gray-700 mb-3 border-b pb-1">
                        Semester {{ $semester ?? 'N/A' }}
                    </h4>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm border-collapse">
                            <thead class="bg-gray-100 border-b">
                                <tr>
                                    <th class="px-4 py-2 text-left font-semibold">CODE</th>
                                    <th class="px-4 py-2 text-left font-semibold">COURSE TITLE</th>
                                    <th class="px-4 py-2 text-center font-semibold">UNITS</th>

                                    @foreach ($program->outcomes as $outcome)
                                        <th class="px-2 py-2 text-center font-semibold text-xs">
                                            {{ $outcome->po_code }}
                                        </th>
                                    @endforeach

                                    <th class="px-4 py-2 text-center font-semibold">ACTIONS</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($courses as $course)
                                    @php
                                        $modalCourses->push($course);
                                    @endphp
                                    <tr class="hover:bg-gray-100 border-b">
                                        <td class="px-4 py-2 font-mono font-semibold">{{ $course->course_code }}</td>
                                        <td class="px-4 py-2">{{ $course->course_title }}</td>
                                        <td class="px-4 py-2 text-center">{{ $course->credit_units }}</td>

                                        @foreach ($program->outcomes as $outcome)
                                            @php
                                                $mapping = $course->programOutcomes->firstWhere('id', $outcome->id);
                                                $ied = $mapping?->pivot->ied ?? '-';
                                            @endphp
                                            <td class="px-2 py-2 text-center font-medium">
                                                <x-feedback-status.ied-badge :level="$ied" />
                                            </td>
                                        @endforeach

                                        <td class="px-4 py-2 text-center">
                                            <div class="flex gap-2 justify-center">
                                                <a href="{{ route('courses.edit', $course->id) }}"
                                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                   <i class="bx bx-edit"></i> Edit
                                                </a>

                                                <button class="text-blue-600 hover:underline text-sm"
                                                        onclick="document.getElementById('viewCourseModal_{{ $course->id }}').showModal()">
                                                    <i class="bx bx-show"></i> View
                                                </button>

                                                {{--
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
                                                --}}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-sm mt-2">No courses for this semester.</p>
            @endforelse
        </div>
    @empty
        <div class="text-center py-8 bg-gray-50 rounded-lg">
            <p class="text-gray-500 mb-3">No courses found for this program</p>
            <a href="{{ route('courses.create', ['program_id' => $program->id]) }}"
               class="text-blue-600 hover:underline">
               Create the first course
            </a>
        </div>
    @endforelse
@else
    <div class="text-center py-12 bg-gray-50 rounded-lg">
        <p class="text-gray-500">Select a program above to view and manage courses</p>
    </div>
@endif

{{-- Include modals for viewing courses --}}
@foreach ($modalCourses as $course)
    @include('Course.modals.viewCourseModal', ['course' => $course])
@endforeach

@endsection
