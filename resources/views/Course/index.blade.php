@extends('layouts.app')

@section('content')

@php $modalCourses = collect(); @endphp

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
        {{-- Program selector card --}}
        <div class="border border-slate-200/80 rounded-2xl p-5 mb-6 bg-white/90 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 mb-3">
                Select Program
            </p>
            <livewire:programs.program-selector
                :program-id="optional($program)?->id"
                redirect-route="courses.index"
                :autoRedirect="true" />
        </div>
    
        @if ($program)
    
            {{-- ── Program Outcomes reference — compact scrollable table ──────────── --}}
            @if ($program->outcomes->isNotEmpty())
                <div class="mb-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 mb-2">
                        Program Outcomes Reference
                        <span class="ml-2 font-normal normal-case tracking-normal text-slate-400">
                            —  {{ $program->outcomes->count() }} outcome(s)
                        </span>
                    </p>
    
                    {{-- Compact horizontal-scroll table — avoids stacking many full cards --}}
                    <div class="rounded-2xl border border-slate-200/80 bg-white/90 shadow-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($program->outcomes as $outcome)
                                        <tr class="hover:bg-slate-50/60 transition-colors">
                                            <td class="px-4 py-2.5 whitespace-nowrap w-px font-mono text-sm font-bold text-emerald-700">
                                                {{ $outcome->po_code }}
                                            </td>
                                            <td class="px-4 py-2.5 text-slate-600 leading-relaxed">
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
    
            {{-- ── Courses grouped by year → semester ─────────────────────────────── --}}
            @forelse ($groupedCourses as $year => $semesters)
                <div class="mb-8">
                    <h3 class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500
                                border-b border-slate-200 pb-2 mb-4">
                        Year {{ $year ?? 'N/A' }}
                    </h3>
    
                    @forelse ($semesters as $semester => $courses)
                        <div class="mb-5 rounded-2xl border border-slate-200/80 bg-white/90 shadow-sm overflow-hidden">
    
                            <div class="px-4 py-2.5 border-b border-slate-100 bg-slate-50/60">
                                <h4 class="text-xs font-semibold text-slate-600 uppercase tracking-[0.15em]">
                                    Semester {{ $semester ?? 'N/A' }}
                                    <span class="ml-2 font-normal normal-case tracking-normal text-slate-400">
                                        — {{ count($courses) }} course(s)
                                    </span>
                                </h4>
                            </div>
    
                            <x-table.container class="rounded-none border-0 shadow-none bg-transparent">
                                <x-table.table class="border-0">
                                    <x-table.head class="bg-slate-50/70 text-slate-700">
                                        <tr>
                                            <x-table.th class="border-0 px-4 py-3">Code</x-table.th>
                                            <x-table.th class="border-0 px-4 py-3">Course Title</x-table.th>
                                            <x-table.th align="center" class="border-0 px-4 py-3">Units</x-table.th>
    
                                            {{-- PO columns — one per outcome --}}
                                            @foreach ($program->outcomes as $outcome)
                                                <x-table.th align="center" class="border-0 px-2 py-3 whitespace-nowrap">
                                                    <span class="text-[10px] font-bold">{{ $outcome->po_code }}</span>
                                                </x-table.th>
                                            @endforeach
    
                                            <x-table.th align="center" class="border-0 px-4 py-3">Actions</x-table.th>
                                        </tr>
                                    </x-table.head>
    
                                    <x-table.body class="divide-y divide-slate-100">
                                        @foreach ($courses as $course)
                                            @php $modalCourses->push($course); @endphp
    
                                            <x-table.row striped hover>
                                                <x-table.td class="border-0 px-4 py-3 font-mono text-xs font-semibold text-slate-700 whitespace-nowrap">
                                                    {{ $course->course_code }}
                                                </x-table.td>
                                                <x-table.td class="border-0 px-4 py-3 text-sm text-slate-700">
                                                    {{ $course->course_title }}
                                                </x-table.td>
                                                <x-table.td align="center" class="border-0 px-4 py-3 text-sm text-slate-600">
                                                    {{ $course->credit_units }}
                                                </x-table.td>
    
                                                @foreach ($program->outcomes as $outcome)
                                                    @php
                                                        $mapping = $course->programOutcomes->firstWhere('id', $outcome->id);
                                                        $ied     = $mapping?->pivot->ied ?? '–';
                                                    @endphp
                                                    <x-table.td align="center" class="border-0 px-2 py-3">
                                                        <x-feedback-status.ied-badge :level="$ied" />
                                                    </x-table.td>
                                                @endforeach
    
                                                <x-table.td align="center" class="border-0 px-4 py-3">
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
                                                    </div>
                                                </x-table.td>
                                            </x-table.row>
                                        @endforeach
                                    </x-table.body>
                                </x-table.table>
                            </x-table.container>
                        </div>
                    @empty
                        <x-empty-state
                            icon="bx-book"
                            title="No courses this semester"
                            message="No courses have been added for this semester yet." />
                    @endforelse
                </div>
            @empty
                <x-empty-state
                    icon="bx-book-open"
                    title="No courses found"
                    message="This program has no courses yet. Add the first one to get started.">
                    <x-button
                        href="{{ route('courses.create', ['program_id' => $program->id]) }}"
                        variant="add-button">
                        <i class="bx bx-plus"></i> Add First Course
                    </x-button>
                </x-empty-state>
            @endforelse
    
        @else
            <x-empty-state
                icon="bx-book-open"
                title="No program selected"
                message="Select a program above to view and manage its courses." />
        @endif
    </x-panel>

    {{-- View modals — collected while rendering rows to avoid duplicate loops --}}
    @foreach ($modalCourses as $course)
        @include('Course.modals.viewCourseModal', ['course' => $course])
    @endforeach

@endsection
