<div class="container mx-auto p-6">

    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('syllabus.index') }}" class="text-blue-600 hover:underline text-sm flex items-center gap-1 mb-4">
            <i class="bx bx-chevron-left"></i> Back to Syllabi
        </a>
        <h1 class="text-2xl font-bold">{{ $syllabus->id ? 'Edit' : 'Create' }} Syllabus</h1>
        <p class="text-gray-600 mt-1">{{ $course->course_code }} - {{ $course->course_title }}</p>
    </div>

    {{-- Progress Steps --}}
    <div class="mb-8 bg-white border rounded-lg p-6">
        <div class="flex items-center justify-between">
            @php
                $steps = $syllabus->getWizardSteps();
                $currentIndex = array_search($currentStep, array_keys($steps));
            @endphp

            @foreach($steps as $step => $label)
                @php
                    $index = array_search($step, array_keys($steps));
                    $isActive = $step === $currentStep;
                    $isCompleted = $index < $currentIndex;
                @endphp

                <div class="flex items-center {{ $loop->last ? '' : 'flex-1' }}">
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-semibold
                            {{ $isActive ? 'bg-blue-600 text-white' : ($isCompleted ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-600') }}">
                            @if($isCompleted)
                                <i class="bx bx-check text-xl"></i>
                            @else
                                {{ $index + 1 }}
                            @endif
                        </div>
                        <span class="text-xs mt-2 text-center {{ $isActive ? 'font-semibold text-blue-600' : 'text-gray-600' }}">
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

    {{-- Step Content --}}
    <div class="bg-white border rounded-lg p-6">
        @if($currentStep === 'academic_calendar')
            @include('livewire.syllabus.steps.academic-calendar')
        @elseif($currentStep === 'course_components')
            @include('livewire.syllabus.steps.course-components')
        @elseif($currentStep === 'course_outcomes')
            @include('livewire.syllabus.steps.course-outcomes')
        @elseif($currentStep === 'co_po_mapping')
            @include('livewire.syllabus.steps.co-po-mapping')
        @elseif($currentStep === 'review')
            @include('livewire.syllabus.steps.review')
        @endif
    </div>

    {{-- Navigation Buttons --}}
    <div class="mt-6 flex justify-between">
        <div>
            @if($syllabus->getPreviousStep())
                <button wire:click="saveAndPrevious"
                        class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    <i class="bx bx-chevron-left"></i> Previous
                </button>
            @endif
        </div>

        <div class="flex gap-3">
            <button wire:click="saveCurrentStep"
                    class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                <i class="bx bx-save"></i> Save Draft
            </button>

            @if($syllabus->getNextStep())
                <button wire:click="saveAndNext"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Next <i class="bx bx-chevron-right"></i>
                </button>
            @else
                <button wire:click="submitForReview"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <i class="bx bx-check-double"></i> Submit for Review
                </button>
            @endif
        </div>
    </div>

    {{-- Loading Indicator --}}
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 flex items-center gap-3">
            <i class="bx bx-loader bx-spin text-2xl text-blue-600"></i>
            <span class="font-medium">Saving...</span>
        </div>
    </div>

</div>
