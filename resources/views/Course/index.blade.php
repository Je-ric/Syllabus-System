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
        <div class="border border-slate-200/80 rounded-2xl p-5 mb-6 bg-white/90 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 mb-3">Select Program</p>
            <livewire:programs.program-selector
                :program-id="optional($program)?->id"
                redirect-route="courses.index"
                :autoRedirect="true" />
        </div>

        @if ($program)

            {{-- ── Program Outcomes reference (accordion) ───────────────────── --}}
            @if ($program->outcomes->isNotEmpty())
                <div x-data="{ open: false }" class="mb-6 rounded-2xl border border-slate-200/80 bg-white/90 shadow-sm overflow-hidden">
                    <button type="button" @click="open = !open"
                        class="w-full px-5 py-3 border-b border-slate-100 flex items-center justify-between hover:bg-slate-50/60 transition-colors">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                            Program Outcomes Reference
                        </p>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-slate-400">{{ $program->outcomes->count() }} outcome(s)</span>
                            <i class="bx text-slate-400 text-base transition-transform duration-200"
                                :class="open ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
                        </div>
                    </button>
                    <div x-show="open" x-collapse>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($program->outcomes as $outcome)
                                        <tr class="hover:bg-slate-50/60 transition-colors">
                                            <td class="px-5 py-2.5 whitespace-nowrap w-px font-mono text-xs font-bold text-emerald-700">
                                                {{ $outcome->po_code }}
                                            </td>
                                            <td class="px-4 py-2.5 text-slate-600 leading-relaxed text-sm">
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
                                     bg-emerald-600 text-white text-xs font-bold shrink-0">
                            {{ $year ?? '?' }}
                        </span>
                        <h3 class="text-sm font-bold text-slate-700 uppercase tracking-[0.15em]">
                            Year {{ $year ?? 'N/A' }}
                        </h3>
                        <div class="flex-1 h-px bg-slate-200"></div>
                    </div>

                    @forelse ($semesters as $semester => $courses)
                        <div class="mb-5 rounded-2xl border border-slate-200/80 bg-white/90 shadow-sm overflow-hidden">

                            {{-- Semester sub-header --}}
                            <div class="px-5 py-2.5 border-b border-slate-100 bg-slate-50/60 flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                <h4 class="text-xs font-semibold text-slate-600 uppercase tracking-[0.15em]">
                                    Semester {{ $semester ?? 'N/A' }}
                                </h4>
                                <span class="ml-auto text-xs text-slate-400">
                                    {{ count($courses) }} course(s)
                                </span>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-sm border-collapse">
                                    <thead>
                                        <tr class="bg-slate-50/70 text-slate-500 text-xs uppercase tracking-[0.12em]">
                                            <th class="px-5 py-3 text-left font-semibold">Course</th>
                                            <th class="px-4 py-3 text-center font-semibold w-16">Units</th>
                                            <th class="px-4 py-3 text-center font-semibold w-16">Type</th>

                                            @foreach ($program->outcomes as $outcome)
                                                <th class="px-2 py-3 text-center font-semibold w-14">
                                                    {{ $outcome->po_code }}
                                                </th>
                                            @endforeach

                                            <th class="px-4 py-3 text-center font-semibold w-28">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach ($courses as $course)
                                            @php $modalCourses->push($course); @endphp
                                            <tr class="hover:bg-emerald-50/30 transition-colors group">

                                                <td class="px-5 py-3">
                                                    {{ $course->course_code }} - {{ $course->course_title }}
                                                </td>

                                                {{-- Units --}}
                                                <td class="px-4 py-3 text-center">
                                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full
                                                                 bg-slate-100 text-slate-700 text-xs font-bold">
                                                        {{ $course->credit_units }}
                                                    </span>
                                                </td>

                                                {{-- LEC / LAB chip --}}
                                                <td class="px-4 py-3 text-center">
                                                    @if ($course->has_lec_lab)
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                                                     bg-blue-50 text-blue-700 text-[10px] font-bold
                                                                     ring-1 ring-blue-200/60 whitespace-nowrap">
                                                            <span class="w-1 h-1 rounded-full bg-blue-500"></span> LEC+LAB
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                                                     bg-emerald-50 text-emerald-700 text-[10px] font-bold
                                                                     ring-1 ring-emerald-200/60">
                                                            <span class="w-1 h-1 rounded-full bg-emerald-500"></span> LEC
                                                        </span>
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
