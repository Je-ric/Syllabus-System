{{-- weekly-partials/header.blade.php --}}

<div class="mb-6 space-y-4">

    <x-wizard.step-header
        title="Weekly Coverage"
        eyebrow="Assessments"
        description="Weeks are auto-generated from the academic calendar. Fill in coverage details per week. Exam and non-teaching weeks are locked automatically.">

        <div class="flex items-center gap-2">
            @if (!$weeksGenerated)
                <x-button variant="sm-add"
                    wire:click="generateWeeklyCoverage"
                    :disabled="!$academic_calendar_id"
                    wireTarget="generateWeeklyCoverage"
                    loading="Generating…">
                    <i class="bx bx-calendar-plus"></i> Generate weeks
                </x-button>
            @else
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
            @endif
        </div>

    </x-wizard.step-header>

    {{-- No calendar error --}}
    @if (!$weeksGenerated && !$academic_calendar_id)
        <x-feedback-status.alert type="error" :showTitle="false">
            No academic calendar selected. Go back to the previous step and select one before generating weeks.
        </x-feedback-status.alert>
    @endif

    {{-- Info cards --}}
    @if ($weeksGenerated || ($courseComponents ?? null))
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">

            {{-- Schedule card --}}
            @if (($courseComponents ?? null) && (isset($courseComponents['LEC']) || isset($courseComponents['LAB'])))
                <div class="rounded-xl border border-slate-200 bg-white overflow-hidden">

                    <div class="px-4 py-2.5 border-b border-slate-100 bg-slate-50">
                        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400">
                            Class Schedule
                        </p>
                    </div>

                    <div class="divide-y divide-slate-100">

                        @if (isset($courseComponents['LEC']))
                            <div class="flex items-center justify-between px-4 py-3">
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-emerald-600 mb-0.5">
                                        Lecture · LEC
                                    </p>
                                    <p class="text-[13px] font-medium text-slate-700">
                                        {{ $courseComponents['LEC']['schedule'] ?? '—' }}
                                    </p>
                                </div>
                                <span class="text-[12px] text-slate-400 font-mono shrink-0">
                                    {{ $courseComponents['LEC']['class_hours'] ?? '—' }} hrs/wk
                                </span>
                            </div>
                        @endif

                        @if (isset($courseComponents['LAB']))
                            <div class="flex items-center justify-between px-4 py-3">
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-blue-600 mb-0.5">
                                        Laboratory · LAB
                                    </p>
                                    <p class="text-[13px] font-medium text-slate-700">
                                        {{ $courseComponents['LAB']['schedule'] ?? '—' }}
                                    </p>
                                </div>
                                <span class="text-[12px] text-slate-400 font-mono shrink-0">
                                    {{ $courseComponents['LAB']['class_hours'] ?? '—' }} hrs/wk
                                </span>
                            </div>
                        @endif

                    </div>
                </div>
            @endif

            {{-- Academic calendar card --}}
            <div class="rounded-xl border border-slate-200 bg-white px-4 py-3.5">

                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400 mb-2.5">
                    Academic Calendar
                </p>

                @if ($syllabus?->academicCalendar)
                    <div class="flex items-start justify-between gap-3 flex-wrap">

                        <div>
                            <p class="text-[14px] font-semibold text-slate-800 leading-tight">
                                {{ \Carbon\Carbon::parse($syllabus->academicCalendar->start_date)->format('M d') }}
                                <span class="text-slate-300 mx-0.5">–</span>
                                {{ \Carbon\Carbon::parse($syllabus->academicCalendar->end_date)->format('M d, Y') }}
                            </p>
                            @if ($syllabus->academicCalendar->academic_year ?? null)
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    {{ $syllabus->academicCalendar->academic_year }}
                                </p>
                            @endif
                        </div>

                        @if ($weeksGenerated)
                            @php $lockedCount = count($lockedWeeks); @endphp
                            <div class="flex flex-wrap gap-1.5 mt-0.5">

                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold
                                             bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                    <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                                    {{ $syllabusWeeks->count() }} weeks
                                </span>

                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold
                                             bg-slate-100 text-slate-500 ring-1 ring-inset ring-slate-200">
                                    {{ collect($weekEvents)->flatten(1)->count() }} events
                                </span>

                                @if ($lockedCount > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold
                                                 bg-amber-50 text-amber-600 ring-1 ring-inset ring-amber-200">
                                        <i class="bx bx-lock-alt text-[10px]"></i>
                                        {{ $lockedCount }} locked
                                    </span>
                                @endif

                            </div>
                        @endif

                    </div>
                @else
                    <p class="text-[13px] text-slate-400 italic flex items-center gap-1.5">
                        <i class="bx bx-calendar-x text-[15px]"></i>
                        Not set
                    </p>
                @endif

            </div>

        </div>
    @endif

</div>