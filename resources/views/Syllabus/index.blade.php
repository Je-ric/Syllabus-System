@extends('layouts.app')

@section('content')

    <x-page-header
        icon="bx-book-open"
        title="My Syllabi"
        desc="Manage and continue working on your course syllabi" />

    @php
        $draftSyllabi = $syllabi->filter(fn ($s) => $s->status !== 'approved');
        $approvedSyllabi = $syllabi->filter(fn ($s) => $s->status === 'approved');
        $tabs = [
            ['id' => 'draft', 'label' => 'Draft'],
            ['id' => 'approved', 'label' => 'Approved'],
        ];
    @endphp

    <x-panel>
        <x-navigation.tabs-modern
            :tabs="$tabs"
            :defaultTab="$tabs[0]['id'] ?? null"
            :stateKey="'syllabi-index'">
            <x-slot name="slot_draft">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
    
            {{-- Create Syllabus card --}}
            <a href="{{ route('syllabus.create') }}"
                class="group flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-300
                        min-h-56 bg-white hover:bg-emerald-50/50 hover:border-emerald-400 transition-colors shadow-sm">
                <div class="flex flex-col items-center text-center px-4">
                    <div class="w-12 h-12 rounded-full bg-slate-100 group-hover:bg-emerald-100 flex items-center justify-center transition-colors mb-3">
                        <i class="bx bx-plus text-2xl text-slate-400 group-hover:text-emerald-600 transition-colors"></i>
                    </div>
                    <span class="text-sm font-semibold text-slate-500 group-hover:text-emerald-600 transition-colors">
                        Create Syllabus
                    </span>
                </div>
            </a>
    
            {{-- Existing syllabi --}}
            @forelse ($draftSyllabi as $syllabus)
                <div class="flex flex-col rounded-2xl bg-white border border-slate-200 shadow-sm hover:shadow-md transition-shadow overflow-hidden">
    
                    {{-- Card header — bg-gradient-to-r (was bg-linear-to-r, invalid Tailwind) --}}
                    <div class="px-4 py-3 bg-linear-to-r from-slate-50 to-emerald-50/60 border-b border-slate-200">
                        <h3 class="font-bold text-slate-800 font-mono">
                            {{ $syllabus->course->course_code }}
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                            {{ Str::limit($syllabus->course->course_title, 55) }}
                        </p>
                    </div>
    
                    {{-- Card body --}}
                    <div class="flex-1 p-4 space-y-2.5 text-sm text-slate-600">
    
                        {{-- Step progress + status --}}
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs text-slate-400">
                                Step
                                {{ array_search($syllabus->current_step, array_keys($syllabus->getWizardSteps())) + 1 }}
                                / {{ count($syllabus->getWizardSteps()) }}
                            </span>
    
                            {{--
                                Status badge — amber for draft, emerald for published.
                                Was: yellow-*/green-* (wrong app tokens)
                                Now: amber-*/emerald-* (consistent with status-indicator component)
                            --}}
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[0.65rem] font-semibold ring-1
                                {{ $syllabus->status === 'draft'
                                    ? 'bg-amber-50 text-amber-700 ring-amber-200'
                                    : 'bg-emerald-50 text-emerald-700 ring-emerald-200' }}">
                                {{ ucfirst($syllabus->status) }}
                            </span>
                        </div>
    
                        @if ($syllabus->academic_calendar)
                            <div class="flex items-center gap-1.5 text-xs text-slate-500">
                                <i class="bx bx-calendar text-slate-400"></i>
                                {{ $syllabus->academic_calendar->academic_year }}
                            </div>
                        @endif
    
                        <div class="flex items-start gap-1.5 text-xs text-slate-500">
                            <i class="bx bx-book text-slate-400 mt-0.5 shrink-0"></i>
                            <span class="leading-relaxed">{{ $syllabus->course->program->name }}</span>
                        </div>
                    </div>
    
                    {{-- Card actions --}}
                    <div class="p-3 pt-0 flex gap-2">
                        <x-button
                            href="{{ route('syllabus.wizard', ['syllabusId' => $syllabus->id]) }}"
                            variant="primary"
                            class="flex-1 justify-center">
                            {{ $syllabus->status === 'draft' ? 'Continue' : 'View' }}
                        </x-button>

                        <x-button
                            href="{{ route('syllabus.preview.complete', ['syllabus' => $syllabus->id]) }}"
                            variant="cancel"
                            class="flex-1 justify-center">
                            Preview
                        </x-button>
                    </div>
    
                </div>
            @empty
                {{-- Spans all columns in the grid --}}
                <div class="col-span-full">
                    <x-empty-state
                        icon="bx-file-blank"
                        title="No draft syllabi"
                        message="Create a new syllabus to start working on it.">
                    </x-empty-state>
                </div>
            @endforelse
    
        </div>
            </x-slot>

            <x-slot name="slot_approved">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                    @forelse ($approvedSyllabi as $syllabus)
                        <div class="flex flex-col rounded-2xl bg-white border border-slate-200 shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                            <div class="px-4 py-3 bg-linear-to-r from-slate-50 to-emerald-50/60 border-b border-slate-200">
                                <h3 class="font-bold text-slate-800 font-mono">
                                    {{ $syllabus->course->course_code }}
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                                    {{ Str::limit($syllabus->course->course_title, 55) }}
                                </p>
                            </div>

                            <div class="flex-1 p-4 space-y-2.5 text-sm text-slate-600">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-xs text-slate-400">Approved</span>
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[0.65rem] font-semibold ring-1 bg-emerald-50 text-emerald-700 ring-emerald-200">
                                        Approved
                                    </span>
                                </div>

                                @if ($syllabus->academic_calendar)
                                    <div class="flex items-center gap-1.5 text-xs text-slate-500">
                                        <i class="bx bx-calendar text-slate-400"></i>
                                        {{ $syllabus->academic_calendar->academic_year }}
                                    </div>
                                @endif

                                <div class="flex items-start gap-1.5 text-xs text-slate-500">
                                    <i class="bx bx-book text-slate-400 mt-0.5 shrink-0"></i>
                                    <span class="leading-relaxed">{{ $syllabus->course->program->name }}</span>
                                </div>
                            </div>

                            <div class="p-3 pt-0 flex gap-2">
                                <x-button
                                    href="{{ route('syllabus.wizard', ['syllabusId' => $syllabus->id]) }}"
                                    variant="primary"
                                    class="flex-1 justify-center">
                                    View
                                </x-button>

                                <x-button
                                    href="{{ route('syllabus.preview.complete', ['syllabus' => $syllabus->id]) }}"
                                    variant="cancel"
                                    class="flex-1 justify-center">
                                    Preview
                                </x-button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full">
                            <x-empty-state
                                icon="bx-check-circle"
                                title="No approved syllabi"
                                message="Approved syllabi will appear here once completed." />
                        </div>
                    @endforelse
                </div>
            </x-slot>

        </x-navigation.tabs-modern>
    </x-panel>

@endsection
