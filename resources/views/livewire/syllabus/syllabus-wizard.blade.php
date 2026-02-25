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

    <div class="mb-6 bg-white border border-slate-200 rounded-xl overflow-hidden">
        <nav class="flex border-b border-slate-200" aria-label="Wizard Steps">
            @foreach($steps as $step => $label)
                @php
                    $index = array_search($step, $stepsOrder, true);
                    $isCurrent = $currentStep === $step;
                    $isCompleted = $currentIndex !== false && $index !== false && $index < $currentIndex;
                @endphp
                <button type="button"
                    wire:click="clickTab('{{ $step }}')"
                    wire:loading.attr="disabled"
                    wire:target="clickTab,goPreviousStep,goNextStep,submitForReview"
                    class="flex-1 flex flex-col items-center gap-1 px-3 py-4 text-xs font-medium transition-all duration-200 focus:outline-none border-b-2
                        {{ $isCurrent
                            ? 'border-blue-600 text-blue-700 bg-blue-50'
                            : ($isCompleted
                                ? 'border-green-500 text-green-700 hover:bg-green-50'
                                : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50') }}">
                    <span class="w-7 h-7 rounded-full flex items-center justify-center font-semibold text-xs
                        {{ $isCurrent ? 'bg-blue-600 text-white' : ($isCompleted ? 'bg-green-500 text-white' : 'bg-slate-200 text-slate-600') }}">
                        @if($isCompleted)
                            <i class="bx bx-check"></i>
                        @else
                            {{ $index + 1 }}
                        @endif
                    </span>
                    <span class="hidden sm:block text-center leading-tight">{{ $label }}</span>
                </button>
            @endforeach
        </nav>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl p-6">
        @if($currentStep === 'academic_calendar')
            <livewire:syllabus.steps.academic-calendar-step :syllabus-id="$syllabus->id"
                :key="'academic-calendar-step-' . $syllabus->id . '-' . $currentStep" />
        @elseif($currentStep === 'course_components')
            <livewire:syllabus.steps.components-step :syllabus-id="$syllabus->id"
                :key="'components-step-' . $syllabus->id . '-' . $currentStep" />
        @elseif($currentStep === 'course_outcomes')
            <livewire:syllabus.steps.course-outcomes-step :syllabus-id="$syllabus->id"
                :key="'course-outcomes-step-' . $syllabus->id . '-' . $currentStep" />
        @elseif($currentStep === 'weekly_coverage')
            <livewire:syllabus.steps.weekly-coverage-step :syllabus-id="$syllabus->id"
                :key="'weekly-coverage-step-' . $syllabus->id . '-' . $currentStep" />
        @elseif($currentStep === 'review')
            <livewire:syllabus.steps.review-step :syllabus-id="$syllabus->id"
                :key="'review-step-' . $syllabus->id . '-' . $currentStep" />
        @endif
    </div>

    <div class="mt-6 flex justify-between items-center">
        <div>
            @if($this->hasPreviousStep())
                <x-button variant="cancel" wire:click="goPreviousStep" wire:loading.attr="disabled" wire:target="clickTab,goPreviousStep,goNextStep,saveCurrentStep,submitForReview">
                    <span wire:loading wire:target="goPreviousStep">
                        <i class="bx bx-loader-alt bx-spin"></i> Saving...
                    </span>
                    <i class="bx bx-chevron-left"></i> Previous
                </x-button>
            @endif
        </div>

        <div class="flex items-center gap-4">
            @if($this->hasNextStep())
                <x-button variant="primary" wire:click="goNextStep" wire:loading.attr="disabled" wire:target="clickTab,goPreviousStep,goNextStep,saveCurrentStep,submitForReview">
                    <span wire:loading wire:target="goNextStep">
                        <i class="bx bx-loader-alt bx-spin"></i> Saving...
                    </span>
                    Next <i class="bx bx-chevron-right"></i>
                </x-button>
            @else
                <button type="button" wire:click="submitForReview" wire:loading.attr="disabled"
                    wire:target="clickTab,goPreviousStep,goNextStep,saveCurrentStep,submitForReview"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition disabled:opacity-60 disabled:cursor-not-allowed">
                    <i class="bx bx-check-double"></i> Submit for Review
                </button>
            @endif
        </div>
    </div>
</div>
