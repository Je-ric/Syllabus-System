@extends('layouts.app')

@section('content')
    <x-page-header
        icon="bx-book-add"
        title="Create Syllabus"
        desc="Step 1: Select program, Step 2: Choose course, Step 3: Fill details" />

    <x-panel>
        <div class="border border-slate-200/80 rounded-2xl p-6 bg-white/90 shadow-sm">
            <h2 class="text-sm uppercase tracking-[0.25em] text-slate-500 mb-4">Select Program</h2>
            <livewire:programs.program-selector 
                :program-id="optional($program)?->id" 
                redirect-route="syllabus.create" 
                :autoRedirect="true" />
        </div>
    
        @if ($program)
            <div class="mb-6 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-slate-800">
                    Courses in <span class="text-emerald-700">{{ $program->name }}</span>
                </h2>
            </div>
    
            @forelse ($groupedCourses as $year => $semesters)
                < class="mb-8">
                    <h3 class="text-sm uppercase tracking-[0.25em] text-slate-500 mb-4 border-b border-slate-200 pb-2">
                        Year {{ $year ?? 'N/A' }}
                    </h3>
    
                    @forelse ($semesters as $semester => $courses)
                        <div class="mb-6 bg-white/90 border border-slate-200 rounded-2xl p-4 shadow-sm">
                            <h4 class="font-medium text-slate-700 mb-3 border-b border-slate-200 pb-1">
                                Semester {{ $semester ?? 'N/A' }}
                            </h4>
    
                            <x-table.container>
                                <x-table.table>
                                    <x-table.head>
                                        <tr>
                                            <x-table.th class="px-4 py-2">Code</x-table.th>
                                            <x-table.th class="px-4 py-2">Course Title</x-table.th>
                                            <x-table.th align="center" class="px-4 py-2">Units</x-table.th>
                                            <x-table.th align="center" class="px-4 py-2">Type</x-table.th>
                                            <x-table.th align="center" class="px-4 py-2">Action</x-table.th>
                                        </tr>
                                    </x-table.head>
    
                                    <x-table.body>
                                        @foreach ($courses as $course)
                                            <x-table.row striped hover class="border-b border-slate-200">
                                                <x-table.td class="px-4 py-2 font-mono font-semibold text-slate-700">
                                                    {{ $course->course_code }}
                                                </x-table.td>
                                                <x-table.td class="px-4 py-2 text-slate-700">
                                                    {{ $course->course_title }}
                                                </x-table.td>
                                                <x-table.td align="center" class="px-4 py-2 text-slate-700">
                                                    {{ $course->credit_units }}
                                                </x-table.td>
                                                <x-table.td align="center" class="px-4 py-2">
                                                    @if ($course->has_lec_lab)
                                                        <x-feedback-status.status-indicator status="lec_lab" label="LEC+LAB" />
                                                    @else
                                                        <x-feedback-status.status-indicator status="lec" label="LEC" />
                                                    @endif
                                                </x-table.td>
                                                <x-table.td align="center" class="px-4 py-2">
                                                    <x-button href="{{ route('syllabus.form', $course->id) }}"
                                                        variant="table-confirm">
                                                        Create Syllabus
                                                    </x-button>
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
                            title="No courses found"
                            message="There are no courses listed for this semester. Please contact the administrator to add courses." />
                    @endforelse
                </div>
            @empty
                <x-empty-state
                    icon="bx-book"
                    title="No courses found"
                    message="There are no courses listed for this program. Please contact the administrator to add courses." />
            @endforelse
        @else
            <x-empty-state
                icon="bx-book"
                title="No program selected"
                message="Please select a program from the dropdown above to view its courses." />
        @endif
    </x-panel>

@endsection
