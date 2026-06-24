<div>

    {{-- ══ Header ══════════════════════════════════════════════════════════════ --}}
    <x-wizard.step-header
        title="Course Evaluation"
        icon="notepad"
        description="Set the weight (%) for each assessment task.
                     Rows are pulled from Weekly Coverage.
                     Select Activity or Quiz for each row.
                     Exam course outcomes are auto-determined from the last covered week.
                     The 60% passing standard applies to every assessment per semester." />

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

        {{-- ══ Sticky save bar ════════════════════════════════════════════════ --}}
        <div class="sticky bottom-0 z-10 mt-4 flex items-center justify-between gap-4 px-5 py-3
                    rounded-xl border border-[#dedee2] bg-white/95 backdrop-blur-sm"
             style="box-shadow: 0 -2px 12px rgba(0,0,0,.08);">

            {{-- Totals --}}
            <div class="flex items-center gap-4 text-[13px]">
                @php $lecOk = $lecTotal === $lecStdNum && $lecTotal > 0; @endphp
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full {{ $lecOk ? 'bg-emerald-500' : ($lecTotal > 0 ? 'bg-rose-400' : 'bg-slate-300') }}"></span>
                    <span class="font-semibold {{ $lecOk ? 'text-emerald-700' : ($lecTotal > 0 ? 'text-rose-600' : 'text-slate-400') }}">
                        LEC {{ $lecTotal }}&thinsp;/&thinsp;{{ $lecStdNum }}%
                    </span>
                </span>
                @if ($courseHasLab)
                    @php $labOk = $labTotal === $labStdNum && $labTotal > 0; @endphp
                    <span class="text-slate-200">|</span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full {{ $labOk ? 'bg-blue-500' : ($labTotal > 0 ? 'bg-rose-400' : 'bg-slate-300') }}"></span>
                        <span class="font-semibold {{ $labOk ? 'text-blue-700' : ($labTotal > 0 ? 'text-rose-600' : 'text-slate-400') }}">
                            LAB {{ $labTotal }}&thinsp;/&thinsp;{{ $labStdNum }}%
                        </span>
                    </span>
                @endif
            </div>

            {{-- Save button --}}
            <x-button variant="sm-add" wire:click="save" wireTarget="save" loading="Saving…">
                <i class="bx bx-save"></i> Save Evaluation
            </x-button>

        </div>

    @endif

</div>
