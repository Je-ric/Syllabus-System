{{-- weekly-partials/header.blade.php --}}

<div class="mb-6 space-y-4" x-data="{ scheduleOpen: false, calInfoOpen: false }">

    {{-- Drawers --}}
    @include('livewire.syllabus.steps.weekly-partials.schedule-drawer')
    @include('livewire.syllabus.steps.weekly-partials.calendar-info-drawer')

    {{-- Step header --}}
    <x-wizard.step-header
        title="Weekly Coverage"
        description="Weeks are auto-generated from the academic calendar. Fill in coverage details per week. Exam and non-teaching weeks are locked automatically.">

        <div class="flex items-center gap-2">
            @if ($weeksGenerated)
                <x-button variant="sm-cancel" type="button" x-on:click="scheduleOpen = true">
                    <i class="bx bx-time text-sm"></i> Schedule
                </x-button>
                <x-button variant="sm-cancel" type="button" x-on:click="calInfoOpen = true">
                    <i class="bx bx-calendar text-sm"></i> Calendar
                </x-button>
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
                    <i class="bx bx-save"></i> Save all
                </x-button>
            @else
                <x-button variant="sm-add"
                    wire:click="generateWeeklyCoverage"
                    :disabled="!$academic_calendar_id"
                    wireTarget="generateWeeklyCoverage"
                    loading="Generating…">
                    <i class="bx bx-calendar-plus"></i> Generate weeks
                </x-button>
            @endif
        </div>

    </x-wizard.step-header>

    {{-- No calendar error --}}
    @if (!$weeksGenerated && !$academic_calendar_id)
        <x-feedback-status.alert type="error" :showTitle="false">
            No academic calendar selected. Go back to the previous step and select one before generating weeks.
        </x-feedback-status.alert>
    @endif

</div>
