@php
    $steps = $syllabus->getWizardSteps();
    $stepsOrder = array_keys($steps);
    $currentIndex = array_search($currentStep, $stepsOrder, true);
@endphp

<div>
    <x-page-header icon="bx-book-open" title="{{ $syllabus->id ? 'Edit' : 'Create' }} Syllabus"
        desc="{{ $course->course_code }} - {{ $course->course_title }}">
        <x-button variant="cancel" href="{{ route('syllabus.index') }}"><i class="bx bx-arrow-back"></i> Back to Syllabi</x-button>
    </x-page-header>

    <x-panel>
        {{-- ══ Navigation overlay (step change / tab click) ════════════════════════ --}}
        <div wire:loading.style="display:flex" wire:target="goNextStep,goPreviousStep,clickTab" style="display:none"
            class="fixed inset-0 z-50 items-center justify-center">
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

        {{-- ══ Save-as-Done overlay ════════════════════════════════════════════════ --}}
        <div wire:loading.style="display:flex" wire:target="saveAsDone" style="display:none"
            class="fixed inset-0 z-50 items-center justify-center">
            <div class="absolute inset-0" style="background: rgba(11,18,32,0.65); backdrop-filter: blur(8px);"></div>
            <div class="relative flex flex-col items-center gap-6 px-10 py-8 rounded-2xl shadow-2xl border border-white/10"
                style="background: linear-gradient(135deg, #1a2235 0%, #0b1220 100%); min-width: 300px;">

                {{-- spinner --}}
                <div class="relative w-14 h-14">
                    <svg class="absolute inset-0 animate-spin" viewBox="0 0 56 56" fill="none"
                        style="color:#ffd700;">
                        <circle cx="28" cy="28" r="22" stroke="currentColor" stroke-width="3"
                            stroke-linecap="round" stroke-dasharray="110" stroke-dashoffset="70" />
                    </svg>
                    <svg class="absolute inset-0" viewBox="0 0 56 56" fill="none"
                        style="color:rgba(255,215,0,0.12);">
                        <circle cx="28" cy="28" r="22" stroke="currentColor" stroke-width="3" />
                    </svg>
                    <svg class="absolute inset-0 animate-spin" viewBox="0 0 56 56" fill="none"
                        style="color:#009639; animation-direction:reverse; animation-duration:1.4s;">
                        <circle cx="28" cy="28" r="14" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-dasharray="50" stroke-dashoffset="32" />
                    </svg>
                    <span class="absolute inset-0 flex items-center justify-center">
                        <i class="bx bx-save text-xl" style="color:#ffd700;"></i>
                    </span>
                </div>

                {{-- title --}}
                <div class="text-center">
                    <p class="text-sm font-bold tracking-wide" style="color:#ffffff;">Saving Syllabus…</p>
                    <p class="text-xs mt-1" style="color:rgba(255,255,255,0.4);">This may take a few seconds</p>
                </div>

                {{-- static step list --}}
                <div class="w-full space-y-2">
                    <div class="flex items-center justify-center gap-3 px-3 py-2 rounded-lg"
                        style="background:rgba(255,255,255,0.05);">
                        <i class="bx bx-code-alt text-base shrink-0" style="color:#ffd700;"></i>
                        <span class="text-xs" style="color:rgba(255,255,255,0.75);">Rendering syllabus…</span>
                    </div>
                    <div class="flex items-center justify-center gap-3 px-3 py-2 rounded-lg"
                        style="background:rgba(255,255,255,0.05);">
                        <i class="bx bx-cloud-upload text-base shrink-0" style="color:#ffd700;"></i>
                        <span class="text-xs" style="color:rgba(255,255,255,0.75);">Uploading to Google Drive…</span>
                    </div>
                    {{-- <div class="flex items-center justify-center gap-3 px-3 py-2 rounded-lg"
                        style="background:rgba(255,255,255,0.05);">
                        <i class="bx bx-hdd text-base shrink-0" style="color:#ffd700;"></i>
                        <span class="text-xs" style="color:rgba(255,255,255,0.75);">Saving local backup…</span>
                    </div> --}}
                    <div class="flex items-center justify-center gap-3 px-3 py-2 rounded-lg"
                        style="background:rgba(255,255,255,0.05);">
                        <i class="bx bx-data text-base shrink-0" style="color:#ffd700;"></i>
                        <span class="text-xs" style="color:rgba(255,255,255,0.75);">Freezing version record…</span>
                    </div>
                </div>

            </div>
        </div>

        @php
            $navSteps = [
                'academic_calendar' => ['label' => 'Academic Calendar',  'icon' => 'bx-calendar'],
                'course_components' => ['label' => 'Course Components',  'icon' => 'bx-notepad'],
                'course_outcomes'   => ['label' => 'Course Outcomes',    'icon' => 'bx-book-open'],
                'weekly_coverage'   => ['label' => 'Weekly Coverage',    'icon' => 'bx-calendar-week'],
                'course_evaluation' => ['label' => 'Course Evaluation',  'icon' => 'bx-bar-chart-alt-2'],
                'review'            => ['label' => 'Review & Submit',    'icon' => 'bx-check-shield'],
            ];
            $missingSteps = ['academic_calendar', 'course_components', 'course_outcomes', 'weekly_coverage'];
        @endphp

        {{-- ══ Two-column layout: content left, sticky nav right ══════════════ --}}
        <div class="flex gap-6 items-start">

            {{-- ── Main content ──────────────────────────────────────────────── --}}
            <div class="flex-1 min-w-0">

                {{-- Mobile step strip (visible only below lg) --}}
                <div class="lg:hidden mb-4 overflow-x-auto">
                    <div class="flex items-center gap-1 min-w-max">
                        @foreach ($navSteps as $step => $meta)
                            @php
                                $idx       = array_search($step, $stepsOrder, true);
                                $isCurrent = $currentStep === $step;
                                $isDone    = $currentIndex !== false && $idx !== false && $idx < $currentIndex;
                                $hasMiss   = in_array($step, $missingSteps) && $this->stepHasMissingRequired($step);
                            @endphp
                            <button type="button"
                                wire:click="clickTab('{{ $step }}')"
                                wire:loading.attr="disabled"
                                wire:target="clickTab,goPreviousStep,goNextStep,submitForReview,saveAsDone"
                                class="relative flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors
                                       focus:outline-none disabled:opacity-50
                                       {{ $isCurrent ? 'bg-[#f0fdf4] text-[#15803d] ring-1 ring-[#bbf7d0]' : 'text-slate-500 hover:bg-slate-50' }}">
                                <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0
                                             {{ $isCurrent ? 'bg-[#16a34a] text-white' : ($isDone ? 'bg-[#16a34a] text-white' : 'bg-slate-100 text-slate-400') }}">
                                    @if ($isDone)<i class="bx bx-check text-[10px]"></i>@else{{ $idx + 1 }}@endif
                                </span>
                                <span class="whitespace-nowrap">{{ $meta['label'] }}</span>
                                @if ($hasMiss)
                                    <span class="absolute -top-0.5 -right-0.5 w-2 h-2 rounded-full bg-amber-400"></span>
                                @endif
                            </button>
                            @if (!$loop->last)
                                <i class="bx bx-chevron-right text-slate-300 text-sm shrink-0"></i>
                            @endif
                        @endforeach
                    </div>
                </div>

                {{-- Step content --}}
                <div 
                {{-- class="bg-white border border-[#dedee2] rounded-xl shadow-sm" --}}
                >

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
                                wire:target="clickTab,goPreviousStep,goNextStep,saveCurrentStep,submitForReview,saveAsDone">
                                <i class="bx bx-chevron-left"></i>
                                <span class="hidden sm:inline">Previous</span>
                            </x-button>
                        @endif
                    </div>
                    <div>
                        @if ($this->hasNextStep())
                            <x-button variant="primary" wire:click="goNextStep" wire:loading.attr="disabled"
                                wire:target="clickTab,goPreviousStep,goNextStep,saveCurrentStep,submitForReview,saveAsDone"
                                loading="Saving…">
                                <span class="hidden sm:inline">Next</span> <i class="bx bx-chevron-right"></i>
                            </x-button>
                        @endif
                    </div>
                </div>

            </div>{{-- end main content --}}

            {{-- ── Sticky right-side step navigator ──────────────────────────── --}}
            <aside class="hidden lg:block w-60 shrink-0" style="position: sticky; top: 5rem; align-self: flex-start; max-height: calc(100vh - 6rem); overflow-y: auto;">
                <div class="rounded-xl border border-[#dedee2] bg-white overflow-hidden" style="box-shadow: 0 1px 4px rgba(0,0,0,0.06);">

                    {{-- Nav header --}}
                    <div class="px-4 py-3 border-b border-[#dedee2] bg-[#F5F5F6]">
                        <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-[#797980]">Steps</p>
                    </div>

                    {{-- Step list --}}
                    <nav class="py-1" aria-label="Wizard Steps">
                        @foreach ($navSteps as $step => $meta)
                            @php
                                $idx         = array_search($step, $stepsOrder, true);
                                $isCurrent   = $currentStep === $step;
                                $isCompleted = $currentIndex !== false && $idx !== false && $idx < $currentIndex;
                                $hasMissing  = in_array($step, $missingSteps) && $this->stepHasMissingRequired($step);
                            @endphp
                            <button type="button"
                                wire:click="clickTab('{{ $step }}')"
                                wire:loading.attr="disabled"
                                wire:target="clickTab,goPreviousStep,goNextStep,submitForReview,saveAsDone"
                                class="w-full flex items-center gap-3 px-4 py-2.5 text-left transition-colors
                                       focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed
                                       {{ $isCurrent
                                            ? 'bg-[#f0fdf4] text-[#15803d] font-semibold border-l-[3px] border-[#16a34a]'
                                            : 'text-[#58585e] hover:bg-[#F5F5F6] hover:text-[#36363b] border-l-[3px] border-transparent' }}">

                                {{-- Step number / check --}}
                                <span class="shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-[11px] font-bold
                                             {{ $isCurrent
                                                  ? 'bg-[#16a34a] text-white'
                                                  : ($isCompleted
                                                       ? 'bg-[#16a34a] text-white'
                                                       : 'bg-slate-100 text-slate-400') }}">
                                    @if ($isCompleted)
                                        <i class="bx bx-check text-xs"></i>
                                    @else
                                        {{ $idx + 1 }}
                                    @endif
                                </span>

                                {{-- Label --}}
                                <span class="flex-1 leading-tight text-[12px]">{{ $meta['label'] }}</span>

                                {{-- Missing indicator --}}
                                @if ($hasMissing)
                                    <span class="shrink-0 w-2 h-2 rounded-full bg-amber-400" title="Incomplete"></span>
                                @endif

                            </button>
                        @endforeach
                    </nav>

                </div>

                {{-- Legend — only on Weekly Coverage step --}}
                @if ($currentStep === 'weekly_coverage')
                    <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2 px-4 py-3 rounded-xl border border-[#e2e8f0] bg-white text-[12px]" style="box-shadow:0 1px 4px rgba(0,0,0,.05);">
                        <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8] shrink-0">Legend</span>
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-[#16a34a] shrink-0"></span>
                            <span class="text-[#475569]">Normal week</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-[#16a34a] ring-2 ring-[#bbf7d0] shrink-0"></span>
                            <span class="text-[#475569]">MVGO (Week 1)</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-amber-400 shrink-0"></span>
                            <span class="text-[#475569]">Exam week <span class="text-[#94a3b8]">(locked)</span></span>
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-rose-400 shrink-0"></span>
                            <span class="text-[#475569]">Non-teaching week <span class="text-[#94a3b8]">(locked)</span></span>
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-slate-300 shrink-0"></span>
                            <span class="text-[#475569]">Break <span class="text-[#94a3b8]">(skipped — no week row)</span></span>
                        </span>
                    </div>
                @endif

                {{-- Course Info + PO Reference — only on Course Outcomes step --}}
                @if ($currentStep === 'course_outcomes')
                    <div class="mt-3 space-y-2">
                        <button type="button"
                            x-on:click="$dispatch('open-course-info-drawer')"
                            class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl border border-[#e2e8f0] bg-white text-[13px] font-semibold text-[#36363b] hover:bg-[#F5F5F6] transition-colors"
                            style="box-shadow:0 1px 4px rgba(0,0,0,.05);">
                            <span class="flex items-center gap-2">
                                <span class="flex items-center justify-center w-6 h-6 rounded-lg bg-slate-100 text-slate-500">
                                    <i class="bx bx-book text-sm leading-none"></i>
                                </span>
                                Course Info
                            </span>
                            <i class="bx bx-chevron-right text-slate-400"></i>
                        </button>
                        <button type="button"
                            x-on:click="$dispatch('open-po-ref-drawer')"
                            class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl border border-[#e2e8f0] bg-white text-[13px] font-semibold text-[#36363b] hover:bg-[#F5F5F6] transition-colors"
                            style="box-shadow:0 1px 4px rgba(0,0,0,.05);">
                            <span class="flex items-center gap-2">
                                <span class="flex items-center justify-center w-6 h-6 rounded-lg bg-slate-100 text-slate-500">
                                    <i class="bx bx-list-check text-sm leading-none"></i>
                                </span>
                                PO Reference
                            </span>
                            <i class="bx bx-chevron-right text-slate-400"></i>
                        </button>
                    </div>
                @endif

            </aside>

        </div>{{-- end two-column --}}
    </x-panel>
</div>
