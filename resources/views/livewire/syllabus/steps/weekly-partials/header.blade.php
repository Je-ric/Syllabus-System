{{-- weekly-partials/header.blade.php --}}

<div class="mb-5">

    <x-wizard.step-header
        title="Weekly Coverage"
        description="Weeks are auto-generated from the academic calendar. Fill in coverage details per week.">

        @if ($weeksGenerated)
            <div class="flex items-center gap-2">
                <x-button variant="sm-warning"
                    wire:click="regenerateWeeks"
                    wireTarget="regenerateWeeks"
                    wire:confirm="This will delete all existing weeks and recreate them. All content will be lost. Continue?"
                    loading="Regenerating…">
                    <i class="bx bx-refresh"></i> Regenerate
                </x-button>
                <x-button variant="sm-add"
                    wire:click="saveAllWeeklyEntries"
                    wireTarget="saveAllWeeklyEntries"
                    loading="Saving…">
                    <i class="bx bx-save"></i> Save All
                </x-button>
            </div>
        @else
            <x-button variant="sm-add"
                wire:click="generateWeeklyCoverage"
                :disabled="!$academic_calendar_id"
                wireTarget="generateWeeklyCoverage"
                loading="Generating…">
                <i class="bx bx-calendar-plus"></i> Generate Weeks
            </x-button>
        @endif

    </x-wizard.step-header>

    @if (!$weeksGenerated && !$academic_calendar_id)
        <x-feedback-status.alert type="error" :showTitle="false">
            No academic calendar selected. Go back to Step 1 and select one before generating weeks.
        </x-feedback-status.alert>
    @endif

</div>

