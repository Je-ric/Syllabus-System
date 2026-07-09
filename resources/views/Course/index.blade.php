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
    $showArchived = request()->boolean('archived');
@endphp

    <x-page-header
        icon="bx-book"
        title="Manage Courses"
        desc="View and manage courses by program, year level, and semester">
        <x-ui.help-trigger />
        @if ($program)
            <x-ui.button
                href="{{ route('courses.create', ['program_id' => $program->id]) }}"
                variant="add-button">
                <i class="bx bx-plus"></i> Add Course
            </x-ui.button>
        @endif
    </x-page-header>

    <x-help-panel module="courses" />

    <x-panel>

        {{-- Program selector --}}
        <x-card-section title="Select Program" icon="bx-network-chart" class="mb-5">
            <livewire:programs.program-selector
                :program-id="optional($program)?->id"
                redirect-route="courses.index"
                :autoRedirect="true" />
        </x-card-section>

        @if ($noAssignment ?? false)
            <x-feedback-status.alert type="warning" title="No department assigned"
                message="You have the Chair role but are not assigned to any department. Contact an administrator to be assigned." />
        @elseif ($program)

            {{-- Active / Archived tabs + PO offcanvas trigger --}}
            <div x-data="{ poDrawer: false }">
                <x-navigation.link-tabs
                    :tabs="[
                        ['id' => 'active',   'label' => 'Active',   'icon' => 'bx-book',    'href' => route('courses.index', ['program_id' => $program->id])],
                        ['id' => 'archived', 'label' => 'Archived', 'icon' => 'bx-archive', 'href' => route('courses.index', ['program_id' => $program->id, 'archived' => 1])],
                    ]"
                    :active="$showArchived ? 'archived' : 'active'">
                    @if ($program->outcomes->isNotEmpty())
                        <x-slot name="actions">
                            <x-ui.button type="button" variant="sm-info" x-on:click="poDrawer = true">
                                <i class="bx bx-book-open text-sm leading-none"></i> Program Outcomes
                            </x-ui.button>
                        </x-slot>
                    @endif
                </x-navigation.link-tabs>

                @if ($program->outcomes->isNotEmpty())
                    @include('Course.offcanvasReference')
                @endif
            </div>

            {{-- Curriculum map --}}
            @forelse ($groupedCourses as $year => $semesters)

                {{-- Year heading --}}
                <div class="flex items-center gap-3 mb-3 mt-5 first:mt-0">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full
                                 bg-[#16a34a] text-white text-[12px] font-bold shrink-0">
                        {{ $year ?? '?' }}
                    </span>
                    <h3 class="text-[12px] font-bold text-[#09090b] uppercase tracking-[0.1em]">
                        Year {{ $year ?? 'N/A' }}
                    </h3>
                    <div class="flex-1 h-px bg-[#e4e4e7]"></div>
                </div>

                @forelse ($semesters as $semester => $courses)
                    <div class="mb-4 rounded-[16px] border border-[#e4e4e7] bg-white overflow-hidden"
                         style="box-shadow: 0 1px 6px rgba(0,0,0,0.04);">

                        {{-- Semester sub-header --}}
                        <div class="px-5 py-2.5 border-b border-[#e4e4e7] bg-[#f4f4f5] flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#16a34a] shrink-0"></span>
                            <h4 class="text-[11px] font-bold text-[#52525b] uppercase tracking-[0.1em]">
                                Semester {{ $semester ?? 'N/A' }}
                            </h4>
                            <span class="ml-auto text-[12px] text-[#a1a1aa]">
                                {{ count($courses) }} course{{ count($courses) !== 1 ? 's' : '' }}
                            </span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm border-collapse">
                                <thead>
                                    <tr class="bg-[#fafafa] border-b border-[#e4e4e7]">
                                        <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-[0.1em] text-[#71717a]">Course</th>
                                        <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-[0.1em] text-[#71717a] w-16">Units</th>
                                        <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-[0.1em] text-[#71717a] w-24">Type</th>
                                        <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-[0.1em] text-[#71717a] w-32">Class Hours</th>
                                        @foreach ($program->outcomes as $outcome)
                                            <th class="px-2 py-3 text-center text-[11px] font-bold uppercase tracking-[0.1em] text-[#71717a] w-14">
                                                {{ $outcome->po_code }}
                                            </th>
                                        @endforeach
                                        <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-[0.1em] text-[#71717a] w-28">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#f4f4f5]">
                                    @foreach ($courses as $course)
                                        @php $modalCourses->push($course); @endphp
                                        <tr class="hover:bg-[#fafafa] transition-colors">

                                            <td class="px-5 py-3 text-[13px] text-[#18181b]">
                                                {{ $course->course_code }} — {{ $course->course_title }}
                                            </td>

                                            {{-- Units --}}
                                            <td class="px-4 py-3 text-center">
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full
                                                             bg-[#f4f4f5] text-[#18181b] text-[12px] font-bold border border-[#e4e4e7]">
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

                                            {{-- Class Hours --}}
                                            <td class="px-4 py-3 text-center">
                                                <div class="flex flex-col items-center gap-0.5 text-[12px]">
                                                    @if ($course->lec_class_hours)
                                                        <span class="text-[#166534] font-medium">LEC: {{ $course->lec_class_hours }}</span>
                                                    @endif
                                                    @if ($course->has_lec_lab && $course->lab_class_hours)
                                                        <span class="text-[#1e40af] font-medium">LAB: {{ $course->lab_class_hours }}</span>
                                                    @endif
                                                    @if (!$course->lec_class_hours && !$course->lab_class_hours)
                                                        <span class="text-[#d4d4d8]">—</span>
                                                    @endif
                                                </div>
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
                                                        <span class="text-[#e4e4e7] text-xs select-none">—</span>
                                                    @endif
                                                </td>
                                            @endforeach

                                            {{-- Actions --}}
                                            <td class="px-4 py-3 text-center">
                                                <div class="inline-flex items-center gap-1">
                                                    @if (!$showArchived)
                                                        <x-ui.button href="{{ route('courses.edit', $course->id) }}" variant="table-edit" title="Edit course">
                                                            <i class="bx bx-edit"></i>
                                                        </x-ui.button>
                                                        <x-ui.button type="button" variant="table-view"
                                                            onclick="document.getElementById('viewCourseModal_{{ $course->id }}').showModal()"
                                                            title="View details">
                                                            <i class="bx bx-show"></i>
                                                        </x-ui.button>
                                                        <x-ui.button type="button" variant="table-manage"
                                                            onclick="document.getElementById('archiveCourseModal_{{ $course->id }}').showModal()"
                                                            title="Archive course">
                                                            <i class="bx bx-archive"></i>
                                                        </x-ui.button>
                                                        @if ($canDelete)
                                                            <x-ui.button type="button" variant="table-danger"
                                                                onclick="document.getElementById('deleteCourseModal_{{ $course->id }}').showModal()"
                                                                title="Delete course">
                                                                <i class="bx bx-trash"></i>
                                                            </x-ui.button>
                                                        @endif
                                                    @else
                                                        <form action="{{ route('courses.restore', $course->id) }}" method="POST">
                                                            @csrf
                                                            <x-ui.button type="submit" variant="table-restore" title="Restore course">
                                                                <i class="bx bx-undo"></i> Restore
                                                            </x-ui.button>
                                                        </form>
                                                        @if ($canDelete)
                                                            <x-ui.button type="button" variant="table-danger"
                                                                onclick="document.getElementById('deleteCourseModal_{{ $course->id }}').showModal()"
                                                                title="Delete course">
                                                                <i class="bx bx-trash"></i>
                                                            </x-ui.button>
                                                        @endif
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
                    <x-feedback-status.empty-state icon="bx-book" title="No courses this semester"
                        message="No courses have been added for this semester yet." />
                @endforelse
            @empty
                <x-feedback-status.empty-state icon="bx-book-open" title="No courses found"
                    message="This program has no courses yet. Add the first one to get started.">
                    <x-ui.button href="{{ route('courses.create', ['program_id' => $program->id]) }}" variant="add-button">
                        <i class="bx bx-plus"></i> Add First Course
                    </x-ui.button>
                </x-feedback-status.empty-state>
            @endforelse

        @else
            <x-feedback-status.empty-state icon="bx-book-open" title="No program selected"
                message="Select a program above to view and manage its courses." />
        @endif
    </x-panel>

    {{-- Modals --}}
    @foreach ($modalCourses as $course)
        @include('Course.modals.viewCourseModal', ['course' => $course])
        @if (!$showArchived)
            @include('Course.modals.archiveCourseModal', ['course' => $course])
        @endif
        @if ($canDelete)
            @include('Course.modals.deleteCourseModal', ['course' => $course])
        @endif
    @endforeach

@endsection
