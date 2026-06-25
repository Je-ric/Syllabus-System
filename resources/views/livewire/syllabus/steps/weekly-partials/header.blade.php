{{-- weekly-partials/header.blade.php --}}

<div class="mb-5" x-data="{ scheduleOpen: false, calInfoOpen: false }">

    {{-- Drawers --}}
    @include('livewire.syllabus.steps.weekly-partials.schedule-drawer')
    @include('livewire.syllabus.steps.weekly-partials.calendar-info-drawer')

    {{-- Step header with actions --}}
    <x-wizard.step-header
        title="Weekly Coverage"
        description="Weeks are auto-generated from the academic calendar. Fill in coverage details per week.">

        <div class="flex items-center gap-2 flex-wrap">
            @if ($weeksGenerated)
                {{-- Context buttons --}}
                <button type="button" x-on:click="scheduleOpen = true"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold
                           border border-[#e2e8f0] bg-white text-slate-600
                           hover:bg-[#f8fafc] hover:border-[#c6c6cc] transition-colors">
                    <i class="bx bx-time text-sm"></i> Schedule
                </button>
                <button type="button" x-on:click="calInfoOpen = true"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold
                           border border-[#e2e8f0] bg-white text-slate-600
                           hover:bg-[#f8fafc] hover:border-[#c6c6cc] transition-colors">
                    <i class="bx bx-calendar text-sm"></i> Calendar
                </button>

                <span class="w-px h-5 bg-[#e2e8f0] shrink-0"></span>

                {{-- Action buttons --}}
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
            @else
                <x-button variant="sm-add"
                    wire:click="generateWeeklyCoverage"
                    :disabled="!$academic_calendar_id"
                    wireTarget="generateWeeklyCoverage"
                    loading="Generating…">
                    <i class="bx bx-calendar-plus"></i> Generate Weeks
                </x-button>
            @endif
        </div>

    </x-wizard.step-header>

    {{-- No calendar error --}}
    @if (!$weeksGenerated && !$academic_calendar_id)
        <x-feedback-status.alert type="error" :showTitle="false">
            No academic calendar selected. Go back to Step 1 and select one before generating weeks.
        </x-feedback-status.alert>
    @endif

</div>
