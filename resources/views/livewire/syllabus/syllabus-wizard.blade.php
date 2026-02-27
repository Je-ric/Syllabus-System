@php
    $steps = $syllabus->getWizardSteps();
    $stepsOrder = array_keys($steps);
    $currentIndex = array_search($currentStep, $stepsOrder, true);
@endphp

<div>
    <x-header-with-button title="{{ $syllabus->id ? 'Edit' : 'Create' }} Syllabus"
        description="{{ $course->course_code }} - {{ $course->course_title }}">
        <x-button variant="cancel" href="{{ route('syllabus.index') }}">&larr; Back to Syllabi</x-button>
    </x-header-with-button>

    {{-- ══ Full-screen saving overlay ══════════════════════════════════════════
        Shown only during goNextStep / goPreviousStep / clickTab.
        Disappears the instant Livewire delivers the re-render — which is now
        fast because no child component is remounted.
    ──────────────────────────────────────────────────────────────────────────── --}}
    <div
        wire:loading.flex
        wire:target="goNextStep,goPreviousStep,clickTab"
        class="fixed inset-0 z-9999 hidden items-center justify-center bg-white/70 backdrop-blur-sm">
        <div class="flex flex-col items-center gap-4 px-8 py-6 bg-white rounded-2xl shadow-2xl border border-slate-100">
            <div class="relative w-12 h-12">
                <svg class="absolute inset-0 animate-spin text-blue-500" viewBox="0 0 48 48" fill="none">
                    <circle cx="24" cy="24" r="20" stroke="currentColor" stroke-width="4"
                            stroke-linecap="round" stroke-dasharray="100" stroke-dashoffset="60" />
                </svg>
                <span class="absolute inset-0 flex items-center justify-center">
                    <span class="w-3 h-3 rounded-full bg-blue-600 animate-pulse"></span>
                </span>
            </div>
            <div class="text-center">
                <p class="text-sm font-semibold text-slate-700">Saving &amp; switching…</p>
                <p class="text-xs text-slate-400 mt-0.5">Just a moment</p>
            </div>
        </div>
    </div>

    {{-- ══ Step nav tabs ════════════════════════════════════════════════════════ --}}
    <div class="mb-6 bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <nav class="flex border-b border-slate-200" aria-label="Wizard Steps">
            @foreach($steps as $step => $label)
                @php
                    $index       = array_search($step, $stepsOrder, true);
                    $isCurrent   = $currentStep === $step;
                    $isCompleted = $currentIndex !== false && $index !== false && $index < $currentIndex;
                @endphp
                <button type="button"
                    wire:click="clickTab('{{ $step }}')"
                    wire:loading.attr="disabled"
                    wire:target="clickTab,goPreviousStep,goNextStep,submitForReview"
                    class="flex-1 flex flex-col items-center gap-1 px-2 py-3.5 text-xs font-medium
                            transition-all duration-150 focus:outline-none border-b-2
                            {{ $isCurrent
                                ? 'border-blue-600 text-blue-700 bg-blue-50'
                                : ($isCompleted
                                    ? 'border-green-500 text-green-700 hover:bg-green-50'
                                    : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50') }}
                            disabled:opacity-50 disabled:cursor-not-allowed">
                    <span class="w-7 h-7 rounded-full flex items-center justify-center font-semibold text-xs shrink-0
                                {{ $isCurrent ? 'bg-blue-600 text-white' : ($isCompleted ? 'bg-green-500 text-white' : 'bg-slate-200 text-slate-600') }}">
                        @if($isCompleted)
                            <i class="bx bx-check text-sm"></i>
                        @else
                            {{ $index + 1 }}
                        @endif
                    </span>
                    <span class="hidden sm:block text-center leading-tight">{{ $label }}</span>
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

        {{-- Review --}}
        <div class="{{ $currentStep === 'review' ? 'block' : 'hidden' }} p-5 sm:p-6">
            <livewire:syllabus.steps.review-step :syllabus-id="$syllabus->id" />
        </div>
    </div>

    {{-- ══ Bottom navigation ════════════════════════════════════════════════════ --}}
    <div class="mt-6 flex justify-between items-center">
        <div>
            @if($this->hasPreviousStep())
                <x-button variant="cancel"
                    wire:click="goPreviousStep"
                    wire:loading.attr="disabled"
                    wire:target="clickTab,goPreviousStep,goNextStep,saveCurrentStep,submitForReview">
                    <i class="bx bx-chevron-left"></i> Previous
                </x-button>
            @endif
        </div>

        <div class="flex items-center gap-3">
            @if($this->hasNextStep())
                <x-button variant="primary"
                    wire:click="goNextStep"
                    wire:loading.attr="disabled"
                    wire:target="clickTab,goPreviousStep,goNextStep,saveCurrentStep,submitForReview">
                    Next <i class="bx bx-chevron-right"></i>
                </x-button>
            @else
                <button type="button"
                    wire:click="submitForReview"
                    wire:loading.attr="disabled"
                    wire:target="clickTab,goPreviousStep,goNextStep,saveCurrentStep,submitForReview"
                    class="inline-flex items-center gap-2 px-6 py-2 bg-green-600 text-white text-sm font-semibold
                            rounded-lg hover:bg-green-700 transition-colors
                            disabled:opacity-60 disabled:cursor-not-allowed">
                    <i class="bx bx-check-double"></i> Submit for Review
                </button>
            @endif
        </div>
    </div>
</div>
