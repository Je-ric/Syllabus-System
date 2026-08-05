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
                    wire:click="hardResetWeeks"
                    wireTarget="hardResetWeeks"
                    wire:confirm="Hard Reset will permanently delete ALL weeks and every piece of content you have entered (topics, learning outcomes, assessments, references, evaluation weights). This cannot be undone.\n\nAre you sure you want to continue?"
                    loading="Resetting…">
                    <i class="bx bx-trash"></i> Hard Reset
                </x-ui.button>

                <span class="w-px h-5 bg-[#E4E7EC] shrink-0"></span>

                {{-- Soft path: updates week dates and exam labels, keeps faculty content intact --}}
                <div class="relative inline-flex items-center">
                    <x-ui.button variant="sm-info"
                        wire:click="refreshWeekDates"
                        wireTarget="refreshWeekDates"
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
                    wire:click="saveAllWeeklyEntries"
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
