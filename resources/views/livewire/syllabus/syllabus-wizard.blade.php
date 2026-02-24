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

    <div class="mb-6 bg-white border border-slate-200 rounded-xl p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="text-sm text-slate-600">
                Step <span class="font-semibold">{{ ($currentIndex === false ? 0 : $currentIndex + 1) }}</span>
                of <span class="font-semibold">{{ count($stepsOrder) }}</span>
            </div>
            <div class="text-sm font-semibold text-slate-800">{{ $steps[$currentStep] ?? 'Step' }}</div>
        </div>
        <div class="flex items-center justify-between">
            @foreach($steps as $step => $label)
                @php
                    $index = array_search($step, $stepsOrder, true);
                    $isCompleted = $currentIndex !== false && $index !== false && $index < $currentIndex;
                    $isCurrent = $currentStep === $step;
                @endphp
                <div class="flex items-center {{ $loop->last ? '' : 'flex-1' }}">
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-semibold
                            {{ $isCurrent ? 'bg-blue-600 text-white ring-4 ring-blue-100' : ($isCompleted ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-600') }}">
                            @if($isCompleted)
                                <i class="bx bx-check text-xl"></i>
                            @else
                                <span>{{ $index + 1 }}</span>
                            @endif
                        </div>
                        <span class="text-xs mt-2 text-center {{ $isCurrent ? 'font-semibold text-blue-700' : 'text-slate-600' }}">
                            {{ $label }}
                        </span>
                    </div>
                    @if(!$loop->last)
                        <div class="flex-1 h-1 mx-2 {{ $isCompleted ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>
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
                <x-button variant="cancel" wire:click="goPreviousStep" wire:loading.attr="disabled" wire:target="goPreviousStep,goNextStep,saveCurrentStep,submitForReview">
                    <i class="bx bx-chevron-left"></i> Previous
                </x-button>
            @endif
        </div>

        <div class="flex items-center gap-4">
            @if($lastSavedAt)
                <span class="text-xs text-gray-500">
                    Draft saved {{ $lastSavedAt }}
                </span>
            @endif

            <button type="button" wire:click="saveCurrentStep" wire:loading.attr="disabled"
                wire:target="goPreviousStep,goNextStep,saveCurrentStep,submitForReview"
                class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition disabled:opacity-60 disabled:cursor-not-allowed">
                <i class="bx bx-save"></i> Save Draft
            </button>

            @if($this->hasNextStep())
                <x-button variant="primary" wire:click="goNextStep" wire:loading.attr="disabled" wire:target="goPreviousStep,goNextStep,saveCurrentStep,submitForReview">
                    Next <i class="bx bx-chevron-right"></i>
                </x-button>
            @else
                <button type="button" wire:click="submitForReview" wire:loading.attr="disabled"
                    wire:target="goPreviousStep,goNextStep,saveCurrentStep,submitForReview"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition disabled:opacity-60 disabled:cursor-not-allowed">
                    <i class="bx bx-check-double"></i> Submit for Review
                </button>
            @endif
        </div>
    </div>
</div>
