<div>

    {{-- ══ Header: title, buttons, info cards, stats ═══════════════════════════ --}}
    @include('livewire.syllabus.steps.weekly-partials.header')

    {{-- ══ Empty State ══════════════════════════════════════════════════════════ --}}
    @if ($syllabusWeeks->isEmpty())

        <x-empty-state
            icon="calendar-x"
            title="No weeks generated yet"
            message="Select an academic calendar in the previous step, then click Generate Weeks.">
            <x-button variant="add-button"
                wire:click="generateWeeklyCoverage"
                :disabled="! $academic_calendar_id"
                wireTarget="generateWeeklyCoverage"
                loading="Generating…">
                <i class="bx bx-calendar-plus"></i> Generate Weeks
            </x-button>
        </x-empty-state>

    @else

        @php $hasLEC = isset($courseComponents['LEC']); $hasLAB = isset($courseComponents['LAB']); @endphp

        {{-- ══ LEC / LAB tab switcher ══════════════════════════════════════════ --}}
        @include('livewire.syllabus.steps.weekly-partials.tab-switcher')

        {{-- ══ Week accordion ══════════════════════════════════════════════════ --}}
        @include('livewire.syllabus.steps.weekly-partials.week-accordion')

        <x-feedback-status.alert type="info" :showTitle="false" class="mt-2">
            Weeks with scheduled exams or non-teaching classes are locked automatically based on the
            academic calendar. You can identify them by the badges and red highlight.
            Click on locked weeks to see details.
        </x-feedback-status.alert>

    @endif

</div>
