@extends('layouts.app')

@section('content')
    <x-page-header
        icon="bx-book-add"
        title="Create Syllabus"
        desc="Select a program and course to begin creating a syllabus" />

    <x-panel>
        {{-- Program selector --}}
        <div class="border border-slate-200/80 rounded-2xl p-5 mb-6 bg-white/90 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 mb-3">Select Program</p>
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
                                        <th class="px-4 py-3 text-center font-semibold w-24">Type</th>
                                        <th class="px-4 py-3 text-center font-semibold">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($courses as $course)
                                        @php $hasPo = $course->programOutcomes()->exists(); @endphp
                                        <tr class="hover:bg-emerald-50/30 transition-colors group">

                                            {{-- Course code + title --}}
                                            <td class="px-5 py-3">
                                                <span class="font-mono font-semibold text-slate-700 text-xs">
                                                    {{ $course->course_code }}
                                                </span>
                                                <span class="text-slate-400 mx-1">—</span>
                                                <span class="text-slate-600">{{ $course->course_title }}</span>
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

                                            {{-- Action --}}
                                            <td class="px-4 py-3 text-center align-middle">
                                                @if (! $hasPo)
                                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg
                                                                 bg-amber-50 text-amber-700 text-xs font-medium
                                                                 ring-1 ring-amber-200/60">
                                                        <i class="bx bx-error-circle"></i> No PO mapped
                                                    </span>
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
