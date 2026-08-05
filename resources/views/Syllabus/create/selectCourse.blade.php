@extends('layouts.app')

@section('content')
    <x-layout.page-header
        icon="bx-book-add"
        title="Create Syllabus"
        desc="Select a program and course to begin creating a syllabus" />

    <x-layout.panel>
        {{-- Program selector --}}

        <x-layout.card-section
            title="Select Program"
            icon="bx-network-chart"
            class="mb-6">

            <livewire:programs.program-selector
                :program-id="optional($program)?->id"
                redirect-route="syllabus.create"
                :autoRedirect="true" />
        </x-layout.card-section>

        @if ($program)

            @forelse ($groupedCourses as $year => $semesters)

                {{-- Year heading --}}
                <div class="flex items-center gap-3 mb-4">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full shrink-0
                                 bg-[linear-gradient(180deg,#00C075_0%,#06754E_100%)] text-white text-[13px] font-bold">
                        {{ $year ?? '?' }}
                    </span>
                    <h3 class="text-[13px] font-bold text-[#394056] uppercase tracking-[0.12em]">
                        Year {{ $year ?? 'N/A' }}
                    </h3>
                    <div class="flex-1 h-px bg-[#E3E8EB]"></div>
                </div>

                @forelse ($semesters as $semester => $courses)
                    <div class="mb-5 rounded-[12px] border border-[#E3E8EB] bg-white overflow-hidden"
                         style="box-shadow: 0 1px 2px rgba(16,24,40,0.04), 0 1px 3px rgba(16,24,40,0.06);">

                        {{-- Semester sub-header --}}
                        <div class="px-5 py-2.5 border-b border-[#E3E8EB] bg-[#F9FAFA] flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#00965F] shrink-0"></span>
                            <h4 class="text-[11px] font-bold text-[#394056] uppercase tracking-[0.12em]">
                                Semester {{ $semester ?? 'N/A' }}
                            </h4>
                            <span class="ml-auto text-[12px] text-[#93A1AF]">
                                {{ count($courses) }} course(s)
                            </span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm border-collapse">
                                <thead>
                                    <tr class="bg-[#F1F3F5] border-b border-[#E3E8EB]">
                                        <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-[0.12em] text-[#72809E]">Course</th>
                                        <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-[0.12em] text-[#72809E] w-16">Units</th>
                                        <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-[0.12em] text-[#72809E] w-24">Type</th>
                                        <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-[0.12em] text-[#72809E] w-32">Class Hours</th>
                                        <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-[0.12em] text-[#72809E]">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#E3E8EB]">
                                    @foreach ($courses as $course)
                                        @php $hasPo = $course->programOutcomes()->exists(); @endphp
                                        <tr class="hover:bg-[#EDFFF8] transition-colors duration-150 group">

                                            {{-- Course code + title --}}
                                            <td class="px-5 py-3">
                                                <span class="font-mono font-semibold text-[#394056] text-[13px]">
                                                    {{ $course->course_code }}
                                                </span>
                                                <span class="text-[#C1C8D4] mx-1">—</span>
                                                <span class="text-[13px] text-[#72809E]">{{ $course->course_title }}</span>
                                            </td>

                                            {{-- Units --}}
                                            <td class="px-4 py-3 text-center">
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full
                                                             bg-[#D5FFF0] text-[#06754E] text-[13px] font-bold ring-1 ring-[#00965F]">
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
                                                        <span class="text-[#06754E] font-medium">LEC: {{ $course->lec_class_hours }}</span>
                                                    @endif
                                                    @if ($course->has_lec_lab && $course->lab_class_hours)
                                                        <span class="text-[#143D57] font-medium">LAB: {{ $course->lab_class_hours }}</span>
                                                    @endif
                                                    @if (!$course->lec_class_hours && !$course->lab_class_hours)
                                                        <span class="text-[#C1C8D4]">—</span>
                                                    @endif
                                                </div>
                                            </td>

                                            {{-- Action --}}
                                            <td class="px-4 py-3 text-center align-middle">
                                                @if (! $hasPo)
                                                    <x-feedback-status.status-indicator variant="amber">
                                                        <i class="bx bx-error-circle"></i> No PO mapped
                                                    </x-feedback-status.status-indicator>
                                                @else
                                                    <x-ui.button
                                                        href="{{ route('syllabus.form', $course->id) }}"
                                                        variant="table-confirm"
                                                        class="whitespace-nowrap inline-flex">
                                                        <i class="bx bx-plus"></i> Create Syllabus
                                                    </x-ui.button>
                                                @endif
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <x-feedback-status.empty-state
                        icon="bx-book"
                        title="No courses this semester"
                        message="No courses have been added for this semester yet." />
                @endforelse

            @empty
                <x-feedback-status.empty-state
                    icon="bx-book-open"
                    title="No courses found"
                    message="This program has no courses yet. Please contact the administrator to add courses." />
            @endforelse

        @else
            <x-feedback-status.empty-state
                icon="bx-book-open"
                title="No program selected"
                message="Select a program above to view its courses and begin creating a syllabus." />
        @endif
    </x-layout.panel>

@endsection
