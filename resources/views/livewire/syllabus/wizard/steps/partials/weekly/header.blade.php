{{-- weekly-partials/header.blade.php --}}

<div class="mb-5">

    <x-wizard.step-header
        title="Weekly Coverage"
        description="Weeks are auto-generated from the academic calendar. Fill in coverage details per week."
        :step="$stepNumber">

        @if ($weeksGenerated)
            <div class="flex items-center gap-2 flex-wrap">

                {{-- Destructive path — leftmost so it's visually separated from the safe actions --}}
                <x-ui.button variant="sm-danger"
                    x-on:click="$wire.confirmHardReset()"
                    loading="Resetting…">
                    <i class="bx bx-trash"></i> Hard Reset
                </x-ui.button>

                <span class="w-px h-5 bg-[#E4E7EC] shrink-0"></span>

                {{-- Soft path: updates week dates and exam labels, keeps faculty content intact --}}
                <div class="relative inline-flex items-center">
                    <x-ui.button variant="sm-info"
                        x-on:click="$wire.confirmRefreshDates()"
                        loading="Refreshing…">
                        <i class="bx bx-calendar-check"></i> Refresh Dates
                    </x-ui.button>
                    <span
                        title="Updates week date ranges and exam labels from the current calendar. Your topics, learning outcomes, assessments, and references stay untouched."
                        class="absolute -top-1.5 -right-1.5 flex items-center justify-center
                               w-4 h-4 rounded-full bg-[#dbeafe] border border-[#bfdbfe]
                               text-[9px] font-bold text-[#1d4ed8] cursor-help select-none
                               hover:bg-[#bfdbfe] transition-colors">
                        ?
                    </span>
                </div>

                <x-ui.button variant="sm-add"
                    x-on:click="async () => {
                        $dispatch('syllabus-save-started');
                        try {
                            await $wire.saveAllWeeklyEntries();
                        } catch (error) {
                            const errorMessage = error?.message || 'Failed to save weeks. Please try again.';
                            window.dispatchEvent(new CustomEvent('lw-toast', {
                                detail: { type: 'error', message: errorMessage }
                            }));
                        }
                    }"
                    wireTarget="saveAllWeeklyEntries"
                    loading="Saving…">
                    <i class="bx bx-save"></i> Save All
                </x-ui.button>

            </div>
        @else
            <x-ui.button variant="sm-add"
                wire:click="generateWeeklyCoverage"
                :disabled="! $academic_calendar_id"
                wireTarget="generateWeeklyCoverage"
                loading="Generating…">
                <i class="bx bx-calendar-plus"></i> Generate Weeks
            </x-ui.button>
        @endif

    </x-wizard.step-header>

    @if (! $weeksGenerated && ! $academic_calendar_id)
        <x-feedback-status.alert type="error" :showTitle="false">
            No academic calendar selected. Go back to Step 1 and select one before generating weeks.
        </x-feedback-status.alert>
    @endif

</div>

{{-- Confirmation Modal for Hard Reset --}}
<div x-data="{ showHardResetModal: false }"
     x-on:confirm-hard-reset.window="showHardResetModal = true"
     x-on:close-hard-reset-modal.window="showHardResetModal = false"
     class="relative z-50">
    
    <template x-if="showHardResetModal">
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center p-4"
             x-on:click.self="$dispatch('close-hard-reset-modal')">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
                <div class="flex items-start gap-4">
                    <div class="shrink-0 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                        <i class="bx bx-trash text-red-600 text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Confirm Hard Reset</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            This will permanently delete ALL weeks and every piece of content you have entered (topics, learning outcomes, assessments, references, evaluation weights). This action cannot be undone.
                        </p>
                        <p class="text-sm font-medium text-red-600 mb-4">Are you sure you want to continue?</p>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button"
                            x-on:click="$dispatch('close-hard-reset-modal')"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Cancel
                    </button>
                    <button type="button"
                            x-on:click="$dispatch('syllabus-save-started'); $wire.hardResetWeeks(); $dispatch('close-hard-reset-modal')"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                        Yes, Reset Everything
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

{{-- Confirmation Modal for Refresh Dates --}}
<div x-data="{ showRefreshModal: false }"
     x-on:confirm-refresh-dates.window="showRefreshModal = true"
     x-on:close-refresh-modal.window="showRefreshModal = false"
     class="relative z-50">
    
    <template x-if="showRefreshModal">
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center p-4"
             x-on:click.self="$dispatch('close-refresh-modal')">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
                <div class="flex items-start gap-4">
                    <div class="shrink-0 w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                        <i class="bx bx-calendar-check text-blue-600 text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Confirm Refresh Dates</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            This will update week date ranges and exam labels from the current academic calendar. Your topics, learning outcomes, assessments, and references will stay untouched.
                        </p>
                        <p class="text-sm font-medium text-blue-600 mb-4">Do you want to proceed?</p>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button"
                            x-on:click="$dispatch('close-refresh-modal')"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Cancel
                    </button>
                    <button type="button"
                            x-on:click="$dispatch('syllabus-save-started'); $wire.refreshWeekDates(); $dispatch('close-refresh-modal')"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        Yes, Refresh Dates
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
