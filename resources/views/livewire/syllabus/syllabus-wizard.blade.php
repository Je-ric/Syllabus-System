@php
    $steps = $syllabus->getWizardSteps();
    $stepsOrder = array_keys($steps);
    $currentIndex = array_search($currentStep, $stepsOrder, true);
@endphp

<div>
    <x-page-header icon="bx-book-open" title="{{ $syllabus->id ? 'Edit' : 'Create' }} Syllabus"
        desc="{{ $course->course_code }} — {{ $course->course_title }}">
        <x-button variant="cancel" href="{{ route('syllabus.index') }}">
            <i class="bx bx-arrow-back"></i>
            <span class="hidden sm:inline">Back to Syllabi</span>
        </x-button>
    </x-page-header>

    <x-panel>
        {{-- ══ Navigation overlay ══════════════════════════════════════════════════ --}}
        <div wire:loading.class.remove="hidden" wire:target="goNextStep,goPreviousStep,clickTab"
            class="hidden fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0" style="background: rgba(49,49,49,0.45); backdrop-filter: blur(4px);"></div>

            <div class="relative w-5/12 h-5/12 flex flex-col items-center gap-4 px-10 py-8 rounded-xl shadow-2xl border"
                style="background: var(--white); border-color: var(--gray);">

                <div class="relative w-32 h-32 flex items-center justify-center">
                    <svg class="absolute inset-0 animate-spin" viewBox="0 0 64 64" fill="none"
                        style="color: var(--clsu-yellow);">
                        <circle cx="32" cy="32" r="27" stroke="currentColor" stroke-width="3"
                            stroke-linecap="round" stroke-dasharray="130" stroke-dashoffset="90" />
                    </svg>
                    <svg class="absolute inset-0" viewBox="0 0 64 64" fill="none" style="color: var(--gray);">
                        <circle cx="32" cy="32" r="27" stroke="currentColor" stroke-width="2" />
                    </svg>
                    <img src="{{ asset('assets/clsu-logo-green.png') }}" alt="CLSU"
                        class="relative w-24 h-24 object-contain" />
                </div>

                <div class="text-center">
                    <p class="text-md font-semibold" style="color: var(--black-25);">Saving changes…</p>
                    <p class="text-sm mt-1" style="color: var(--black-20);">Please wait</p>
                </div>

                 <div class="flex justify-center space-x-1">
                <div class="w-2 h-2 bg-green-600 rounded-full animate-bounce"></div>
                <div class="w-2 h-2 bg-green-600 rounded-full animate-bounce" style="animation-delay: 0.1s;"></div>
                <div class="w-2 h-2 bg-green-600 rounded-full animate-bounce" style="animation-delay: 0.2s;"></div>
            </div>
            </div>
        </div>

        {{-- ══ Save-as-Done overlay ════════════════════════════════════════════════ --}}
        <div wire:loading.class.remove="hidden" wire:target="saveAsDone"
            class="hidden fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0" style="background: rgba(49,49,49,0.5); backdrop-filter: blur(6px);"></div>

            <div class="relative flex flex-col items-center gap-6 px-10 py-9 rounded-xl shadow-2xl border"
                style="background: var(--white); border-color: var(--gray); width: 300px;">

                <div class="relative w-16 h-16 flex items-center justify-center">
                    <svg class="absolute inset-0 animate-spin" viewBox="0 0 64 64" fill="none"
                        style="color: var(--clsu-yellow);">
                        <circle cx="32" cy="32" r="27" stroke="currentColor" stroke-width="3"
                            stroke-linecap="round" stroke-dasharray="130" stroke-dashoffset="90" />
                    </svg>
                    <svg class="absolute inset-0" viewBox="0 0 64 64" fill="none" style="color: var(--gray);">
                        <circle cx="32" cy="32" r="27" stroke="currentColor" stroke-width="2" />
                    </svg>
                    <svg class="absolute inset-0 animate-spin" viewBox="0 0 64 64" fill="none"
                        style="color: var(--clsu-green); animation-direction: reverse; animation-duration: 1.4s;">
                        <circle cx="32" cy="32" r="18" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-dasharray="60" stroke-dashoffset="40" />
                    </svg>
                    <img src="{{ asset('assets/CLSU-LOGO-removebg.png') }}" alt="CLSU"
                        class="relative w-9 h-9 object-contain" />
                </div>

                <div class="text-center">
                    <p class="text-sm font-bold tracking-wide" style="color: var(--black-25);">Saving Syllabus…</p>
                    <p class="text-xs mt-1" style="color: var(--black-20);">This may take a few seconds</p>
                </div>

                <div class="w-full space-y-2">
                    <div class="flex items-center justify-center gap-3 px-3 py-2 rounded-lg"
                        style="background: var(--white-50);">
                        <i class="bx bx-code-alt text-base shrink-0" style="color: var(--clsu-gold);"></i>
                        <span class="text-xs" style="color: var(--black-20);">Rendering syllabus…</span>
                    </div>
                    <div class="flex items-center justify-center gap-3 px-3 py-2 rounded-lg"
                        style="background: var(--white-50);">
                        <i class="bx bx-cloud-upload text-base shrink-0" style="color: var(--clsu-gold);"></i>
                        <span class="text-xs" style="color: var(--black-20);">Uploading to Google Drive…</span>
                    </div>
                    <div class="flex items-center justify-center gap-3 px-3 py-2 rounded-lg"
                        style="background: var(--white-50);">
                        <i class="bx bx-data text-base shrink-0" style="color: var(--clsu-gold);"></i>
                        <span class="text-xs" style="color: var(--black-20);">Freezing version record…</span>
                    </div>
                </div>

                <div class="w-16 h-0.5 rounded-full" style="background: var(--green-grad);"></div>
            </div>
        </div>

        @php
            $navSteps = [
                'academic_calendar' => ['label' => 'Academic Calendar', 'icon' => 'bx-calendar', 'short' => 'Calendar'],
                'course_components' => [
                    'label' => 'Course Components',
                    'icon' => 'bx-notepad',
                    'short' => 'Components',
                ],
                'course_outcomes' => ['label' => 'Course Outcomes', 'icon' => 'bx-book-open', 'short' => 'Outcomes'],
                'weekly_coverage' => [
                    'label' => 'Weekly Coverage',
                    'icon' => 'bx-calendar-week',
                    'short' => 'Coverage',
                ],
                'course_evaluation' => [
                    'label' => 'Course Evaluation',
                    'icon' => 'bx-bar-chart-alt-2',
                    'short' => 'Evaluation',
                ],
                'review' => ['label' => 'Review & Submit', 'icon' => 'bx-check-shield', 'short' => 'Review'],
            ];
            $missingSteps = ['academic_calendar', 'course_components', 'course_outcomes', 'weekly_coverage'];
        @endphp

        {{-- ══ Two-column layout ════════════════════════════════════════════════ --}}
        <div class="flex gap-6 items-start">

            {{-- ── Main content ──────────────────────────────────────────────── --}}
            <div class="flex-1 min-w-0">

                {{-- Mobile step strip --}}
                <div class="lg:hidden mb-4 overflow-x-auto">
                    <div class="flex items-center gap-1 min-w-max">
                        @foreach ($navSteps as $step => $meta)
                            @php
                                $idx = array_search($step, $stepsOrder, true);
                                $isCurrent = $currentStep === $step;
                                $isDone = $currentIndex !== false && $idx !== false && $idx < $currentIndex;
                                $hasMiss = in_array($step, $missingSteps) && $this->stepHasMissingRequired($step);
                            @endphp
                            <button type="button" wire:click="clickTab('{{ $step }}')"
                                wire:loading.attr="disabled"
                                wire:target="clickTab,goPreviousStep,goNextStep,submitForReview,saveAsDone"
                                class="relative flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors focus:outline-none disabled:opacity-50
                                       {{ $isCurrent ? 'bg-[#f0fdf4] text-[#15803d] ring-1 ring-[#bbf7d0]' : 'text-slate-500 hover:bg-slate-50' }}">
                                <span
                                    class="w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold shrink-0
                                             {{ $isCurrent ? 'bg-[#16a34a] text-white' : ($isDone ? 'bg-[#16a34a] text-white' : 'bg-slate-100 text-slate-400') }}">
                                    @if ($isDone)
                                        <i class="bx bx-check text-xs"></i>@else{{ $idx + 1 }}
                                    @endif
                                </span>
                                <span class="whitespace-nowrap">{{ $meta['short'] }}</span>
                                @if ($hasMiss)
                                    <span
                                        class="absolute -top-0.5 -right-0.5 w-2 h-2 rounded-full bg-amber-400"></span>
                                @endif
                            </button>
                            @if (!$loop->last)
                                <i class="bx bx-chevron-right text-slate-300 text-sm shrink-0"></i>
                            @endif
                        @endforeach
                    </div>
                </div>

                {{-- Step content card --}}
                <div class="bg-white rounded-xl border border-[#e2e8f0]"
                    style="box-shadow: 0 2px 12px rgba(0,0,0,.06);">

                    {{-- Step banner --}}
                    @php
                        $bannerMap = [
                            'academic_calendar' => [
                                'num' => 1,
                                'label' => 'Step 1 of 6 — Academic Calendar',
                                'icon' => 'bx-calendar',
                                'color' => '#92d12c', // --green — lightest, "just starting"
                            ],
                            'course_components' => [
                                'num' => 2,
                                'label' => 'Step 2 of 6 — Course Components',
                                'icon' => 'bx-notepad',
                                'color' => '#4eab18', // --green-50
                            ],
                            'course_outcomes' => [
                                'num' => 3,
                                'label' => 'Step 3 of 6 — Course Outcomes',
                                'icon' => 'bx-book-open',
                                'color' => '#009639', // --clsu-green — the flagship brand color at the midpoint
                            ],
                            'weekly_coverage' => [
                                'num' => 4,
                                'label' => 'Step 4 of 6 — Weekly Coverage',
                                'icon' => 'bx-calendar-week',
                                'color' => '#038303', // --green-100
                            ],
                            'course_evaluation' => [
                                'num' => 5,
                                'label' => 'Step 5 of 6 — Course Evaluation',
                                'icon' => 'bx-bar-chart-alt-2',
                                'color' => '#1a5f30', // --clsu-cobra
                            ],
                            'review' => [
                                'num' => 6,
                                'label' => 'Step 6 of 6 — Review & Submit',
                                'icon' => 'bx-check-shield',
                                'color' => '#003a10', // --green-150 — deepest, "arrival"
                            ],
                        ];
                        $banner = $bannerMap[$currentStep] ?? null;
                    @endphp
                    @if ($banner)
                        <div class="flex items-center gap-3 px-5 py-2.5 border-b border-[#e2e8f0] rounded-t-xl"
                            style="background: linear-gradient(135deg, {{ $banner['color'] }}18 0%, #f8fafc 100%);">
                            <span
                                class="flex items-center justify-center w-6 h-6 rounded-full text-white text-xs font-bold shrink-0"
                                style="background-color: {{ $banner['color'] }};">
                                {{ $banner['num'] }}
                            </span>
                            <span class="text-xs font-semibold tracking-wide" style="color: {{ $banner['color'] }};">
                                {{ $banner['label'] }}
                            </span>
                            {{-- Progress bar --}}
                            <div class="flex-1 ml-2 hidden sm:block">
                                <div class="h-1 rounded-full bg-slate-100 overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500"
                                        style="width: {{ ($banner['num'] / 6) * 100 }}%; background-color: {{ $banner['color'] }};">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

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
            <aside class="hidden lg:block w-60 shrink-0"
                style="position: sticky; top: 5rem; align-self: flex-start; max-height: calc(100vh - 6rem); overflow-y: auto;">

                {{-- Step navigator card --}}
                <div class="rounded-xl border border-[#dedee2] bg-white overflow-hidden"
                    style="box-shadow: 0 2px 12px rgba(0,0,0,.08);">

                    {{-- Nav header --}}
                    <div class="px-4 py-3 border-b"
                        style="background: linear-gradient(135deg, #002a0c 0%, #004d16 100%); border-color: rgba(255,215,0,0.3);">
                        <div class="flex items-center gap-2">
                            <i class="bx bx-list-check text-sm" style="color: #ffd700;"></i>
                            <p class="text-xs font-bold uppercase tracking-widest" style="color: #ffd700;">Wizard
                                Steps</p>
                        </div>
                        {{-- Mini progress --}}
                        <div class="mt-2 flex items-center gap-1">
                            @foreach ($navSteps as $step => $meta)
                                @php $idx = array_search($step, $stepsOrder, true); @endphp
                                <div
                                    class="flex-1 h-1 rounded-full transition-all duration-300
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
                    <nav class="py-1.5" aria-label="Wizard Steps">
                        @foreach ($navSteps as $step => $meta)
                            @php
                                $idx = array_search($step, $stepsOrder, true);
                                $isCurrent = $currentStep === $step;
                                $isCompleted = $currentIndex !== false && $idx !== false && $idx < $currentIndex;
                                $hasMissing = in_array($step, $missingSteps) && $this->stepHasMissingRequired($step);
                            @endphp
                            <button type="button" wire:click="clickTab('{{ $step }}')"
                                wire:loading.attr="disabled"
                                wire:target="clickTab,goPreviousStep,goNextStep,submitForReview,saveAsDone"
                                class="w-full flex items-center gap-3 px-4 py-2.5 text-left transition-all duration-150
                                       focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed
                                       {{ $isCurrent
                                           ? 'bg-[#f0fdf4] text-[#15803d] font-semibold border-l-[3px] border-[#009639]'
                                           : 'text-[#58585e] hover:bg-[#F5F5F6] hover:text-[#36363b] border-l-[3px] border-transparent' }}">

                                {{-- Step indicator --}}
                                <span
                                    class="shrink-0 w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-200
                                             {{ $isCurrent ? 'text-white shadow-md' : ($isCompleted ? 'text-white' : 'bg-slate-100 text-slate-400') }}"
                                    @if ($isCurrent) style="background: linear-gradient(135deg, #009639 0%, #16a34a 100%); box-shadow: 0 2px 8px rgba(0,150,57,0.4);"
                                      @elseif($isCompleted) style="background: #16a34a;" @endif>
                                    @if ($isCompleted)
                                        <i class="bx bx-check text-xs"></i>
                                    @else
                                        {{ $idx + 1 }}
                                    @endif
                                </span>

                                {{-- Label --}}
                                <span class="flex-1 leading-tight text-xs">{{ $meta['label'] }}</span>

                                {{-- Missing indicator --}}
                                @if ($hasMissing)
                                    <span class="shrink-0 w-2 h-2 rounded-full bg-amber-400 animate-pulse"
                                        title="Incomplete"></span>
                                @endif

                            </button>
                        @endforeach
                    </nav>

                </div>

                {{-- Legend — only on Weekly Coverage step --}}
                @if ($currentStep === 'weekly_coverage')
                    <div class="mt-3 px-4 py-3.5 rounded-xl border border-[#e2e8f0] bg-white"
                        style="box-shadow: 0 1px 4px rgba(0,0,0,.05);">
                        <p class="text-xs font-bold uppercase tracking-widest text-[#94a3b8] mb-3">Legend</p>
                        <div class="space-y-2">
                            <span class="flex items-center gap-2 text-xs text-[#475569]">
                                <span class="w-3 h-3 rounded-full bg-[#16a34a] shrink-0"></span> Normal week
                            </span>
                            <span class="flex items-center gap-2 text-xs text-[#475569]">
                                <span class="w-3 h-3 rounded-full bg-[#16a34a] ring-2 ring-[#bbf7d0] shrink-0"></span>
                                MVGO (Week 1)
                            </span>
                            <span class="flex items-center gap-2 text-xs text-[#475569]">
                                <span class="w-3 h-3 rounded-full bg-amber-400 shrink-0"></span>
                                Exam week <span class="text-[#94a3b8]">(locked)</span>
                            </span>
                            <span class="flex items-center gap-2 text-xs text-[#475569]">
                                <span class="w-3 h-3 rounded-full bg-rose-400 shrink-0"></span>
                                Non-teaching <span class="text-[#94a3b8]">(locked)</span>
                            </span>
                            <span class="flex items-center gap-2 text-xs text-[#475569]">
                                <span class="w-3 h-3 rounded-full bg-slate-300 shrink-0"></span>
                                Break <span class="text-[#94a3b8]">(skipped)</span>
                            </span>
                        </div>
                    </div>
                @endif

                {{-- Course Info + PO Reference — only on Course Outcomes step --}}
                @if ($currentStep === 'course_outcomes')
                    <div class="mt-3 space-y-2">
                        <button type="button" x-on:click="$dispatch('open-course-info-drawer')"
                            class="w-full flex items-center justify-between px-4 py-3 rounded-xl border border-[#e2e8f0] bg-white text-sm font-semibold text-[#36363b] hover:bg-[#F5F5F6] hover:border-[#bbf7d0] transition-all"
                            style="box-shadow:0 1px 4px rgba(0,0,0,.05);">
                            <span class="flex items-center gap-2.5">
                                <span
                                    class="flex items-center justify-center w-7 h-7 rounded-lg bg-[#dcfce7] text-[#16a34a]">
                                    <i class="bx bx-book text-sm leading-none"></i>
                                </span>
                                Course Info
                            </span>
                            <i class="bx bx-chevron-right text-slate-400"></i>
                        </button>
                        <button type="button" x-on:click="$dispatch('open-po-ref-drawer')"
                            class="w-full flex items-center justify-between px-4 py-3 rounded-xl border border-[#e2e8f0] bg-white text-sm font-semibold text-[#36363b] hover:bg-[#F5F5F6] hover:border-[#bbf7d0] transition-all"
                            style="box-shadow:0 1px 4px rgba(0,0,0,.05);">
                            <span class="flex items-center gap-2.5">
                                <span
                                    class="flex items-center justify-center w-7 h-7 rounded-lg bg-[#dbeafe] text-[#1d4ed8]">
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
