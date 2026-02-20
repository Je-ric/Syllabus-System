@extends('layouts.app')

@section('content')

@php
    $modalCourses = collect();
@endphp

    <x-header-with-button
        title="Manage Courses"
        description="Program Educational Objectives (PEO) and Program Outcomes (PO)"
    />

    <div class="border border-slate-200/80 rounded-2xl p-6 mb-6 bg-white/90 shadow-sm">
        <h2 class="text-sm uppercase tracking-[0.25em] text-slate-500 mb-4">Select Program</h2>
        <livewire:programs.program-selector
            :program-id="optional($program)?->id"
            redirect-route="courses.index"
            :autoRedirect="true"
        />
    </div>


@if ($program)
    <div class="mb-6 flex flex-wrap justify-between items-center gap-3">
        <h2 class="text-lg font-semibold text-slate-800">Courses in <span class="text-emerald-700">{{ $program->name }}</span></h2>
        <x-button href="{{ route('courses.create', ['program_id' => $program->id]) }}"
            variant="add-button">
            <i class="bx bx-plus"></i> Add Course
        </x-button>
    </div>

    {{-- PO's for Reference kumbaga HAHAHAHHA --}}
    <div class="mb-6">
        <h3 class="text-sm uppercase tracking-[0.25em] text-slate-500 mb-3">Program Outcomes Reference</h3>

        <div class="flex flex-col gap-3">
            @foreach ($program->outcomes as $outcome)
                <div class="bg-white/90 border border-slate-200 rounded-2xl p-4 flex items-start gap-4 shadow-sm">
                    <div class="shrink-0 text-emerald-700 font-semibold">
                            {{ $outcome->po_code }}.
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-slate-700">{{ $outcome->po_text }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Group courses by year --}}
    @forelse ($groupedCourses as $year => $semesters)
        <div class="mb-8">
            <h3 class="text-sm uppercase tracking-[0.25em] text-slate-500 mb-4 border-b border-slate-200 pb-2">Year {{ $year ?? 'N/A' }}</h3>

            {{-- Group courses by semester --}}
            @forelse ($semesters as $semester => $courses)
                <div class="mb-6 bg-white/90 border border-slate-200 rounded-2xl p-4 shadow-sm">
                    <h4 class="font-medium text-slate-700 mb-3 border-b border-slate-200 pb-1">
                        Semester {{ $semester ?? 'N/A' }}
                    </h4>

                    <x-table.container>
                        <x-table.table class="border border-slate-200">
                            <x-table.head>
                                <tr>
                                    <x-table.th class="px-4 py-2">Code</x-table.th>
                                    <x-table.th class="px-4 py-2">Course Title</x-table.th>
                                    <x-table.th align="center" class="px-4 py-2">Units</x-table.th>

                                    @foreach ($program->outcomes as $outcome)
                                        <x-table.th align="center" class="px-2 py-2">
                                            {{ $outcome->po_code }}
                                        </x-table.th>
                                    @endforeach

                                    <x-table.th align="center" class="px-4 py-2">Actions</x-table.th>
                                </tr>
                            </x-table.head>

                            <x-table.body>
                                @foreach ($courses as $course)
                                    @php
                                        $modalCourses->push($course);
                                    @endphp
                                    <x-table.row striped hover class="border-b border-slate-200">
                                        <x-table.td class="px-4 py-2 font-mono font-semibold text-slate-700">{{ $course->course_code }}</x-table.td>
                                        <x-table.td class="px-4 py-2 text-slate-700">{{ $course->course_title }}</x-table.td>
                                        <x-table.td align="center" class="px-4 py-2 text-slate-700">{{ $course->credit_units }}</x-table.td>

                                        @foreach ($program->outcomes as $outcome)
                                            @php
                                                $mapping = $course->programOutcomes->firstWhere('id', $outcome->id);
                                                $ied = $mapping?->pivot->ied ?? '-';
                                            @endphp
                                            <x-table.td align="center" class="px-2 py-2 font-medium">
                                                <x-feedback-status.ied-badge :level="$ied" />
                                            </x-table.td>
                                        @endforeach

                                        <x-table.td align="center" class="px-4 py-2">
                                            <div class="flex gap-3 justify-center">
                                                <a href="{{ route('courses.edit', $course->id) }}"
                                                   class="text-emerald-700 hover:text-emerald-900 text-sm font-medium">
                                                   <i class="bx bx-edit"></i> Edit
                                                </a>

                                                <button class="text-slate-600 hover:text-slate-900 text-sm"
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
                                        </x-table.td>
                                    </x-table.row>
                                @endforeach
                            </x-table.body>
                        </x-table.table>
                    </x-table.container>
                </div>
            @empty
                <p class="text-slate-500 text-sm mt-2">No courses for this semester.</p>
            @endforelse
        </div>
    @empty
        <div class="text-center py-8 bg-slate-50 rounded-2xl border border-slate-200">
            <p class="text-slate-500 mb-3">No courses found for this program</p>
            <a href="{{ route('courses.create', ['program_id' => $program->id]) }}"
                class="text-emerald-700 hover:underline">
               Create the first course
            </a>
        </div>
    @endforelse
@else
    <div class="text-center py-12 bg-slate-50 rounded-2xl border border-slate-200">
        <p class="text-slate-500">Select a program above to view and manage courses</p>
    </div>
@endif

{{-- Include modals for viewing courses --}}
@foreach ($modalCourses as $course)
    @include('Course.modals.viewCourseModal', ['course' => $course])
@endforeach

@endsection
