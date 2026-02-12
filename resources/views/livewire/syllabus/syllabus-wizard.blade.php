@php
    $steps = $syllabus->getWizardSteps();
    $stepsOrder = array_keys($steps);
@endphp

<div x-data="syllabusWizard(@js($stepsOrder),
                            @entangle('currentStep').live,
                            @js($steps)
                            )"
    >


    <x-header-with-button title="{{ $syllabus->id ? 'Edit' : 'Create' }} Syllabus"
                            description="{{ $course->course_code }} - {{ $course->course_title }}">
        <x-button variant="cancel" href="{{ route('syllabus.index') }}">← Back to Syllabi</x-button>
    </x-header-with-button>

    {{-- Progress Steps --}}
    <div class="mb-6 bg-white border border-slate-200 rounded-xl p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="text-sm text-slate-600">
                Step <span class="font-semibold" x-text="stepNumber(localStep)"></span>
                of <span class="font-semibold" x-text="steps.length"></span>
            </div>
            <div class="text-sm font-semibold text-slate-800" x-text="labels[localStep]"></div>
        </div>
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
    <div class="bg-white border border-slate-200 rounded-xl p-6">
        <div x-show="localStep === 'academic_calendar'" x-cloak>
            @include('livewire.syllabus.steps.academic-calendar')
        </div>
        <div x-show="localStep === 'course_components'" x-cloak>
            @include('livewire.syllabus.steps.course-components')
        </div>
        <div x-show="localStep === 'course_outcomes'" x-cloak>
            @include('livewire.syllabus.steps.course-outcomes')
        </div>
        <div x-show="localStep === 'co_po_mapping'" x-cloak>
            @include('livewire.syllabus.steps.co-po-mapping')
        </div>
        <div x-show="localStep === 'weekly_coverage'" x-cloak>
            @include('livewire.syllabus.steps.weekly-coverage')
        </div>
        <div x-show="localStep === 'review'" x-cloak>
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

            <button type="button"
                    wire:click="saveCurrentStep"
                    class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                <i class="bx bx-save"></i> Save Draft
            </button>

            <x-button variant="primary"
                    x-show="hasNext()"
                    @click="goNext()">
                Next <i class="bx bx-chevron-right"></i>
            </x-button>

            <button type="button"
                    x-show="!hasNext()"
                    wire:click="submitForReview"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <i class="bx bx-check-double"></i> Submit for Review
            </button>
        </div>
    </div>

</div>

<script>
    function syllabusWizard(steps, boundStep, labels) {
        return {
            steps,
            labels,
            localStep: boundStep,
            isNavigating: false,

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
                if (this.localStep === step) return 'bg-blue-600 text-white ring-4 ring-blue-100';
                if (this.isCompleted(step)) return 'bg-green-500 text-white';
                return 'bg-gray-300 text-gray-600';
            },

            stepLabelClass(step) {
                return this.localStep === step ? 'font-semibold text-blue-700' : 'text-slate-600';
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

            async flushCourseOutcomeDrafts() {
                if (this.localStep !== 'course_outcomes') return;

                const rows = Array.from(document.querySelectorAll('textarea[data-co-rowkey]'))
                    .map((el) => ({
                        rowKey: el.dataset.coRowkey,
                        description: el.value ?? '',
                    }));

                if (rows.length > 0) {
                    await this.$wire.syncCourseOutcomeDescriptions(rows);
                }
            },

            async goToStep(target) {
                if (!target || target === this.localStep || this.isNavigating) return;
                this.isNavigating = true;
                const previous = this.localStep;
                try {
                    await this.flushCourseOutcomeDrafts();
                    await this.$wire.navigateToStep(previous, target);
                    this.localStep = target;
                } finally {
                    this.isNavigating = false;
                }
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
