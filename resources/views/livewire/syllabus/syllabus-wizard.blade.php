@php
    $steps = $syllabus->getWizardSteps();
    $stepsOrder = array_keys($steps);
    $currentIndex = array_search($currentStep, $stepsOrder, true);
@endphp

<div x-data="{
        _navigating: false,
        scheduleOpen: false,
        calInfoOpen: false,
        tryNavigate(fn) {
            this._navigating = true;
            fn();
        }
    }"
    x-on:syllabus-step-changed.window="_navigating = false"
    x-on:lw-toast.window="if ($event.detail?.type === 'error') _navigating = false"
    x-on:livewire:navigated.window="_navigating = false">

    <x-page-header icon="bx-book-open" title="{{ $syllabus->id ? 'Edit' : 'Create' }} Syllabus"
        desc="{{ $course->course_code }} — {{ $course->course_title }}">
        <button type="button"
            onclick="window.dispatchEvent(new CustomEvent('open-help-panel'))"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-[10px]
                   text-[#52525b] bg-white border border-[#d4d4d8] hover:bg-[#f4f4f5] hover:border-[#a1a1aa]
                   transition-colors [&_i]:leading-none"
            aria-label="Open how to use guide">
            <i class="bx bx-help-circle text-sm"></i> How to Use
        </button>
        <x-button variant="cancel" href="{{ route('syllabus.index') }}">
            <i class="bx bx-arrow-back"></i>
            <span class="sm:hidden">Back</span>
            <span class="hidden sm:inline">Back to Syllabi</span>
        </x-button>
    </x-page-header>

    @php
        $helpModuleMap = [
            'academic_calendar' => 'syllabus-academic-calendar',
            'course_components'  => 'syllabus-course-components',
            'course_outcomes'    => 'syllabus-course-outcomes',
            'weekly_coverage'    => 'syllabus-weekly-coverage',
            'course_evaluation'  => 'syllabus-course-evaluation',
            'review'             => 'syllabus-review',
        ];
    @endphp
    <x-help-panel :module="$helpModuleMap[$currentStep] ?? 'syllabus-index'" />

    <x-panel>

        {{-- ══ Navigation overlay ══════════════════════════════════════════════ --}}
        <template x-if="_navigating">
            <x-wizard.overlay title="Saving changes…" subtitle="Please wait" />
        </template>

        {{-- ══ Save-as-Done overlay ════════════════════════════════════════════ --}}
        <div wire:loading.class.remove="hidden" wire:target="saveAsDone"
            class="hidden">
            <x-wizard.overlay title="Saving Syllabus…" subtitle="This may take a few seconds" :dual="true">
                <div class="flex items-center justify-center gap-2.5 px-3 py-2 rounded-[10px] bg-[#f4f4f5]">
                    <i class="bx bx-code-alt text-sm shrink-0 text-[#ffd700]"></i>
                    <span class="text-[11px] text-[#71717a]">Rendering syllabus…</span>
                </div>
                <div class="flex items-center justify-center gap-2.5 px-3 py-2 rounded-[10px] bg-[#f4f4f5]">
                    <i class="bx bx-data text-sm shrink-0 text-[#ffd700]"></i>
                    <span class="text-[11px] text-[#71717a]">Freezing version record…</span>
                </div>
            </x-wizard.overlay>
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
            $missingSteps = ['academic_calendar', 'course_components', 'course_outcomes', 'weekly_coverage', 'course_evaluation'];
            $missingLabels = [
                'academic_calendar' => 'No calendar selected',
                'course_components' => 'Instructor details incomplete',
                'course_outcomes'   => 'No course outcomes saved',
                'weekly_coverage'   => 'Some weeks have missing content',
                'course_evaluation' => 'Evaluation weights incomplete',
            ];
            // Use CSS variable steps so colors stay in sync with the design system.
            // Each step gets a progressively darker shade of the CLSU green ramp.
            $bannerVars = [
                'academic_calendar' => 'color-mix(in srgb, var(--clsu-green) 55%, #fff)',
                'course_components' => 'color-mix(in srgb, var(--clsu-green) 70%, #fff)',
                'course_outcomes'   => 'var(--clsu-green)',
                'weekly_coverage'   => 'color-mix(in srgb, var(--clsu-green) 100%, #000 5%)',
                'course_evaluation' => 'color-mix(in srgb, var(--clsu-green) 100%, #000 20%)',
                'review'            => 'color-mix(in srgb, var(--clsu-green) 100%, #000 40%)',
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
                                <button type="button"
                                    x-on:click="tryNavigate(() => $wire.clickTab('{{ $step }}'))"
                                    wire:loading.attr="disabled"
                                    wire:target="clickTab,goPreviousStep,goNextStep,submitForReview,saveAsDone"
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
                        $stepNums    = array_keys($navSteps);
                        $stepNum     = (array_search($currentStep, $stepNums) ?: 0) + 1;
                        $bannerColor = $bannerVars[$currentStep] ?? 'var(--clsu-green)';
                        $bannerLabel = $navSteps[$currentStep]['label'] ?? '';
                    @endphp
                    <div class="flex items-center gap-3 px-5 py-2.5 border-b border-[#e4e4e7] rounded-t-[16px]"
                         style="background: linear-gradient(135deg, color-mix(in srgb, {{ $bannerColor }} 8%, #fff) 0%, #ffffff 100%);">
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
                            <x-button variant="cancel" wire:loading.attr="disabled"
                                wire:target="clickTab,goPreviousStep,goNextStep,saveCurrentStep,submitForReview,saveAsDone"
                                x-on:click="tryNavigate(() => $wire.goPreviousStep())">
                                <i class="bx bx-chevron-left"></i>
                                <span class="hidden sm:inline">Previous</span>
                            </x-button>
                        @endif
                    </div>
                    <div>
                        @if ($this->hasNextStep())
                            <x-button variant="primary" wire:loading.attr="disabled"
                                wire:target="clickTab,goPreviousStep,goNextStep,saveCurrentStep,submitForReview,saveAsDone"
                                loading="Saving…" x-on:click="tryNavigate(() => $wire.goNextStep())">
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
                                $idx         = array_search($step, $stepsOrder, true);
                                $isCurrent   = $currentStep === $step;
                                $isCompleted = $currentIndex !== false && $idx !== false && $idx < $currentIndex;
                                $hasMissing  = in_array($step, $missingSteps) && ($this->stepMissing[$step] ?? false);
                            @endphp
                            <button type="button"
                                x-on:click="tryNavigate(() => $wire.clickTab('{{ $step }}'))"
                                wire:loading.attr="disabled"
                                wire:target="clickTab,goPreviousStep,goNextStep,submitForReview,saveAsDone"
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
                        {{-- Schedule and Calendar Info open drawers that live inside the weekly-coverage
                             Livewire component. We dispatch custom events; the step component listens
                             for them on its own x-data rather than relying on wizard-root Alpine vars. --}}
                        <button type="button" x-on:click="$dispatch('open-schedule-drawer')"
                            class="w-full flex items-center gap-2.5 px-4 py-2.5 text-[12px] font-medium text-[#52525b] hover:bg-[#f4f4f5] transition-colors">
                            <i class="bx bx-time text-sm text-[#a1a1aa]"></i> Schedule
                        </button>
                        <button type="button" x-on:click="$dispatch('open-calendar-info-drawer')"
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
