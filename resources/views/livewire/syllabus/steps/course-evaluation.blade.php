<div>

    {{-- ══ Header ══════════════════════════════════════════════════════════════ --}}
    <x-wizard.step-header
        title="Course Evaluation"
        icon="notepad"
        description="Set the weight (%) for each assessment task.
                     Rows are pulled from Weekly Coverage.
                     Select Activity or Quiz for each row.
                     Exam course outcomes are auto-determined from the last covered week.
                     The 60% passing standard applies to every assessment per semester.">

    @if (count($rows) > 0)
        <x-button variant="sm-add"
            wire:click="save"
            wireTarget="save"
            loading="Saving…">
            <i class="bx bx-save"></i> Save Evaluation
        </x-button>
    @endif

    </x-wizard.step-header>

    {{-- ══ Empty state ════════════════════════════════════════════════════════ --}}
    @if (count($rows) === 0)
        <x-empty-state
            icon="bx-calendar-event"
            title="Why no assessment tasks?"
            message="Fill in assessment tasks in the Weekly Coverage step first.
                     Week 1 (MVGO) will appear here only if it has an assessment task.
                     Exam weeks are auto-detected from calendar events." />
    @else

        {{-- ══ Evaluation table ════════════════════════════════════════════════ --}}
        @include('livewire.syllabus.steps.evaluation-partials.table')

        {{-- ══ Notes ══════════════════════════════════════════════════════════ --}}
        @include('livewire.syllabus.steps.evaluation-partials.notes')

    @endif

</div>