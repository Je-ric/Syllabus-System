@extends('layouts.app')

@section('content')
    <x-page-header
        icon="bx-book-add"
        title="Create Syllabus"
        desc="Select a program and course to begin creating a syllabus" />

    <x-panel>
        {{-- Program selector --}}
        <div class="rounded-xl border border-[#e2e8f0] bg-white p-5 mb-6" style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">
            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-3">Select Program</p>
            <livewire:programs.program-selector
                :program-id="optional($program)?->id"
                redirect-route="syllabus.create"
                :autoRedirect="true" />
        </div>

        @if ($program)

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
                                        <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] w-24">Type</th>
                                        <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569]">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#e2e8f0]">
                                    @foreach ($courses as $course)
                                        @php $hasPo = $course->programOutcomes()->exists(); @endphp
                                        <tr class="hover:bg-[#f0fdf4] transition-colors group">

                                            {{-- Course code + title --}}
                                            <td class="px-5 py-3">
                                                <span class="font-mono font-semibold text-[#0f172a] text-[13px]">
                                                    {{ $course->course_code }}
                                                </span>
                                                <span class="text-[#94a3b8] mx-1">—</span>
                                                <span class="text-[13px] text-[#475569]">{{ $course->course_title }}</span>
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

                                            {{-- Action --}}
                                            <td class="px-4 py-3 text-center align-middle">
                                                @if (! $hasPo)
                                                    <x-feedback-status.status-indicator variant="amber">
                                                        <i class="bx bx-error-circle"></i> No PO mapped
                                                    </x-feedback-status.status-indicator>
                                                @else
                                                    <x-button
                                                        href="{{ route('syllabus.form', $course->id) }}"
                                                        variant="table-confirm"
                                                        class="whitespace-nowrap inline-flex">
                                                        <i class="bx bx-plus"></i> Create Syllabus
                                                    </x-button>
                                                @endif
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <x-empty-state
                        icon="bx-book"
                        title="No courses this semester"
                        message="No courses have been added for this semester yet." />
                @endforelse

            @empty
                <x-empty-state
                    icon="bx-book-open"
                    title="No courses found"
                    message="This program has no courses yet. Please contact the administrator to add courses." />
            @endforelse

        @else
            <x-empty-state
                icon="bx-book-open"
                title="No program selected"
                message="Select a program above to view its courses and begin creating a syllabus." />
        @endif
    </x-panel>

@endsection
