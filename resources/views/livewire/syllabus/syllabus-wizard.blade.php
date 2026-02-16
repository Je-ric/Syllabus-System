@php
    $steps = $syllabus->getWizardSteps();
    $stepsOrder = array_keys($steps);
@endphp

<div x-data="syllabusWizard(
        @js($stepsOrder),
        @entangle('currentStep'),
        @js($steps),
        @entangle('stepDirty').live
    )"
    x-on:lw-toast.window="showToast($event.detail.type, $event.detail.message)">


    <x-header-with-button title="{{ $syllabus->id ? 'Edit' : 'Create' }} Syllabus"
                            description="{{ $course->course_code }} - {{ $course->course_title }}">
        <x-button variant="cancel" href="{{ route('syllabus.index') }}"><- Back to Syllabi</x-button>
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

        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-2">
            @foreach($steps as $step => $label)
                <div class="rounded-lg border px-3 py-2 text-xs
                    {{ $currentStep === $step ? 'border-blue-300 bg-blue-50' : 'border-slate-200 bg-slate-50' }}">
                    <div class="font-semibold text-slate-700">{{ $label }}</div>
                    @if($this->stepHasMissingRequired($step))
                        <div class="mt-1 text-rose-700">Required fields missing</div>
                    @else
                        <div class="mt-1 text-emerald-700">Ready</div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{--
    <div wire:loading.flex wire:target="navigateToStep,saveCurrentStep,submitForReview,generateWeeklyCoverage"
        class="mb-4 items-center gap-2 text-xs text-slate-600 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2">
        <i class="bx bx-loader-alt bx-spin text-base"></i>
        <span>Working on your latest action...</span>
    </div>
    --}}

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

    {{-- Realtime Toast --}}
    <div x-show="toast.show" x-transition
        class="fixed top-6 right-6 z-9999 px-4 py-3 rounded-lg shadow-lg text-sm font-medium"
        :class="{
            'bg-emerald-600 text-white': toast.type === 'success',
            'bg-rose-600 text-white': toast.type === 'error',
            'bg-amber-500 text-white': toast.type === 'warning',
            'bg-slate-700 text-white': toast.type === 'info'
        }">
        <span x-text="toast.message"></span>
    </div>

    {{-- Buttons --}}
    <div class="mt-6 flex justify-between items-center">
        <div>
            <x-button variant="cancel"
                    x-show="hasPrevious()"
                    @click.prevent="goPrevious()"
                    x-bind:disabled="isPrevDisabled()"
                    x-bind:class="isPrevDisabled() ? 'opacity-60 cursor-not-allowed' : ''">
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
                    x-on:click="saveDraft()"
                    :disabled="isNavigating"
                    :class="isNavigating ? 'opacity-60 cursor-not-allowed' : ''"
                    class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                <i class="bx bx-save"></i> Save Draft
            </button>

            <x-button variant="primary"
                    x-show="hasNext()"
                    @click.prevent="goNext()"
                    x-bind:disabled="isNextDisabled()"
                    x-bind:class="isNextDisabled() ? 'opacity-60 cursor-not-allowed' : ''">
                Next <i class="bx bx-chevron-right"></i>
            </x-button>

            <button type="button"
                    x-show="!hasNext()"
                    x-on:click="submitForReview()"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <i class="bx bx-check-double"></i> Submit for Review
            </button>
        </div>
    </div>

</div>

<script>
    function syllabusWizard(steps, boundStep, labels, stepDirty) {
        return {
            steps,
            labels,
            stepDirty,
            localStep: boundStep,
            isNavigating: false,
            toast: {
                show: false,
                type: 'info',
                message: '',
                timeout: null,
            },

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

            getScrollContainer() {
                return document.querySelector('main.overflow-y-auto');
            },

            async preserveScroll(action) {
                const container = this.getScrollContainer();
                const previousContainerTop = container ? container.scrollTop : null;
                const previousWindowTop = window.scrollY;

                const result = await action();

                requestAnimationFrame(() => {
                    if (container) {
                        container.scrollTop = previousContainerTop ?? 0;
                    }
                    window.scrollTo(0, previousWindowTop);
                });

                return result;
            },

            isNextDisabled() {
                return this.isNavigating;
            },

            isPrevDisabled() {
                return this.isNavigating;
            },

            showToast(type, message) {
                if (this.toast.timeout) {
                    clearTimeout(this.toast.timeout);
                }
                this.toast.type = type || 'info';
                this.toast.message = message || '';
                this.toast.show = true;
                this.toast.timeout = setTimeout(() => {
                    this.toast.show = false;
                }, 2600);
            },

            async goToStep(target) {
                if (!target || target === this.localStep || this.isNavigating) return;
                if (this.localStep === 'course_outcomes' && !!(this.stepDirty?.course_outcomes)) {
                    this.showToast('warning', 'Save Course Outcomes first before proceeding.');
                    return;
                }
                this.isNavigating = true;
                const previous = this.localStep;
                this.localStep = target; // Optimistic UI switch for faster navigation feel.
                try {
                    await this.preserveScroll(async () => {
                        await this.$wire.navigateToStep(previous, target);
                    });
                } catch (error) {
                    this.localStep = previous;
                    throw error;
                } finally {
                    this.isNavigating = false;
                }
            },

            async saveDraft() {
                if (this.isNavigating) return;
                this.isNavigating = true;
                try {
                    await this.preserveScroll(async () => {
                        await this.$wire.saveCurrentStep();
                    });

                    // Livewire action return payload is not always reliable on the client.
                    // Infer save result from dirty state after the server roundtrip.
                    const stillUnsavedCo = this.localStep === 'course_outcomes' && !!(this.stepDirty?.course_outcomes);
                    if (this.localStep === 'course_outcomes' && !stillUnsavedCo) {
                        this.showToast('success', 'Course Outcomes saved.');
                    }
                } finally {
                    this.isNavigating = false;
                }
            },

            async submitForReview() {
                if (this.isNavigating) return;
                this.isNavigating = true;
                try {
                    await this.preserveScroll(async () => {
                        await this.$wire.submitForReview();
                    });
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
