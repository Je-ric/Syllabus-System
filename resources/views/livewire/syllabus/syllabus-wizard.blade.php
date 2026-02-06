@php
    $steps = $syllabus->getWizardSteps();
    $stepsOrder = array_keys($steps);
@endphp

<div x-data="syllabusWizard(@js($stepsOrder), @js($currentStep))"
        class="container mx-auto p-6">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold">{{ $syllabus->id ? 'Edit' : 'Create' }} Syllabus</h1>
        <p class="text-gray-600 mt-1">{{ $course->course_code }} - {{ $course->course_title }}</p>

        <x-button variant="cancel" href="{{ route('syllabus.index') }}">
            <i class="bx bx-chevron-left"></i> Back to Syllabi
        </x-button>
    </div>

    {{-- Progress Steps --}}
    <div class="mb-8 bg-white border rounded-lg p-6">
        <div class="flex items-center justify-between">
            @foreach($steps as $step => $label)
                <div class="flex items-center {{ $loop->last ? '' : 'flex-1' }}">
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-semibold"
                            :class="stepCircleClass('{{ $step }}')">
                            <template x-if="isCompleted('{{ $step }}')">
                                <i class="bx bx-check text-xl"></i>
                            </template>
                            <template x-if="!isCompleted('{{ $step }}')">
                                <span x-text="stepNumber('{{ $step }}')"></span>
                            </template>
                        </div>
                        <span class="text-xs mt-2 text-center"
                            :class="stepLabelClass('{{ $step }}')">
                            {{ $label }}
                        </span>
                    </div>

                    @if(!$loop->last)
                        <div class="flex-1 h-1 mx-2"
                            :class="stepLineClass('{{ $step }}')"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Step Content --}}
    <div class="bg-white border rounded-lg p-6">
        <div x-show="localStep === 'academic_calendar'">
            @include('livewire.syllabus.steps.academic-calendar')
        </div>
        <div x-show="localStep === 'course_components'">
            @include('livewire.syllabus.steps.course-components')
        </div>
        <div x-show="localStep === 'course_outcomes'">
            @include('livewire.syllabus.steps.course-outcomes')
        </div>
        <div x-show="localStep === 'co_po_mapping'">
            @include('livewire.syllabus.steps.co-po-mapping')
        </div>
        <div x-show="localStep === 'review'">
            @include('livewire.syllabus.steps.review')
        </div>
    </div>

    {{-- Buttoness --}}
    <div class="mt-6 flex justify-between items-center">
        <div>
            <x-button variant="cancel"
                    x-show="hasPrevious()"
                    @click="goPrevious()">
                <i class="bx bx-chevron-left"></i> Previous
            </x-button>
        </div>

        <div class="flex items-center gap-4">
            @if($lastSavedAt)
                <span class="text-xs text-gray-500">
                    Draft saved {{ $lastSavedAt }}
                </span>
            @endif

            <button wire:click="saveCurrentStep"
                    class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                <i class="bx bx-save"></i> Save Draft
            </button>

            <x-button variant="primary"
                    x-show="hasNext()"
                    @click="goNext()">
                Next <i class="bx bx-chevron-right"></i>
            </x-button>

            <button x-show="!hasNext()"
                    wire:click="submitForReview"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <i class="bx bx-check-double"></i> Submit for Review
            </button>
        </div>
    </div>

</div>

<script>
    function syllabusWizard(steps, initialStep) {
        return {
            steps,
            localStep: initialStep,

            stepIndex(step) {
                return this.steps.indexOf(step);
            },

            stepNumber(step) {
                return this.stepIndex(step) + 1;
            },

            isCompleted(step) {
                return this.stepIndex(step) < this.stepIndex(this.localStep);
            },

            stepCircleClass(step) {
                if (this.localStep === step) return 'bg-blue-600 text-white';
                if (this.isCompleted(step)) return 'bg-green-500 text-white';
                return 'bg-gray-300 text-gray-600';
            },

            stepLabelClass(step) {
                return this.localStep === step ? 'font-semibold text-blue-600' : 'text-gray-600';
            },

            stepLineClass(step) {
                return this.isCompleted(step) ? 'bg-green-500' : 'bg-gray-300';
            },

            hasNext() {
                return this.stepIndex(this.localStep) < this.steps.length - 1;
            },

            hasPrevious() {
                return this.stepIndex(this.localStep) > 0;
            },

            nextStep() {
                if (!this.hasNext()) return null;
                return this.steps[this.stepIndex(this.localStep) + 1];
            },

            previousStep() {
                if (!this.hasPrevious()) return null;
                return this.steps[this.stepIndex(this.localStep) - 1];
            },

            goToStep(target) {
                if (!target || target === this.localStep) return;
                const previous = this.localStep;
                this.localStep = target;
                this.$wire.saveStep(previous);
                this.$wire.set('currentStep', target);
            },

            goNext() {
                this.goToStep(this.nextStep());
            },

            goPrevious() {
                this.goToStep(this.previousStep());
            },
        };
    }
</script>
