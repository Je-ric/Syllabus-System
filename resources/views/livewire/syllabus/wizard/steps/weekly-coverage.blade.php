<div>

    {{-- Drawer state — owned here, not on the wizard root, because this is a
         child Livewire component with its own Alpine scope. The wizard sidebar
         dispatches open-schedule-drawer / open-calendar-info-drawer on window;
         we listen here and flip the flags the offcanvas components read. --}}
    <div x-data="{ scheduleOpen: false, calInfoOpen: false }"
         x-on:open-schedule-drawer.window="scheduleOpen = true"
         x-on:open-calendar-info-drawer.window="calInfoOpen = true"
         x-on:sidebar-save-all-weeks.window="$dispatch('syllabus-save-started')"
         x-on:syllabus-step-saved.window="$dispatch('syllabus-save-finished')"
         x-on:syllabus-step-save-failed.window="$dispatch('syllabus-save-finished')">

        {{-- Drawers --}}
        @include('livewire.syllabus.wizard.steps.partials.weekly.schedule-drawer')
        @include('livewire.syllabus.wizard.steps.partials.weekly.calendar-info-drawer')

    </div>

    {{-- ══ Header: title, buttons, info cards, stats ═══════════════════════════ --}}
    @include('livewire.syllabus.wizard.steps.partials.weekly.header')

    {{-- ══ Empty State ══════════════════════════════════════════════════════════ --}}
    @if ($syllabusWeeks->isEmpty())

        <x-feedback-status.empty-state
            icon="calendar-x"
            title="No weeks generated yet"
            message="Select an academic calendar in the previous step, then click Generate Weeks.">
            <x-ui.button variant="sm-add"
                x-on:click="$dispatch('syllabus-save-started'); $wire.generateWeeklyCoverage()"
                :disabled="! $academic_calendar_id"
                wireTarget="generateWeeklyCoverage"
                loading="Generating…">
                <i class="bx bx-calendar-plus"></i> Generate Weeks
            </x-ui.button>
        </x-feedback-status.empty-state>

    @else

        @php $hasLEC = isset($courseComponents['LEC']); $hasLAB = isset($courseComponents['LAB']); @endphp

        {{-- ══ LEC / LAB tab switcher ══════════════════════════════════════════ --}}
        @include('livewire.syllabus.wizard.steps.partials.weekly.tab-switcher')

        {{-- ══ Week accordion ══════════════════════════════════════════════════ --}}
        @include('livewire.syllabus.wizard.steps.partials.weekly.week-accordion')

    @endif

</div>
