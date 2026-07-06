@php
    $steps = $syllabus->getWizardSteps();
    $stepsOrder = array_keys($steps);
    $currentIndex = array_search($currentStep, $stepsOrder, true);
@endphp

<div x-data="{ _navigating: false, scheduleOpen: false, calInfoOpen: false }"
    x-on:syllabus-step-changed.window="_navigating = false"
    x-on:lw-toast.window="if ($event.detail?.type === 'error') _navigating = false">

    <x-page-header icon="bx-book-open" title="{{ $syllabus->id ? 'Edit' : 'Create' }} Syllabus"
        desc="{{ $course->course_code }} — {{ $course->course_title }}">
        <x-button variant="cancel" href="{{ route('syllabus.index') }}">
            <i class="bx bx-arrow-back"></i>
            <span class="sm:hidden">Back</span>
            <span class="hidden sm:inline">Back to Syllabi</span>
        </x-button>
    </x-page-header>

    <x-panel>

        {{-- ══ Navigation overlay ══════════════════════════════════════════════ --}}
        <div x-show="_navigating" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-[#09090b]/40 backdrop-blur-[3px]"></div>
            <div class="relative flex flex-col items-center gap-5 px-10 py-8 rounded-[20px] border border-[#e4e4e7] bg-white"
                 style="width:300px; box-shadow: 0 8px 40px rgba(0,0,0,0.12);">
                <div class="relative w-14 h-14 flex items-center justify-center">
                    <svg class="absolute inset-0 animate-spin" viewBox="0 0 64 64" fill="none" style="color:#ffd700;">
                        <circle cx="32" cy="32" r="27" stroke="currentColor" stroke-width="3"
                            stroke-linecap="round" stroke-dasharray="130" stroke-dashoffset="90" />
                    </svg>
                    <svg class="absolute inset-0" viewBox="0 0 64 64" fill="none" style="color:#e4e4e7;">
                        <circle cx="32" cy="32" r="27" stroke="currentColor" stroke-width="2" />
                    </svg>
                    <img src="{{ asset('assets/CLSU-LOGO-removebg.png') }}" alt="CLSU"
                        class="relative w-8 h-8 object-contain" />
                </div>
                <div class="text-center">
                    <p class="text-[13px] font-bold text-[#09090b]">Saving changes…</p>
                    <p class="text-[11px] mt-0.5 text-[#71717a]">Please wait</p>
                </div>
                <div class="flex justify-center gap-1">
                    <div class="w-1.5 h-1.5 bg-[#16a34a] rounded-full animate-bounce"></div>
                    <div class="w-1.5 h-1.5 bg-[#16a34a] rounded-full animate-bounce" style="animation-delay:0.1s;"></div>
                    <div class="w-1.5 h-1.5 bg-[#16a34a] rounded-full animate-bounce" style="animation-delay:0.2s;"></div>
                </div>
            </div>
        </div>

        {{-- ══ Save-as-Done overlay ════════════════════════════════════════════ --}}
        <div wire:loading.class.remove="hidden" wire:target="saveAsDone"
            class="hidden fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-[#09090b]/50 backdrop-blur-[4px]"></div>
            <div class="relative flex flex-col items-center gap-5 px-10 py-8 rounded-[20px] border border-[#e4e4e7] bg-white"
                 style="width:300px; box-shadow: 0 8px 40px rgba(0,0,0,0.12);">
                <div class="relative w-14 h-14 flex items-center justify-center">
                    <svg class="absolute inset-0 animate-spin" viewBox="0 0 64 64" fill="none" style="color:#ffd700;">
                        <circle cx="32" cy="32" r="27" stroke="currentColor" stroke-width="3"
                            stroke-linecap="round" stroke-dasharray="130" stroke-dashoffset="90" />
                    </svg>
                    <svg class="absolute inset-0" viewBox="0 0 64 64" fill="none" style="color:#e4e4e7;">
                        <circle cx="32" cy="32" r="27" stroke="currentColor" stroke-width="2" />
                    </svg>
                    <svg class="absolute inset-0 animate-spin" viewBox="0 0 64 64" fill="none"
                        style="color:#16a34a; animation-direction:reverse; animation-duration:1.4s;">
                        <circle cx="32" cy="32" r="18" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-dasharray="60" stroke-dashoffset="40" />
                    </svg>
                    <img src="{{ asset('assets/CLSU-LOGO-removebg.png') }}" alt="CLSU"
                        class="relative w-8 h-8 object-contain" />
                </div>
                <div class="text-center">
                    <p class="text-[13px] font-bold text-[#09090b]">Saving Syllabus…</p>
                    <p class="text-[11px] mt-0.5 text-[#71717a]">This may take a few seconds</p>
                </div>
                <div class="w-full space-y-1.5">
                    <div class="flex items-center justify-center gap-2.5 px-3 py-2 rounded-[10px] bg-[#f4f4f5]">
                        <i class="bx bx-code-alt text-sm shrink-0 text-[#ffd700]"></i>
                        <span class="text-[11px] text-[#71717a]">Rendering syllabus…</span>
                    </div>
                    <div class="flex items-center justify-center gap-2.5 px-3 py-2 rounded-[10px] bg-[#f4f4f5]">
                        <i class="bx bx-data text-sm shrink-0 text-[#ffd700]"></i>
                        <span class="text-[11px] text-[#71717a]">Freezing version record…</span>
                    </div>
                </div>
                <div class="w-12 h-[2px] rounded-full bg-[#16a34a]"></div>
            </div>
        </div>

        @php
            $navSteps = [
                'academic_calendar' => ['label' => 'Academic Calendar',  'icon' => 'bx-calendar',       'short' => 'Calendar'],
                'course_components' => ['label' => 'Course Components',  'icon' => 'bx-notepad',        'short' => 'Components'],
                'course_outcomes'   => ['label' => 'Course Outcomes',    'icon' => 'bx-book-open',      'short' => 'Outcomes'],
                'weekly_coverage'   => ['label' => 'Weekly Coverage',    'icon' => 'bx-calendar-week',  'short' => 'Coverage'],
                'course_evaluation' => ['label' => 'Course Evaluation',  'icon' => 'bx-bar-chart-alt-2','short' => 'Evaluation'],
                'review'            => ['label' => 'Review & Submit',    'icon' => 'bx-check-shield',   'short' => 'Review'],
            ];
            $missingSteps = ['academic_calendar', 'course_components', 'course_outcomes', 'weekly_coverage'];
            $missingLabels = [
                'academic_calendar' => 'No calendar selected',
                'course_components' => 'Instructor details incomplete',
                'course_outcomes'   => 'No course outcomes saved',
                'weekly_coverage'   => 'Some weeks have missing content',
            ];
            $bannerColors = [
                'academic_calendar' => '#92d12c',
                'course_components' => '#4eab18',
                'course_outcomes'   => '#009639',
                'weekly_coverage'   => '#038303',
                'course_evaluation' => '#1a5f30',
                'review'            => '#003a10',
            ];
        @endphp

        {{-- ══ Two-column layout ══════════════════════════════════════════════ --}}
        <div class="flex gap-6 items-start">

            {{-- ── Main content ────────────────────────────────────────────── --}}
            <div class="flex-1 min-w-0">

                {{-- Mobile step strip --}}
                <div class="lg:hidden mb-4 relative">
                    <div class="pointer-events-none absolute right-0 top-0 bottom-0 w-10 z-10"
                         style="background: linear-gradient(to right, transparent, #f4f4f5);"></div>
                    <div class="overflow-x-auto scrollbar-none">
                        <div class="flex items-center gap-1 min-w-max pr-8">
                            @foreach ($navSteps as $step => $meta)
                                @php
                                    $idx      = array_search($step, $stepsOrder, true);
                                    $isCurrent = $currentStep === $step;
                                    $isDone    = $currentIndex !== false && $idx !== false && $idx < $currentIndex;
                                    $hasMiss   = in_array($step, $missingSteps) && ($this->stepMissing[$step] ?? false);
                                @endphp
                                <button type="button" wire:click="clickTab('{{ $step }}')"
                                    wire:loading.attr="disabled"
                                    wire:target="clickTab,goPreviousStep,goNextStep,submitForReview,saveAsDone"
                                    x-on:click="_navigating = true"
                                    class="relative flex items-center gap-1.5 px-3 py-1.5 rounded-[10px] text-[12px] font-medium transition-colors focus:outline-none disabled:opacity-50
                                           {{ $isCurrent ? 'bg-[#f0fdf4] text-[#15803d] border border-[#86efac]' : 'text-[#71717a] hover:bg-[#f4f4f5] border border-transparent' }}">
                                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0
                                                 {{ $isCurrent ? 'bg-[#16a34a] text-white' : ($isDone ? 'bg-[#16a34a] text-white' : 'bg-[#e4e4e7] text-[#71717a]') }}">
                                        @if ($isDone)<i class="bx bx-check text-xs"></i>@else{{ $idx + 1 }}@endif
                                    </span>
                                    <span class="whitespace-nowrap">{{ $meta['short'] }}</span>
                                    @if ($hasMiss)
                                        <span class="absolute -top-0.5 -right-0.5 w-2 h-2 rounded-full bg-[#d97706]"
                                            title="{{ $missingLabels[$step] ?? 'Incomplete' }}"></span>
                                    @endif
                                </button>
                                @if (!$loop->last)
                                    <i class="bx bx-chevron-right text-[#d4d4d8] text-sm shrink-0"></i>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Step content card --}}
                <div class="bg-white rounded-[16px] border border-[#e4e4e7]"
                     style="box-shadow: 0 1px 8px rgba(0,0,0,0.05);">

                    {{-- Step banner --}}
                    @php
                        $stepNums = array_keys($navSteps);
                        $stepNum  = (array_search($currentStep, $stepNums) ?: 0) + 1;
                        $bannerColor = $bannerColors[$currentStep] ?? '#16a34a';
                        $bannerLabel = $navSteps[$currentStep]['label'] ?? '';
                    @endphp
                    <div class="flex items-center gap-3 px-5 py-2.5 border-b border-[#e4e4e7] rounded-t-[16px]"
                         style="background: linear-gradient(135deg, {{ $bannerColor }}12 0%, #ffffff 100%);">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full text-white text-[11px] font-bold shrink-0"
                              style="background-color: {{ $bannerColor }};">
                            {{ $stepNum }}
                        </span>
                        <span class="text-[12px] font-semibold tracking-wide" style="color: {{ $bannerColor }};">
                            Step {{ $stepNum }} of 6 — {{ $bannerLabel }}
                        </span>
                        <div class="flex-1 ml-2 hidden sm:block">
                            <div class="h-[3px] rounded-full bg-[#f4f4f5] overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500"
                                     style="width: {{ ($stepNum / 6) * 100 }}%; background-color: {{ $bannerColor }};"></div>
                            </div>
                        </div>
                    </div>

                    <div class="{{ $currentStep === 'academic_calendar' ? 'block' : 'hidden' }} p-5 sm:p-6">
                        <livewire:syllabus.steps.academic-calendar-step :syllabus-id="$syllabus->id" />
                    </div>
                    <div class="{{ $currentStep === 'course_components' ? 'block' : 'hidden' }} p-5 sm:p-6">
                        <livewire:syllabus.steps.components-step :syllabus-id="$syllabus->id" />
                    </div>
                    <div class="{{ $currentStep === 'course_outcomes' ? 'block' : 'hidden' }} p-5 sm:p-6">
                        <livewire:syllabus.steps.course-outcomes-step :syllabus-id="$syllabus->id" />
                    </div>
                    <div class="{{ $currentStep === 'weekly_coverage' ? 'block' : 'hidden' }} p-5 sm:p-6">
                        <livewire:syllabus.steps.weekly-coverage-step :syllabus-id="$syllabus->id" />
                    </div>
                    <div class="{{ $currentStep === 'course_evaluation' ? 'block' : 'hidden' }} p-5 sm:p-6">
                        <livewire:syllabus.steps.course-evaluation-step :syllabus-id="$syllabus->id" />
                    </div>
                    <div class="{{ $currentStep === 'review' ? 'block' : 'hidden' }} p-5 sm:p-6">
                        <livewire:syllabus.steps.review-step :syllabus-id="$syllabus->id" />
                    </div>

                </div>

                {{-- Bottom Prev / Next --}}
                <div class="mt-4 flex justify-between items-center gap-3">
                    <div>
                        @if ($this->hasPreviousStep())
                            <x-button variant="cancel" wire:click="goPreviousStep" wire:loading.attr="disabled"
                                wire:target="clickTab,goPreviousStep,goNextStep,saveCurrentStep,submitForReview,saveAsDone"
                                x-on:click="_navigating = true">
                                <i class="bx bx-chevron-left"></i>
                                <span class="hidden sm:inline">Previous</span>
                            </x-button>
                        @endif
                    </div>
                    <div>
                        @if ($this->hasNextStep())
                            <x-button variant="primary" wire:click="goNextStep" wire:loading.attr="disabled"
                                wire:target="clickTab,goPreviousStep,goNextStep,saveCurrentStep,submitForReview,saveAsDone"
                                loading="Saving…" x-on:click="_navigating = true">
                                <span class="hidden sm:inline">Next</span> <i class="bx bx-chevron-right"></i>
                            </x-button>
                        @endif
                    </div>
                </div>

            </div>{{-- end main content --}}

            {{-- ── Sticky right-side step navigator ──────────────────────────── --}}
            <aside class="hidden lg:block w-60 shrink-0"
                style="position: sticky; top: 5rem; align-self: flex-start; max-height: calc(100vh - 6rem); overflow-y: auto;">

                {{-- Step navigator card --}}
                <div class="rounded-[16px] border border-[#e4e4e7] bg-white overflow-hidden"
                     style="box-shadow: 0 1px 8px rgba(0,0,0,0.06);">

                    {{-- Nav header --}}
                    <div class="px-4 py-3 border-b border-[#e4e4e7] bg-[#09090b]">
                        <div class="flex items-center gap-2">
                            <i class="bx bx-list-check text-sm text-[#ffd700]"></i>
                            <p class="text-[11px] font-bold uppercase tracking-widest text-[#ffd700]">Wizard Steps</p>
                        </div>
                        {{-- Mini progress --}}
                        <div class="mt-2 flex items-center gap-1">
                            @foreach ($navSteps as $step => $meta)
                                @php $idx = array_search($step, $stepsOrder, true); @endphp
                                <div class="flex-1 h-[3px] rounded-full transition-all duration-300
                                    {{ $currentStep === $step
                                        ? 'bg-[#ffd700]'
                                        : ($currentIndex !== false && $idx < $currentIndex
                                            ? 'bg-[#86efac]'
                                            : 'bg-white/20') }}">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Step list --}}
                    <nav class="py-1" aria-label="Wizard Steps">
                        @foreach ($navSteps as $step => $meta)
                            @php
                                $idx        = array_search($step, $stepsOrder, true);
                                $isCurrent  = $currentStep === $step;
                                $isCompleted = $currentIndex !== false && $idx !== false && $idx < $currentIndex;
                                $hasMissing = in_array($step, $missingSteps) && ($this->stepMissing[$step] ?? false);
                            @endphp
                            <button type="button" wire:click="clickTab('{{ $step }}')"
                                wire:loading.attr="disabled"
                                wire:target="clickTab,goPreviousStep,goNextStep,submitForReview,saveAsDone"
                                x-on:click="_navigating = true"
                                class="w-full flex items-center gap-3 px-4 py-2.5 text-left transition-all duration-150
                                       focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed border-l-[3px]
                                       {{ $isCurrent
                                           ? 'bg-[#f0fdf4] text-[#15803d] font-semibold border-l-[#16a34a]'
                                           : 'text-[#52525b] hover:bg-[#f4f4f5] hover:text-[#18181b] border-l-transparent' }}">

                                <span class="shrink-0 w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-bold transition-all duration-200
                                             {{ $isCurrent ? 'bg-[#16a34a] text-white' : ($isCompleted ? 'bg-[#16a34a] text-white' : 'bg-[#f4f4f5] text-[#a1a1aa]') }}"
                                      @if($isCurrent) style="box-shadow: 0 2px 8px rgba(22,163,74,0.3);" @endif>
                                    @if ($isCompleted)
                                        <i class="bx bx-check text-xs"></i>
                                    @else
                                        {{ $idx + 1 }}
                                    @endif
                                </span>

                                <span class="flex-1 leading-tight text-[12px]">{{ $meta['label'] }}</span>

                                @if ($hasMissing)
                                    <span class="shrink-0 w-2 h-2 rounded-full bg-[#d97706] animate-pulse"
                                        title="{{ $missingLabels[$step] ?? 'Incomplete' }}"></span>
                                @endif
                            </button>
                        @endforeach
                    </nav>

                </div>

                {{-- Tools — Weekly Coverage step --}}
                @if ($currentStep === 'weekly_coverage')
                    <div class="mt-3 rounded-[16px] border border-[#e4e4e7] bg-white overflow-hidden"
                         style="box-shadow: 0 1px 8px rgba(0,0,0,0.05);">
                        <button type="button" x-on:click="scheduleOpen = true"
                            class="w-full flex items-center gap-2.5 px-4 py-2.5 text-[12px] font-medium text-[#52525b] hover:bg-[#f4f4f5] transition-colors">
                            <i class="bx bx-time text-sm text-[#a1a1aa]"></i> Schedule
                        </button>
                        <button type="button" x-on:click="calInfoOpen = true"
                            class="w-full flex items-center gap-2.5 px-4 py-2.5 text-[12px] font-medium text-[#52525b] hover:bg-[#f4f4f5] transition-colors">
                            <i class="bx bx-calendar text-sm text-[#a1a1aa]"></i> Calendar Info
                        </button>
                        <div class="mx-4 border-t border-[#f4f4f5]"></div>
                        <button type="button" x-on:click="$dispatch('expand-all-weeks')"
                            class="w-full flex items-center gap-2.5 px-4 py-2.5 text-[12px] font-medium text-[#52525b] hover:bg-[#f4f4f5] transition-colors">
                            <i class="bx bx-expand-alt text-sm text-[#a1a1aa]"></i> Expand All
                        </button>
                        <button type="button" x-on:click="$dispatch('collapse-all-weeks')"
                            class="w-full flex items-center gap-2.5 px-4 py-2.5 text-[12px] font-medium text-[#52525b] hover:bg-[#f4f4f5] transition-colors">
                            <i class="bx bx-collapse-alt text-sm text-[#a1a1aa]"></i> Collapse All
                        </button>
                        <button type="button" x-on:click="$dispatch('jump-to-incomplete-week')"
                            class="w-full flex items-center gap-2.5 px-4 py-2.5 text-[12px] font-medium text-[#92400e] hover:bg-[#fffbeb] transition-colors">
                            <i class="bx bx-skip-next text-sm text-[#d97706]"></i> Next Incomplete
                        </button>
                    </div>
                @endif

                {{-- Course Info + PO Reference — Course Outcomes step --}}
                @if ($currentStep === 'course_outcomes')
                    <div class="mt-3 space-y-2">
                        <button type="button" x-on:click="$dispatch('open-course-info-drawer')"
                            class="w-full flex items-center justify-between px-4 py-3 rounded-[14px] border border-[#e4e4e7] bg-white text-[13px] font-semibold text-[#18181b] hover:bg-[#f4f4f5] hover:border-[#d1fae5] transition-all"
                            style="box-shadow: 0 1px 4px rgba(0,0,0,0.04);">
                            <span class="flex items-center gap-2.5">
                                <span class="flex items-center justify-center w-7 h-7 rounded-[10px] bg-[#dcfce7] text-[#16a34a]">
                                    <i class="bx bx-book text-sm leading-none"></i>
                                </span>
                                Course Info
                            </span>
                            <i class="bx bx-chevron-right text-[#a1a1aa]"></i>
                        </button>
                        <button type="button" x-on:click="$dispatch('open-po-ref-drawer')"
                            class="w-full flex items-center justify-between px-4 py-3 rounded-[14px] border border-[#e4e4e7] bg-white text-[13px] font-semibold text-[#18181b] hover:bg-[#f4f4f5] hover:border-[#bfdbfe] transition-all"
                            style="box-shadow: 0 1px 4px rgba(0,0,0,0.04);">
                            <span class="flex items-center gap-2.5">
                                <span class="flex items-center justify-center w-7 h-7 rounded-[10px] bg-[#dbeafe] text-[#2563eb]">
                                    <i class="bx bx-list-check text-sm leading-none"></i>
                                </span>
                                PO Reference
                            </span>
                            <i class="bx bx-chevron-right text-[#a1a1aa]"></i>
                        </button>
                    </div>
                @endif

            </aside>

        </div>{{-- end two-column --}}
    </x-panel>
</div>
