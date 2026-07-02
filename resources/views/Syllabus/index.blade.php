@extends('layouts.app')

@section('content')

    <x-page-header
        icon="bx-book-open"
        title="My Syllabi"
        desc="Manage and continue working on your course syllabi">
        <x-button href="{{ route('syllabus.create') }}" variant="add-button">
            <i class="bx bx-plus text-base leading-none"></i> Create Syllabus
        </x-button>
    </x-page-header>

    @php
        $grouped = [
            'draft'        => $syllabi->filter(fn($s) => $s->status === 'draft'),
            'under_review' => $syllabi->filter(fn($s) => $s->status === 'under_review'),
            'for_revision' => $syllabi->filter(fn($s) => $s->status === 'for_revision'),
            'approved'     => $syllabi->filter(fn($s) => $s->status === 'approved'),
        ];

        $tabs = [
            ['id' => 'draft',        'label' => 'Draft'],
            ['id' => 'under_review', 'label' => 'Under Review'],
            ['id' => 'for_revision', 'label' => 'For Revision'],
            ['id' => 'approved',     'label' => 'Approved'],
        ];

        /**
         * Per-status visual tokens.
         * header_bg / header_border  → card top strip
         * badge_variant              → your <x-feedback-status.status-indicator> prop
         * action_label               → primary button text
         * empty_icon / empty_title / empty_message → empty-state copy
         */
        $statusConfig = [
            'draft' => [
                'header_bg'      => 'bg-amber-50',
                'header_border'  => 'border-amber-200',
                'badge_variant'  => 'amber',
                'badge_label'    => 'Draft',
                'action_label'   => 'Continue',
                'empty_icon'     => 'bx-file-blank',
                'empty_title'    => 'No draft syllabi',
                'empty_message'  => 'Create a new syllabus to start working on it.',
                'show_create'    => true,
            ],
            'under_review' => [
                'header_bg'      => 'bg-blue-50',
                'header_border'  => 'border-blue-200',
                'badge_variant'  => 'blue',
                'badge_label'    => 'Under Review',
                'action_label'   => 'View',
                'empty_icon'     => 'bx-time-five',
                'empty_title'    => 'No syllabi under review',
                'empty_message'  => 'Syllabi submitted for review will appear here.',
                'show_create'    => false,
            ],
            'for_revision' => [
                'header_bg'      => 'bg-rose-50',
                'header_border'  => 'border-rose-200',
                'badge_variant'  => 'rose',
                'badge_label'    => 'For Revision',
                'action_label'   => 'Continue',
                'empty_icon'     => 'bx-edit',
                'empty_title'    => 'No syllabi for revision',
                'empty_message'  => 'Syllabi returned by reviewers will appear here.',
                'show_create'    => false,
            ],
            'approved' => [
                'header_bg'      => 'bg-emerald-50',
                'header_border'  => 'border-emerald-200',
                'badge_variant'  => 'emerald',
                'badge_label'    => 'Approved',
                'action_label'   => 'View',
                'empty_icon'     => 'bx-check-circle',
                'empty_title'    => 'No approved syllabi',
                'empty_message'  => 'Approved syllabi will appear here once completed.',
                'show_create'    => false,
            ],
        ];
    @endphp

    <x-panel>
        <x-navigation.tabs-modern
            :tabs="$tabs"
            :defaultTab="$tabs[0]['id'] ?? null"
            :stateKey="'syllabi-index'">

            @foreach ($tabs as $tab)
                @php
                    $key      = $tab['id'];
                    $cfg      = $statusConfig[$key];
                    $items    = $grouped[$key];
                @endphp

                <x-slot :name="'slot_' . $key">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">

                        @forelse ($items as $syllabus)
                            <div class="flex flex-col rounded-xl bg-white border border-slate-200
                                        overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-150">

                                {{-- Status-tinted header strip --}}
                                <div class="px-4 py-3 {{ $cfg['header_bg'] }} border-b {{ $cfg['header_border'] }}">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <h3 class="font-bold text-slate-900 font-mono text-[14px] leading-none mb-1">
                                                {{ $syllabus->course->course_code }}
                                            </h3>
                                            <p class="text-[12px] text-slate-500 leading-relaxed">
                                                {{ Str::limit($syllabus->course->course_title, 52) }}
                                            </p>
                                        </div>
                                        <x-feedback-status.status-indicator
                                            :variant="$cfg['badge_variant']"
                                            class="shrink-0 mt-0.5">
                                            {{ $cfg['badge_label'] }}
                                        </x-feedback-status.status-indicator>
                                    </div>
                                </div>

                                {{-- Card body --}}
                                <div class="flex-1 px-4 py-3 space-y-2">

                                    {{-- Progress (draft only) --}}
                                    @if ($key === 'draft')
                                        @php
                                            $steps    = $syllabus->getWizardSteps();
                                            $stepKeys = array_keys($steps);
                                            $current  = array_search($syllabus->current_step, $stepKeys) + 1;
                                            $total    = count($steps);
                                            $pct      = round(($current / $total) * 100);
                                        @endphp
                                        <div>
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-[11px] text-slate-400 font-medium">
                                                    Step {{ $current }} of {{ $total }}
                                                </span>
                                                <span class="text-[11px] text-slate-400">{{ $pct }}%</span>
                                            </div>
                                            <div class="h-1 rounded-full bg-slate-100 overflow-hidden">
                                                <div class="h-full rounded-full bg-amber-400 transition-all"
                                                     style="width: {{ $pct }}%"></div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Academic year --}}
                                    @if ($syllabus->academic_calendar)
                                        <div class="flex items-center gap-1.5 text-[12px] text-slate-500">
                                            <i class="bx bx-calendar text-slate-400 text-sm"></i>
                                            {{ $syllabus->academic_calendar->academic_year }}
                                        </div>
                                    @endif

                                    {{-- Program --}}
                                    <div class="flex items-start gap-1.5 text-[12px] text-slate-500">
                                        <i class="bx bx-book text-slate-400 text-sm mt-0.5 shrink-0"></i>
                                        <span class="leading-relaxed">{{ $syllabus->course->program->name }}</span>
                                    </div>

                                </div>

                                {{-- Actions --}}
                                <div class="px-3 pb-3 pt-1 flex gap-2">
                                    <x-button
                                        href="{{ route('syllabus.wizard', ['syllabusId' => $syllabus->id]) }}"
                                        variant="primary"
                                        class="flex-1 justify-center">
                                        {{ $cfg['action_label'] }}
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
                                    :icon="$cfg['empty_icon']"
                                    :title="$cfg['empty_title']"
                                    :message="$cfg['empty_message']">

                                    @if ($cfg['show_create'])
                                        <a href="{{ route('syllabus.create') }}"
                                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg
                                                  bg-emerald-600 text-white text-sm font-medium
                                                  hover:bg-emerald-700 transition-colors shadow-sm">
                                            <i class="bx bx-plus text-base"></i>
                                            Create Syllabus
                                        </a>
                                    @endif

                                </x-empty-state>
                            </div>
                        @endforelse

                    </div>
                </x-slot>
            @endforeach

        </x-navigation.tabs-modern>
    </x-panel>

@endsection