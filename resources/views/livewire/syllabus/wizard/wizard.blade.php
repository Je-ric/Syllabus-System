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
    x-on:syllabus-step-save-failed.window="
        _navigating = false;
        // Handle rollback notification if needed
        if ($event.detail.previousStep) {
            window.dispatchEvent(new CustomEvent('lw-toast', {
                detail: { type: 'warning', message: 'Returning to previous step due to save error.' }
            }));
        }
    "
    x-on:livewire:navigated.window="_navigating = false"
    x-on:syllabus-save-started.window="_navigating = true"
    x-on:syllabus-save-finished.window="_navigating = false">

    <x-layout.page-header icon="bx-book-open" title="{{ $syllabus->id ? 'Edit' : 'Create' }} Syllabus"
        desc="{{ $course->course_code }} — {{ $course->course_title }}">
        <x-ui.help-trigger />
        <x-ui.button variant="cancel" href="{{ route('syllabus.index') }}">
            <i class="bx bx-arrow-back"></i>
            <span class="sm:hidden">Back</span>
            <span class="hidden sm:inline">Back to Syllabi</span>
        </x-ui.button>
    </x-layout.page-header>

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
    <x-layout.help-panel :module="$helpModuleMap[$currentStep] ?? 'syllabus-index'" />

    <x-layout.panel>

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
                                    x-bind:disabled="_navigating"
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
                {{-- class="bg-white rounded-2xl border border-[#e4e4e7]" --}}
                <div
                     {{-- style="box-shadow: 0 1px 8px rgba(0,0,0,0.05);" --}}
                     >

                    @if ($currentStep !== 'academic_calendar' && $this->calendarIsInactive())
                        <div class="mx-5 sm:mx-6 mt-5 sm:mt-6">
                            <x-feedback-status.alert type="warning" :showTitle="false">
                                The previously selected academic calendar is no longer active.
                                <a href="#" wire:click.prevent="clickTab('academic_calendar')" class="underline font-medium">Go to Step 1</a> to update it.
                            </x-feedback-status.alert>
                        </div>
                    @endif

                    <div class="{{ $currentStep === 'academic_calendar' ? 'block' : 'hidden' }} py-5 sm:p-3">
                        <livewire:syllabus.steps.academic-calendar-step :syllabus-id="$syllabus->id" :step-number="1" />
                    </div>
                    <div class="{{ $currentStep === 'course_components' ? 'block' : 'hidden' }} py-5 sm:p-3">
                        <livewire:syllabus.steps.components-step :syllabus-id="$syllabus->id" :step-number="2" />
                    </div>
                    <div class="{{ $currentStep === 'course_outcomes' ? 'block' : 'hidden' }} py-5 sm:p-3">
                        <livewire:syllabus.steps.course-outcomes-step :syllabus-id="$syllabus->id" :step-number="3" />
                    </div>
                    <div class="{{ $currentStep === 'weekly_coverage' ? 'block' : 'hidden' }} py-5 sm:p-3">
                        <livewire:syllabus.steps.weekly-coverage-step :syllabus-id="$syllabus->id" :step-number="4" />
                    </div>
                    <div class="{{ $currentStep === 'course_evaluation' ? 'block' : 'hidden' }} py-5 sm:p-3">
                        <livewire:syllabus.steps.course-evaluation-step :syllabus-id="$syllabus->id" :step-number="5" />
                    </div>
                    <div class="{{ $currentStep === 'review' ? 'block' : 'hidden' }} py-5 sm:p-3">
                        <livewire:syllabus.steps.review-step :syllabus-id="$syllabus->id" :step-number="6" />
                    </div>

                </div>

                {{-- Bottom Prev / Next --}}
                <div class="mt-4 flex justify-between items-center gap-3">
                    <div>
                        @if ($this->hasPreviousStep())
                            <x-ui.button variant="cancel"
                                x-bind:disabled="_navigating"
                                x-on:click="tryNavigate(() => $wire.goPreviousStep())">
                                <i class="bx bx-chevron-left"></i>
                                <span class="hidden sm:inline">Previous</span>
                            </x-ui.button>
                        @endif
                    </div>
                    <div>
                        @if ($this->hasNextStep())
                            <x-ui.button variant="primary"
                                x-bind:disabled="_navigating"
                                x-on:click="tryNavigate(() => $wire.goNextStep())">
                                <span class="hidden sm:inline">Next</span>
                                <i class="bx bx-chevron-right"></i>
                            </x-ui.button>
                        @endif
                    </div>
                </div>

            </div>{{-- end main content --}}

            {{-- ── Sticky right-side step navigator ──────────────────────────── --}}
            <aside class="hidden lg:block w-60 shrink-0"
                style="position: sticky; top: 1rem; align-self: flex-start; max-height: calc(100vh - 2rem); overflow-y: auto;">

                {{-- Step navigator card --}}
                <div class="rounded-2xl border border-[#e4e4e7] bg-white overflow-hidden"
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
                                <div class="flex-1 h-0.75 rounded-full transition-all duration-300
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
                    <div class="mt-3 rounded-2xl border border-[#e4e4e7] bg-white overflow-hidden"
                         style="box-shadow: 0 1px 8px rgba(0,0,0,0.05);">
                        {{-- Info group --}}
                        <div class="px-4 pt-3 pb-1">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-[#a1a1aa]">Info</p>
                        </div>
                        <button type="button" x-on:click="$dispatch('open-schedule-drawer')"
                            class="w-full flex items-center gap-2.5 px-4 py-2.5 text-[12px] font-medium text-[#52525b] hover:bg-[#f4f4f5] transition-colors">
                            <i class="bx bx-time text-sm text-[#a1a1aa]"></i> Schedule
                        </button>
                        <button type="button" x-on:click="$dispatch('open-calendar-info-drawer')"
                            class="w-full flex items-center gap-2.5 px-4 py-2.5 text-[12px] font-medium text-[#52525b] hover:bg-[#f4f4f5] transition-colors">
                            <i class="bx bx-calendar text-sm text-[#a1a1aa]"></i> Calendar Info
                        </button>
                        {{-- Navigation group --}}
                        <div class="mx-4 border-t border-[#f4f4f5] mt-1"></div>
                        <div class="px-4 pt-3 pb-1">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-[#a1a1aa]">Navigation</p>
                        </div>
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
                        {{-- Save All — reachable without scrolling back to the header --}}
                        <div class="mx-4 border-t border-[#f4f4f5] mt-1"></div>
                        <button type="button"
                            x-on:click="$dispatch('sidebar-save-all-weeks')"
                            class="w-full flex items-center gap-2.5 px-4 py-2.5 text-[12px] font-medium text-[#166534] hover:bg-[#f0fdf4] transition-colors">
                            <i class="bx bx-save text-sm text-[#16a34a]"></i> Save All
                        </button>
                    </div>
                @endif

                {{-- Save as Done — Review step --}}
                @if ($currentStep === 'review')
                    <x-layout.card-section class="mt-3" title="Freeze Syllabus" icon="bx-save">
                        <div class="flex flex-col gap-3">
                            <div>
                                <p class="text-xs text-slate-500 mt-0.5">Create an immutable snapshot of this syllabus.</p>
                            </div>
                            <x-ui.button
                                type="button"
                                variant="add-button"
                                wire:click="saveAsDone"
                                wire:loading.attr="disabled"
                                wire:target="saveAsDone"
                                loading="Saving…">
                                <i class="bx bx-save text-base leading-none"></i> Create version
                            </x-ui.button>
                        </div>
                    </x-layout.card-section>
                @endif

                {{-- Tools — Course Outcomes step --}}
                @if ($currentStep === 'course_outcomes')
                    <div class="mt-3 rounded-2xl border border-[#e4e4e7] bg-white overflow-hidden"
                         style="box-shadow: 0 1px 8px rgba(0,0,0,0.05);">
                        <div class="px-4 pt-3 pb-1">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-[#a1a1aa]">Info</p>
                        </div>
                        <button type="button" x-on:click="$dispatch('open-course-info-drawer')"
                            class="w-full flex items-center gap-2.5 px-4 py-2.5 text-[12px] font-medium text-[#52525b] hover:bg-[#f4f4f5] transition-colors">
                            <i class="bx bx-book text-sm text-[#a1a1aa]"></i> Course Info
                        </button>
                        <button type="button" x-on:click="$dispatch('open-po-ref-drawer')"
                            class="w-full flex items-center gap-2.5 px-4 py-2.5 text-[12px] font-medium text-[#52525b] hover:bg-[#f4f4f5] transition-colors">
                            <i class="bx bx-list-check text-sm text-[#a1a1aa]"></i> PO Reference
                        </button>
                        <div class="mx-4 border-t border-[#f4f4f5] mt-1"></div>
                        <div class="px-4 pt-3 pb-1">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-[#a1a1aa]">Actions</p>
                        </div>
                        <button type="button" x-on:click="$dispatch('sidebar-save-all-co')"
                            class="w-full flex items-center gap-2.5 px-4 py-2.5 text-[12px] font-medium text-[#166534] hover:bg-[#f0fdf4] transition-colors">
                            <i class="bx bx-save text-sm text-[#16a34a]"></i> Save All
                        </button>
                    </div>
                @endif

                {{-- Tools — Course Components step --}}
                @if ($currentStep === 'course_components')
                    <div class="mt-3 rounded-2xl border border-[#e4e4e7] bg-white overflow-hidden"
                         style="box-shadow: 0 1px 8px rgba(0,0,0,0.05);">
                        <div class="px-4 pt-3 pb-1">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-[#a1a1aa]">Actions</p>
                        </div>
                        <button type="button"
                            x-on:click="$dispatch('sidebar-save-components')"
                            class="w-full flex items-center gap-2.5 px-4 py-2.5 text-[12px] font-medium text-[#166534] hover:bg-[#f0fdf4] transition-colors">
                            <i class="bx bx-save text-sm text-[#16a34a]"></i> Save All
                        </button>
                    </div>
                @endif

                {{-- Tools — Course Evaluation step --}}
                @if ($currentStep === 'course_evaluation')
                    <div class="mt-3 rounded-2xl border border-[#e4e4e7] bg-white overflow-hidden"
                         style="box-shadow: 0 1px 8px rgba(0,0,0,0.05);">
                        {{-- Info group --}}
                        <div class="px-4 pt-3 pb-1">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-[#a1a1aa]">Info</p>
                        </div>
                        <button type="button" x-on:click="$dispatch('open-eval-notes-drawer')"
                            class="w-full flex items-center gap-2.5 px-4 py-2.5 text-[12px] font-medium text-[#52525b] hover:bg-[#f4f4f5] transition-colors">
                            <i class="bx bx-info-circle text-sm text-[#a1a1aa]"></i> Evaluation Notes
                        </button>
                        {{-- Actions group --}}
                        <div class="mx-4 border-t border-[#f4f4f5] mt-1"></div>
                        <div class="px-4 pt-3 pb-1">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-[#a1a1aa]">Actions</p>
                        </div>
                        <button type="button"
                            x-on:click="$dispatch('sidebar-save-evaluation')"
                            class="w-full flex items-center gap-2.5 px-4 py-2.5 text-[12px] font-medium text-[#166534] hover:bg-[#f0fdf4] transition-colors">
                            <i class="bx bx-save text-sm text-[#16a34a]"></i> Save Evaluation
                        </button>
                    </div>
                @endif

            </aside>

        </div>{{-- end two-column --}}
    </x-layout.panel>
</div>
