@extends('layouts.app')

@section('content')

    <x-layout.page-header
        icon="bx-book-open"
        title="My Syllabi"
        desc="Manage and continue working on your course syllabi">
        <x-ui.help-trigger />
        <x-ui.button href="{{ route('syllabus.create') }}" variant="add-button">
            <i class="bx bx-plus text-base leading-none"></i> Create Syllabus
        </x-ui.button>
    </x-layout.page-header>

    <x-layout.help-panel module="syllabus-index" />

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

        $statusConfig = [
            'draft' => [
                'bar'           => 'bg-[#d97706]',
                'header_bg'     => 'bg-[#fffbeb]',
                'header_border' => 'border-[#fde68a]',
                'badge_variant' => 'amber',
                'badge_label'   => 'Draft',
                'action_label'  => 'Continue',
                'progress_color'=> 'bg-[#d97706]',
                'empty_icon'    => 'bx-file-blank',
                'empty_title'   => 'No draft syllabi',
                'empty_message' => 'Create a new syllabus to start working on it.',
                'show_create'   => true,
            ],
            'under_review' => [
                'bar'           => 'bg-[#2563eb]',
                'header_bg'     => 'bg-[#eff6ff]',
                'header_border' => 'border-[#bfdbfe]',
                'badge_variant' => 'blue',
                'badge_label'   => 'Under Review',
                'action_label'  => 'View',
                'progress_color'=> 'bg-[#2563eb]',
                'empty_icon'    => 'bx-time-five',
                'empty_title'   => 'No syllabi under review',
                'empty_message' => 'Syllabi submitted for review will appear here.',
                'show_create'   => false,
            ],
            'for_revision' => [
                'bar'           => 'bg-[#e11d48]',
                'header_bg'     => 'bg-[#fff1f2]',
                'header_border' => 'border-[#fecdd3]',
                'badge_variant' => 'rose',
                'badge_label'   => 'For Revision',
                'action_label'  => 'Continue',
                'progress_color'=> 'bg-[#e11d48]',
                'empty_icon'    => 'bx-edit',
                'empty_title'   => 'No syllabi for revision',
                'empty_message' => 'Syllabi returned by reviewers will appear here.',
                'show_create'   => false,
            ],
            'approved' => [
                'bar'           => 'bg-[#16a34a]',
                'header_bg'     => 'bg-[#f0fdf4]',
                'header_border' => 'border-[#d1fae5]',
                'badge_variant' => 'emerald',
                'badge_label'   => 'Approved',
                'action_label'  => 'View',
                'progress_color'=> 'bg-[#16a34a]',
                'empty_icon'    => 'bx-check-circle',
                'empty_title'   => 'No approved syllabi',
                'empty_message' => 'Approved syllabi will appear here once completed.',
                'show_create'   => false,
            ],
        ];
    @endphp

    <x-layout.panel>
        <x-navigation.tabs-modern
            :tabs="$tabs"
            :defaultTab="$tabs[0]['id'] ?? null"
            :stateKey="'syllabi-index'">

            @foreach ($tabs as $tab)
                @php
                    $key   = $tab['id'];
                    $cfg   = $statusConfig[$key];
                    $items = $grouped[$key];
                @endphp

                <x-slot :name="'slot_' . $key">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">

                        @forelse ($items as $syllabus)
                            <div class="flex flex-col rounded-[16px] bg-white border border-[#e4e4e7] overflow-hidden
                                        hover:border-[#d4d4d8] transition-all duration-150"
                                 style="box-shadow: 0 1px 6px rgba(0,0,0,0.05);">

                                {{-- Colored top bar --}}
                                <div class="h-[3px] w-full {{ $cfg['bar'] }}"></div>

                                {{-- Status-tinted header --}}
                                <div class="px-4 py-3 {{ $cfg['header_bg'] }} border-b {{ $cfg['header_border'] }}">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <h3 class="font-bold text-[#09090b] font-mono text-[13px] leading-none mb-1">
                                                {{ $syllabus->course->course_code }}
                                            </h3>
                                            <p class="text-[12px] text-[#71717a] leading-relaxed">
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
                                                <span class="text-[11px] text-[#a1a1aa] font-medium">
                                                    Step {{ $current }} of {{ $total }}
                                                </span>
                                                <span class="text-[11px] text-[#a1a1aa]">{{ $pct }}%</span>
                                            </div>
                                            <div class="h-1 rounded-full bg-[#f4f4f5] overflow-hidden">
                                                <div class="h-full rounded-full {{ $cfg['progress_color'] }} transition-all"
                                                     style="width: {{ $pct }}%"></div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Academic year --}}
                                    @if ($syllabus->academic_calendar)
                                        <div class="flex items-center gap-1.5 text-[12px] text-[#71717a]">
                                            <i class="bx bx-calendar text-[#a1a1aa] text-sm leading-none"></i>
                                            {{ $syllabus->academic_calendar->academic_year }}
                                        </div>
                                    @endif

                                    {{-- Program --}}
                                    <div class="flex items-start gap-1.5 text-[12px] text-[#71717a]">
                                        <i class="bx bx-book text-[#a1a1aa] text-sm mt-0.5 shrink-0 leading-none"></i>
                                        <span class="leading-relaxed">{{ $syllabus->course->program->name }}</span>
                                    </div>

                                </div>

                                {{-- Actions --}}
                                <div class="px-3 pb-3 pt-1 flex gap-2">
                                    <x-ui.button
                                        href="{{ route('syllabus.wizard', ['syllabusId' => $syllabus->id]) }}"
                                        variant="primary"
                                        class="flex-1 justify-center">
                                        {{ $cfg['action_label'] }}
                                    </x-ui.button>
                                    <x-ui.button
                                        href="{{ route('syllabus.preview.complete', ['syllabus' => $syllabus->id]) }}"
                                        variant="cancel"
                                        class="flex-1 justify-center">
                                        Preview
                                    </x-ui.button>
                                    @if ($key === 'draft')
                                        <form method="POST"
                                              action="{{ route('syllabus.destroy', $syllabus->id) }}"
                                              x-data
                                              x-on:submit.prevent="
                                                  if (confirm('Delete this draft syllabus? This cannot be undone.')) $el.submit();
                                              ">
                                            @csrf @method('DELETE')
                                            <x-ui.button type="submit" variant="danger" title="Delete draft">
                                                <i class="bx bx-trash text-base leading-none"></i>
                                            </x-ui.button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                        @empty
                            <div class="col-span-full">
                                <x-feedback-status.empty-state
                                    :icon="$cfg['empty_icon']"
                                    :title="$cfg['empty_title']"
                                    :message="$cfg['empty_message']">

                                    @if ($cfg['show_create'])
                                        <x-ui.button href="{{ route('syllabus.create') }}" variant="add-button">
                                            <i class="bx bx-plus text-base"></i> Create Syllabus
                                        </x-ui.button>
                                    @endif

                                </x-feedback-status.empty-state>
                            </div>
                        @endforelse

                    </div>
                </x-slot>
            @endforeach

        </x-navigation.tabs-modern>
    </x-layout.panel>

@endsection
