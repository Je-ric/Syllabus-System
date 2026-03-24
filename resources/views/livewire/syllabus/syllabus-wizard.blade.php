@php
    $steps = $syllabus->getWizardSteps();
    $stepsOrder = array_keys($steps);
    $currentIndex = array_search($currentStep, $stepsOrder, true);
@endphp

<div>
    <x-page-header
        icon="bx-book-open"
        title="{{ $syllabus->id ? 'Edit' : 'Create' }} Syllabus"
        desc="{{ $course->course_code }} - {{ $course->course_title }}">
        <x-button variant="cancel" href="{{ route('syllabus.index') }}">&larr; Back to Syllabi</x-button>
    </x-page-header>

    <x-panel>
        {{-- ══ Navigation overlay (step change / tab click) ════════════════════════ --}}
        <div
            wire:loading.style="display:flex"
            wire:target="goNextStep,goPreviousStep,clickTab"
            style="display:none"
            class="fixed inset-0 z-50 items-center justify-center"
            >
            {{-- backdrop --}}
            <div class="absolute inset-0" style="background: rgba(11,18,32,0.55); backdrop-filter: blur(6px);"></div>
            {{-- card --}}
            <div class="relative flex flex-col items-center gap-4 px-10 py-7 rounded-2xl shadow-2xl border border-white/10"
                 style="background: linear-gradient(135deg, #1a2235 0%, #0b1220 100%);">
                {{-- ring spinner --}}
                <div class="relative w-14 h-14">
                    <svg class="absolute inset-0 animate-spin" viewBox="0 0 56 56" fill="none"
                         style="color: #ffd700;">
                        <circle cx="28" cy="28" r="22" stroke="currentColor" stroke-width="3"
                                stroke-linecap="round" stroke-dasharray="110" stroke-dashoffset="70" />
                    </svg>
                    <svg class="absolute inset-0" viewBox="0 0 56 56" fill="none"
                         style="color: rgba(255,215,0,0.15);">
                        <circle cx="28" cy="28" r="22" stroke="currentColor" stroke-width="3" />
                    </svg>
                    <span class="absolute inset-0 flex items-center justify-center">
                        <i class="bx bx-transfer-alt text-lg" style="color: #ffd700;"></i>
                    </span>
                </div>
                <div class="text-center">
                    <p class="text-sm font-semibold" style="color: #ffffff;">Saving changes…</p>
                    <p class="text-xs mt-1" style="color: rgba(255,255,255,0.5);">Please wait</p>
                </div>
            </div>
        </div>

        {{-- ══ Save-as-Done overlay ════════════════════════════════════════════════
            wire:loading.style avoids the Tailwind `hidden` (!important) conflict.
            Alpine cycles step labels every 2.8 s while the request is in-flight.
        ──────────────────────────────────────────────────────────────────────────── --}}
        <div
            x-data="{
                steps: [
                    { icon: 'bx-code-alt',     label: 'Rendering syllabus…'        },
                    { icon: 'bx-cloud-upload', label: 'Uploading to Google Drive…'  },
                    { icon: 'bx-hdd',          label: 'Saving local backup…'        },
                    { icon: 'bx-data',         label: 'Freezing version record…'    },
                ],
                current: 0,
                timer: null,
                start() { this.current = 0; this.timer = setInterval(() => { if (this.current < this.steps.length - 1) this.current++; }, 2800); },
                stop()  { clearInterval(this.timer); this.current = 0; }
            }"
            wire:loading.style="display:flex"
            wire:target="saveAsDone"
            style="display:none"
            x-on:livewire:request-start.window="$event.detail?.call === 'saveAsDone' && start()"
            x-on:livewire:request-finish.window="stop()"
            class="fixed inset-0 z-50 items-center justify-center"
            >
            {{-- backdrop --}}
            <div class="absolute inset-0" style="background: rgba(11,18,32,0.65); backdrop-filter: blur(8px);"></div>
            {{-- card --}}
            <div class="relative flex flex-col items-center gap-5 px-12 py-9 rounded-2xl shadow-2xl border border-white/10 min-w-80"
                 style="background: linear-gradient(135deg, #1a2235 0%, #0b1220 100%);">

                {{-- dual-ring spinner with icon --}}
                <div class="relative w-16 h-16">
                    <svg class="absolute inset-0 animate-spin" viewBox="0 0 64 64" fill="none"
                         style="color: #ffd700;">
                        <circle cx="32" cy="32" r="26" stroke="currentColor" stroke-width="3"
                                stroke-linecap="round" stroke-dasharray="130" stroke-dashoffset="80" />
                    </svg>
                    <svg class="absolute inset-0" viewBox="0 0 64 64" fill="none"
                         style="color: rgba(255,215,0,0.12);">
                        <circle cx="32" cy="32" r="26" stroke="currentColor" stroke-width="3" />
                    </svg>
                    {{-- inner accent ring --}}
                    <svg class="absolute inset-0 animate-spin" viewBox="0 0 64 64" fill="none"
                         style="color: #009639; animation-direction: reverse; animation-duration: 1.4s;">
                        <circle cx="32" cy="32" r="18" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-dasharray="60" stroke-dashoffset="40" />
                    </svg>
                    <span class="absolute inset-0 flex items-center justify-center">
                        <i x-bind:class="'bx text-2xl ' + steps[current].icon"
                           style="color: #ffd700;"></i>
                    </span>
                </div>

                {{-- step label --}}
                <div class="text-center">
                    <p class="text-sm font-semibold" style="color: #ffffff;"
                       x-text="steps[current].label"></p>
                    <p class="text-xs mt-1" style="color: rgba(255,255,255,0.45);">This may take a few seconds</p>
                </div>

                {{-- progress dots --}}
                <div class="flex items-center gap-2.5">
                    <template x-for="(s, i) in steps" :key="i">
                        <span class="rounded-full transition-all duration-300"
                              x-bind:style="i === current
                                  ? 'width:10px;height:10px;background:#ffd700;'
                                  : (i < current
                                      ? 'width:8px;height:8px;background:#009639;'
                                      : 'width:6px;height:6px;background:rgba(255,255,255,0.2);')"
                        ></span>
                    </template>
                </div>

                {{-- step counter --}}
                <p class="text-[11px]" style="color: rgba(255,255,255,0.3);">
                    Step <span x-text="current + 1"></span> of <span x-text="steps.length"></span>
                </p>

            </div>
        </div>

        {{-- ══ Step nav tabs ════════════════════════════════════════════════════════ --}}
        <div class="mb-6 bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
            <nav class="flex overflow-x-auto border-b border-slate-200 scrollbar-none" aria-label="Wizard Steps">
                @foreach($steps as $step => $label)
                    @php
                        $index       = array_search($step, $stepsOrder, true);
                        $isCurrent   = $currentStep === $step;
                        $isCompleted = $currentIndex !== false && $index !== false && $index < $currentIndex;
                    @endphp
                    <button type="button"
                        wire:click="clickTab('{{ $step }}')"
                        wire:loading.attr="disabled"
                        wire:target="clickTab,goPreviousStep,goNextStep,submitForReview,saveAsDone"
                        class="flex-1 min-w-20 flex flex-col items-center gap-1 px-3 py-3.5 text-xs font-medium
                                transition-all duration-150 focus:outline-none border-b-2 whitespace-nowrap
                                {{ $isCurrent
                                    ? 'border-emerald-600 text-emerald-700 bg-emerald-50'
                                    : ($isCompleted
                                        ? 'border-emerald-400 text-emerald-600 hover:bg-emerald-50'
                                        : 'border-transparent text-slate-400 hover:text-slate-600 hover:bg-slate-50') }}
                                disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs shrink-0
                                    {{ $isCurrent
                                        ? 'bg-emerald-600 text-white shadow-sm'
                                        : ($isCompleted
                                            ? 'bg-emerald-500 text-white'
                                            : 'bg-slate-100 text-slate-500 ring-1 ring-slate-200') }}">
                            @if($isCompleted)
                                <i class="bx bx-check text-sm"></i>
                            @else
                                {{ $index + 1 }}
                            @endif
                        </span>
                        <span class="hidden md:block text-center leading-tight mt-0.5">{{ $label }}</span>
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- ══ Step content ════════════════════════════════════════════════════════
            CRITICAL: No :key attribute on any child component.
            Without :key, Livewire keeps the component mounted in memory for the
            entire page session. Switching steps just shows/hides the wrapper div —
            zero remount, zero cold-boot DB queries, instant perceived transition.

            Each child listens to 'syllabus-save-step' and only acts on its own step.
            The wizard's saveAndNavigate() dispatches the save event + changes
            $currentStep in ONE round trip, so the UI updates immediately.
        ──────────────────────────────────────────────────────────────────────────── --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm">

            {{-- Academic Calendar --}}
            <div class="{{ $currentStep === 'academic_calendar' ? 'block' : 'hidden' }} p-5 sm:p-6">
                <livewire:syllabus.steps.academic-calendar-step :syllabus-id="$syllabus->id" />
            </div>

            {{-- Course Components --}}
            <div class="{{ $currentStep === 'course_components' ? 'block' : 'hidden' }} p-5 sm:p-6">
                <livewire:syllabus.steps.components-step :syllabus-id="$syllabus->id" />
            </div>

            {{-- Course Outcomes --}}
            <div class="{{ $currentStep === 'course_outcomes' ? 'block' : 'hidden' }} p-5 sm:p-6">
                <livewire:syllabus.steps.course-outcomes-step :syllabus-id="$syllabus->id" />
            </div>

            {{-- Weekly Coverage --}}
            <div class="{{ $currentStep === 'weekly_coverage' ? 'block' : 'hidden' }} p-5 sm:p-6">
                <livewire:syllabus.steps.weekly-coverage-step :syllabus-id="$syllabus->id" />
            </div>

            {{-- Course Evaluation --}}
            <div class="{{ $currentStep === 'course_evaluation' ? 'block' : 'hidden' }} p-5 sm:p-6">
                <livewire:syllabus.steps.course-evaluation-step :syllabus-id="$syllabus->id" />
            </div>

            {{-- Review --}}
            <div class="{{ $currentStep === 'review' ? 'block' : 'hidden' }} p-5 sm:p-6">
                <livewire:syllabus.steps.review-step :syllabus-id="$syllabus->id" />
            </div>
        </div>

        {{-- ══ Bottom navigation ════════════════════════════════════════════════════ --}}
        <div class="mt-6 flex justify-between items-center gap-3">
            <div>
                @if($this->hasPreviousStep())
                    <x-button variant="cancel"
                        wire:click="goPreviousStep"
                        wire:loading.attr="disabled"
                        wire:target="clickTab,goPreviousStep,goNextStep,saveCurrentStep,submitForReview,saveAsDone">
                        <i class="bx bx-chevron-left"></i>
                        <span class="hidden sm:inline">Previous</span>
                    </x-button>
                @endif
            </div>

            <div class="flex items-center gap-3">
                @if($this->hasNextStep())
                    <x-button variant="primary"
                        wire:click="goNextStep"
                        wire:loading.attr="disabled"
                        wire:target="clickTab,goPreviousStep,goNextStep,saveCurrentStep,submitForReview,saveAsDone"
                        loading="Saving…">
                        <span class="hidden sm:inline">Next</span> <i class="bx bx-chevron-right"></i>
                    </x-button>
                @endif
            </div>
        </div>
    </x-panel>
</div>
