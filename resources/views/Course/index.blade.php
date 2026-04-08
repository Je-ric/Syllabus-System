@extends('layouts.app')

@section('content')

@php
    $modalCourses = collect();
    $authUser = auth()->user();
    $isAdmin = $authUser->hasRole('admin');
    $isChair = $authUser->hasRole('chair');
    $chairDeptId = null;
    if ($isChair && $program) {
        $chairAssignment = $authUser->assignments()->where('context', 'chair')->first();
        $chairDeptId = $chairAssignment?->department_id;
        $programDeptIds = $program->departments->pluck('id')->toArray();
        $canDelete = in_array($chairDeptId, $programDeptIds);
    } else {
        $canDelete = $isAdmin;
    }
@endphp

    <x-page-header
        icon="bx-book"
        title="Manage Courses"
        desc="View and manage courses by program, year level, and semester">
        @if ($program)
            <x-button
                href="{{ route('courses.create', ['program_id' => $program->id]) }}"
                variant="add-button">
                <i class="bx bx-plus"></i> Add Course
            </x-button>
        @endif
    </x-page-header>

    <x-panel>
        {{-- Program selector --}}
        <div class="rounded-xl border border-[#e2e8f0] bg-white p-5 mb-6" style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">
            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-3">Select Program</p>
            <livewire:programs.program-selector
                :program-id="optional($program)?->id"
                redirect-route="courses.index"
                :autoRedirect="true" />
        </div>

        @if ($program)

            {{-- ── Program Outcomes reference (accordion) ───────────────────── --}}
            @if ($program->outcomes->isNotEmpty())
                <div x-data="{ open: false }" class="mb-6 rounded-xl border border-[#e2e8f0] bg-white overflow-hidden" style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">
                    <button type="button" @click="open = !open"
                        class="w-full px-5 py-3 border-b border-[#e2e8f0] flex items-center justify-between hover:bg-[#f8fafc] transition-colors">
                        <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569]">
                            Program Outcomes Reference
                        </p>
                        <div class="flex items-center gap-2">
                            <span class="text-[13px] text-[#94a3b8]">{{ $program->outcomes->count() }} outcome(s)</span>
                            <i class="bx text-[#94a3b8] text-base transition-transform duration-200"
                                :class="open ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
                        </div>
                    </button>
                    <div x-show="open" x-collapse>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <tbody class="divide-y divide-[#e2e8f0]">
                                    @foreach ($program->outcomes as $outcome)
                                        <tr class="hover:bg-[#f0fdf4] transition-colors">
                                            <td class="px-5 py-2.5 whitespace-nowrap w-px font-mono text-[13px] font-bold text-[#166534]">
                                                {{ $outcome->po_code }}
                                            </td>
                                            <td class="px-4 py-2.5 text-[13px] text-[#475569] leading-relaxed">
                                                {{ $outcome->po_text }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ── Curriculum map ────────────────────────────────────────────── --}}
            @forelse ($groupedCourses as $year => $semesters)

                    {{-- Year heading --}}
                    <div class="flex items-center gap-3 mb-4">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full
                                     bg-[#16a34a] text-white text-[13px] font-bold shrink-0">
                            {{ $year ?? '?' }}
                        </span>
                        <h3 class="text-[13px] font-bold text-[#0f172a] uppercase tracking-[0.12em]">
                            Year {{ $year ?? 'N/A' }}
                        </h3>
                        <div class="flex-1 h-px bg-[#e2e8f0]"></div>
                    </div>

                    @forelse ($semesters as $semester => $courses)
                        <div class="mb-5 rounded-xl border border-[#e2e8f0] bg-white overflow-hidden" style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">

                            {{-- Semester sub-header --}}
                            <div class="px-5 py-2.5 border-b border-[#e2e8f0] bg-[#f8fafc] flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#16a34a] shrink-0"></span>
                                <h4 class="text-[11px] font-bold text-[#475569] uppercase tracking-[0.12em]">
                                    Semester {{ $semester ?? 'N/A' }}
                                </h4>
                                <span class="ml-auto text-[13px] text-[#94a3b8]">
                                    {{ count($courses) }} course(s)
                                </span>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-sm border-collapse">
                                    <thead>
                                        <tr class="bg-[#f8fafc] border-b border-[#e2e8f0]">
                                            <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569]">Course</th>
                                            <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] w-16">Units</th>
                                            <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] w-16">Type</th>
                                            @foreach ($program->outcomes as $outcome)
                                                <th class="px-2 py-3 text-center text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] w-14">
                                                    {{ $outcome->po_code }}
                                                </th>
                                            @endforeach
                                            <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] w-28">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-[#e2e8f0]">
                                        @foreach ($courses as $course)
                                            @php $modalCourses->push($course); @endphp
                                            <tr class="hover:bg-[#f0fdf4] transition-colors group">

                                                <td class="px-5 py-3 text-[13px] text-[#0f172a]">
                                                    {{ $course->course_code }} - {{ $course->course_title }}
                                                </td>

                                                {{-- Units --}}
                                                <td class="px-4 py-3 text-center">
                                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full
                                                                 bg-[#f8fafc] text-[#0f172a] text-[13px] font-bold ring-1 ring-[#e2e8f0]">
                                                        {{ $course->credit_units }}
                                                    </span>
                                                </td>

                                                {{-- LEC / LAB chip --}}
                                                <td class="px-4 py-3 text-center">
                                                    @if ($course->has_lec_lab)
                                                        <x-feedback-status.status-indicator variant="lab" :dot="true">LEC+LAB</x-feedback-status.status-indicator>
                                                    @else
                                                        <x-feedback-status.status-indicator variant="brand" :dot="true">LEC</x-feedback-status.status-indicator>
                                                    @endif
                                                </td>

                                                {{-- PO IED cells --}}
                                                @foreach ($program->outcomes as $outcome)
                                                    @php
                                                        $mapping = $course->programOutcomes->firstWhere('id', $outcome->id);
                                                        $ied     = $mapping?->pivot->ied ?? null;
                                                    @endphp
                                                    <td class="px-2 py-3 text-center">
                                                        @if ($ied)
                                                            <x-feedback-status.ied-badge :level="$ied" />
                                                        @else
                                                            <span class="text-slate-200 text-xs select-none">—</span>
                                                        @endif
                                                    </td>
                                                @endforeach

                                                {{-- Actions --}}
                                                <td class="px-4 py-3 text-center">
                                                    <div class="inline-flex items-center gap-1.5">
                                                        <x-button
                                                            href="{{ route('courses.edit', $course->id) }}"
                                                            variant="table-edit"
                                                            title="Edit course">
                                                            <i class="bx bx-edit"></i> Edit
                                                        </x-button>
                                                        <x-button
                                                            type="button"
                                                            variant="table-view"
                                                            onclick="document.getElementById('viewCourseModal_{{ $course->id }}').showModal()"
                                                            title="View details">
                                                            <i class="bx bx-show"></i> View
                                                        </x-button>
                                                        @if ($canDelete)
                                                            <x-button
                                                                type="button"
                                                                variant="table-danger"
                                                                onclick="document.getElementById('deleteCourseModal_{{ $course->id }}').showModal()"
                                                                title="Delete course">
                                                                <i class="bx bx-trash"></i> Delete
                                                            </x-button>
                                                        @endif
                                                    </div>
                                                </td>

                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <x-empty-state icon="bx-book" title="No courses this semester"
                            message="No courses have been added for this semester yet." />
                    @endforelse
            @empty
                <x-empty-state icon="bx-book-open" title="No courses found"
                    message="This program has no courses yet. Add the first one to get started.">
                    <x-button href="{{ route('courses.create', ['program_id' => $program->id]) }}" variant="add-button">
                        <i class="bx bx-plus"></i> Add First Course
                    </x-button>
                </x-empty-state>
            @endforelse

        @else
            <x-empty-state icon="bx-book-open" title="No program selected"
                message="Select a program above to view and manage its courses." />
        @endif
    </x-panel>

    {{-- Modals --}}
    @foreach ($modalCourses as $course)
        @include('Course.modals.viewCourseModal', ['course' => $course])
        @if ($canDelete)
            @include('Course.modals.deleteCourseModal', ['course' => $course])
        @endif
    @endforeach

@endsection
