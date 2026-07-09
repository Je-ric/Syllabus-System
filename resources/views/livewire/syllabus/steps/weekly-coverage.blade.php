<div>

    {{-- Drawer state — owned here, not on the wizard root, because this is a
         child Livewire component with its own Alpine scope. The wizard sidebar
         dispatches open-schedule-drawer / open-calendar-info-drawer on window;
         we listen here and flip the flags the offcanvas components read. --}}
    <div x-data="{ scheduleOpen: false, calInfoOpen: false }"
         x-on:open-schedule-drawer.window="scheduleOpen = true"
         x-on:open-calendar-info-drawer.window="calInfoOpen = true">

        {{-- Drawers --}}
        @include('livewire.syllabus.steps.weekly-partials.schedule-drawer')
        @include('livewire.syllabus.steps.weekly-partials.calendar-info-drawer')

    </div>

    {{-- ══ Header: title, buttons, info cards, stats ═══════════════════════════ --}}
    @include('livewire.syllabus.steps.weekly-partials.header')

    {{-- ══ Empty State ══════════════════════════════════════════════════════════ --}}
    @if ($syllabusWeeks->isEmpty())

        <x-feedback-status.empty-state
            icon="calendar-x"
            title="No weeks generated yet"
            message="Select an academic calendar in the previous step, then click Generate Weeks.">
            <x-button variant="sm-add"
                wire:click="generateWeeklyCoverage"
                :disabled="! $academic_calendar_id"
                wireTarget="generateWeeklyCoverage"
                loading="Generating…">
                <i class="bx bx-calendar-plus"></i> Generate Weeks
            </x-button>
        </x-feedback-status.empty-state>

    @else

        @php $hasLEC = isset($courseComponents['LEC']); $hasLAB = isset($courseComponents['LAB']); @endphp

        {{-- ══ LEC / LAB tab switcher ══════════════════════════════════════════ --}}
        @include('livewire.syllabus.steps.weekly-partials.tab-switcher')

        {{-- Completion dot legend — visible only when sidebar legend is hidden --}}
        <div class="lg:hidden flex items-center gap-4 mb-3 px-1 flex-wrap">
            <span class="flex items-center gap-1.5 text-xs text-slate-500">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span> Complete
            </span>
            <span class="flex items-center gap-1.5 text-xs text-slate-500">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-400 shrink-0"></span> Incomplete
            </span>
            <span class="flex items-center gap-1.5 text-xs text-slate-500">
                <span class="w-2.5 h-2.5 rounded-full bg-slate-300 shrink-0"></span> Empty
            </span>
        </div>

        {{-- ══ Week accordion ══════════════════════════════════════════════════ --}}
        @include('livewire.syllabus.steps.weekly-partials.week-accordion')

        <x-feedback-status.alert type="info" :showTitle="false" class="mt-2">
            Weeks with scheduled exams or non-teaching classes are locked automatically based on the
            academic calendar. You can identify them by the badges and red highlight.
            Click on locked weeks to see details.
        </x-feedback-status.alert>

    @endif

</div>
