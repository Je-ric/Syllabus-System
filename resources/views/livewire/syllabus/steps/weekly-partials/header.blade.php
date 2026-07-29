{{-- weekly-partials/header.blade.php --}}

<div class="mb-5">

    <x-wizard.step-header
        title="Weekly Coverage"
        description="Weeks are auto-generated from the academic calendar. Fill in coverage details per week.">

        @if ($weeksGenerated)
            <div class="flex items-center gap-2">

                {{-- Soft path: updates week dates and exam labels from the current calendar.
                     Faculty content (LOs, topics, TLAs, assessments, references) is kept intact. --}}
                <x-ui.button variant="sm-info"
                    wire:click="refreshWeekDates"
                    wireTarget="refreshWeekDates"
                    loading="Refreshing…">
                    <i class="bx bx-calendar-check"></i> Refresh Dates
                </x-ui.button>

                {{-- Visual divider --}}
                <span class="w-px h-5 bg-[#E4E7EC] shrink-0"></span>

                {{-- Destructive path: wipes all weeks and content, rebuilds from scratch.
                     Wire confirm is the only safety net here — the label must make the
                     consequence unmistakably clear. --}}
                <x-ui.button variant="sm-danger"
                    wire:click="hardResetWeeks"
                    wireTarget="hardResetWeeks"
                    wire:confirm="Hard Reset will permanently delete ALL weeks and every piece of content you have entered (topics, learning outcomes, assessments, references, evaluation weights). This cannot be undone.\n\nAre you sure you want to continue?"
                    loading="Resetting…">
                    <i class="bx bx-trash"></i> Hard Reset
                </x-ui.button>

                {{-- Visual divider --}}
                <span class="w-px h-5 bg-[#E4E7EC] shrink-0"></span>

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

    @if ($weeksGenerated)
        <x-feedback-status.alert type="info" :showTitle="false" class="mt-3">
            <strong>Refresh Dates</strong> updates week date ranges and exam labels from the current
            calendar — your topics, learning outcomes, assessments, and references stay untouched.
            Use <strong>Hard Reset</strong> only if you want to start over completely and rebuild
            all weeks from scratch.
        </x-feedback-status.alert>
    @endif

</div>
