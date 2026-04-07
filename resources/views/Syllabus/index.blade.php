@extends('layouts.app')

@section('content')

    <x-page-header
        icon="bx-book-open"
        title="My Syllabi"
        desc="Manage and continue working on your course syllabi" />

    @php
        $draftSyllabi = $syllabi->filter(fn ($s) => $s->status === 'draft');
        $underReviewSyllabi = $syllabi->filter(fn ($s) => $s->status === 'under_review');
        $forRevisionSyllabi = $syllabi->filter(fn ($s) => $s->status === 'for_revision');
        $approvedSyllabi = $syllabi->filter(fn ($s) => $s->status === 'approved');
        $tabs = [
            ['id' => 'draft', 'label' => 'Draft'],
            ['id' => 'under_review', 'label' => 'Under Review'],
            ['id' => 'for_revision', 'label' => 'For Revision'],
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
                <div class="flex flex-col rounded-xl bg-white border border-[#e2e8f0] overflow-hidden transition-shadow"
                     style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">

                    <div class="px-4 py-3 bg-[#f0fdf4] border-b border-[#bbf7d0]">
                        <h3 class="font-bold text-[#0f172a] font-mono text-[15px]">{{ $syllabus->course->course_code }}</h3>
                        <p class="text-[13px] text-[#475569] mt-0.5 leading-relaxed">
                            {{ Str::limit($syllabus->course->course_title, 55) }}
                        </p>
                    </div>

                    <div class="flex-1 p-4 space-y-2.5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-[13px] text-[#94a3b8]">
                                Step {{ array_search($syllabus->current_step, array_keys($syllabus->getWizardSteps())) + 1 }}
                                / {{ count($syllabus->getWizardSteps()) }}
                            </span>
                            <x-feedback-status.status-indicator
                                :variant="$syllabus->status === 'draft' ? 'amber' : 'brand'">
                                {{ ucfirst($syllabus->status) }}
                            </x-feedback-status.status-indicator>
                        </div>
                        @if ($syllabus->academic_calendar)
                            <div class="flex items-center gap-1.5 text-[13px] text-[#475569]">
                                <i class="bx bx-calendar text-[#94a3b8]"></i>
                                {{ $syllabus->academic_calendar->academic_year }}
                            </div>
                        @endif
                        <div class="flex items-start gap-1.5 text-[13px] text-[#475569]">
                            <i class="bx bx-book text-[#94a3b8] mt-0.5 shrink-0"></i>
                            <span class="leading-relaxed">{{ $syllabus->course->program->name }}</span>
                        </div>
                    </div>

                    <div class="p-3 pt-0 flex gap-2">
                        <x-button href="{{ route('syllabus.wizard', ['syllabusId' => $syllabus->id]) }}"
                            variant="primary" class="flex-1 justify-center">
                            {{ $syllabus->status === 'draft' ? 'Continue' : 'View' }}
                        </x-button>
                        <x-button href="{{ route('syllabus.preview.complete', ['syllabus' => $syllabus->id]) }}"
                            variant="cancel" class="flex-1 justify-center">Preview</x-button>
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

            <x-slot name="slot_under_review">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                    @forelse ($underReviewSyllabi as $syllabus)
                        <div class="flex flex-col rounded-xl bg-white border border-[#e2e8f0] overflow-hidden transition-shadow"
                             style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">
                            <div class="px-4 py-3 bg-[#f0fdf4] border-b border-[#bbf7d0]">
                                <h3 class="font-bold text-[#0f172a] font-mono text-[15px]">{{ $syllabus->course->course_code }}</h3>
                                <p class="text-[13px] text-[#475569] mt-0.5 leading-relaxed">{{ Str::limit($syllabus->course->course_title, 55) }}</p>
                            </div>
                            <div class="flex-1 p-4 space-y-2.5">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-[13px] text-[#94a3b8]">Under review</span>
                                    <x-feedback-status.status-indicator variant="brand">Under Review</x-feedback-status.status-indicator>
                                </div>
                                @if ($syllabus->academic_calendar)
                                    <div class="flex items-center gap-1.5 text-[13px] text-[#475569]">
                                        <i class="bx bx-calendar text-[#94a3b8]"></i>
                                        {{ $syllabus->academic_calendar->academic_year }}
                                    </div>
                                @endif
                                <div class="flex items-start gap-1.5 text-[13px] text-[#475569]">
                                    <i class="bx bx-book text-[#94a3b8] mt-0.5 shrink-0"></i>
                                    <span class="leading-relaxed">{{ $syllabus->course->program->name }}</span>
                                </div>
                            </div>
                            <div class="p-3 pt-0 flex gap-2">
                                <x-button href="{{ route('syllabus.wizard', ['syllabusId' => $syllabus->id]) }}" variant="primary" class="flex-1 justify-center">View</x-button>
                                <x-button href="{{ route('syllabus.preview.complete', ['syllabus' => $syllabus->id]) }}" variant="cancel" class="flex-1 justify-center">Preview</x-button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full">
                            <x-empty-state
                                icon="bx-time-five"
                                title="No syllabi under review"
                                message="Syllabi submitted for review will appear here." />
                        </div>
                    @endforelse
                </div>
            </x-slot>

            <x-slot name="slot_for_revision">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                    @forelse ($forRevisionSyllabi as $syllabus)
                        <div class="flex flex-col rounded-xl bg-white border border-[#e2e8f0] overflow-hidden transition-shadow"
                             style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">
                            <div class="px-4 py-3 bg-[#f0fdf4] border-b border-[#bbf7d0]">
                                <h3 class="font-bold text-[#0f172a] font-mono text-[15px]">{{ $syllabus->course->course_code }}</h3>
                                <p class="text-[13px] text-[#475569] mt-0.5 leading-relaxed">{{ Str::limit($syllabus->course->course_title, 55) }}</p>
                            </div>
                            <div class="flex-1 p-4 space-y-2.5">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-[13px] text-[#94a3b8]">For revision</span>
                                    <x-feedback-status.status-indicator variant="rose">For Revision</x-feedback-status.status-indicator>
                                </div>
                                @if ($syllabus->academic_calendar)
                                    <div class="flex items-center gap-1.5 text-[13px] text-[#475569]">
                                        <i class="bx bx-calendar text-[#94a3b8]"></i>
                                        {{ $syllabus->academic_calendar->academic_year }}
                                    </div>
                                @endif
                                <div class="flex items-start gap-1.5 text-[13px] text-[#475569]">
                                    <i class="bx bx-book text-[#94a3b8] mt-0.5 shrink-0"></i>
                                    <span class="leading-relaxed">{{ $syllabus->course->program->name }}</span>
                                </div>
                            </div>
                            <div class="p-3 pt-0 flex gap-2">
                                <x-button href="{{ route('syllabus.wizard', ['syllabusId' => $syllabus->id]) }}" variant="primary" class="flex-1 justify-center">Continue</x-button>
                                <x-button href="{{ route('syllabus.preview.complete', ['syllabus' => $syllabus->id]) }}" variant="cancel" class="flex-1 justify-center">Preview</x-button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full">
                            <x-empty-state
                                icon="bx-revision"
                                title="No syllabi for revision"
                                message="Syllabi returned by reviewers will appear here." />
                        </div>
                    @endforelse
                </div>
            </x-slot>

            <x-slot name="slot_approved">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                    @forelse ($approvedSyllabi as $syllabus)
                        <div class="flex flex-col rounded-xl bg-white border border-[#e2e8f0] overflow-hidden transition-shadow"
                             style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">
                            <div class="px-4 py-3 bg-[#f0fdf4] border-b border-[#bbf7d0]">
                                <h3 class="font-bold text-[#0f172a] font-mono text-[15px]">{{ $syllabus->course->course_code }}</h3>
                                <p class="text-[13px] text-[#475569] mt-0.5 leading-relaxed">{{ Str::limit($syllabus->course->course_title, 55) }}</p>
                            </div>
                            <div class="flex-1 p-4 space-y-2.5">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-[13px] text-[#94a3b8]">Approved</span>
                                    <x-feedback-status.status-indicator variant="brand">Approved</x-feedback-status.status-indicator>
                                </div>
                                @if ($syllabus->academic_calendar)
                                    <div class="flex items-center gap-1.5 text-[13px] text-[#475569]">
                                        <i class="bx bx-calendar text-[#94a3b8]"></i>
                                        {{ $syllabus->academic_calendar->academic_year }}
                                    </div>
                                @endif
                                <div class="flex items-start gap-1.5 text-[13px] text-[#475569]">
                                    <i class="bx bx-book text-[#94a3b8] mt-0.5 shrink-0"></i>
                                    <span class="leading-relaxed">{{ $syllabus->course->program->name }}</span>
                                </div>
                            </div>
                            <div class="p-3 pt-0 flex gap-2">
                                <x-button href="{{ route('syllabus.wizard', ['syllabusId' => $syllabus->id]) }}" variant="primary" class="flex-1 justify-center">View</x-button>
                                <x-button href="{{ route('syllabus.preview.complete', ['syllabus' => $syllabus->id]) }}" variant="cancel" class="flex-1 justify-center">Preview</x-button>
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
