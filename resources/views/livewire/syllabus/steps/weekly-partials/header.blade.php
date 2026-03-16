{{--
    Partial: weekly-partials/header.blade.php
    ─────────────────────────────────────────
    Renders: step title/description, generate/regenerate/save-all action buttons,
    schedule chips, academic calendar date range, and coverage stats.

    Inherits from parent component view:
        $weeksGenerated       bool
        $academic_calendar_id int|null
        $courseComponents     array       Keyed 'LEC' | 'LAB'
        $syllabusWeeks        Collection
        $weekEvents           array
        $lockedWeeks          array
        $syllabus             Syllabus|null  (with academicCalendar loaded)
--}}

<div class="mb-5">
    <div class="space-y-3">

        {{-- ── Step title + action buttons ────────────────────────────────── --}}
        <x-wizard.step-header title="Weekly Coverage" icon="calendar-week"
            description="Weeks are auto-generated from the academic calendar.
                         Fill in coverage details per week.
                         Exam and Non-Teaching weeks are locked automatically.">

            <div class="flex items-center gap-2 flex-wrap">
                @if (!$weeksGenerated)
                    <x-button variant="sm-success" wire:click="generateWeeklyCoverage" :disabled="!$academic_calendar_id"
                        wireTarget="generateWeeklyCoverage" loading="Generating…">
                        <i class="bx bx-calendar-plus"></i> Generate Weeks
                    </x-button>

                    @if (!$academic_calendar_id)
                        <x-feedback-status.alert type="error" :showTitle="false" class="text-xs">
                            No academic calendar selected. Please go back to the previous step
                            and select one to generate weeks.
                        </x-feedback-status.alert>
                    @endif
                @else
                    <x-button variant="sm-warning" wire:click="regenerateWeeks" wireTarget="regenerateWeeks"
                        wire:confirm="This will delete all existing weeks and recreate them. All content will be lost. Continue?"
                        loading="Regenerating…">
                        <i class="bx bx-refresh"></i> Regenerate Weeks
                    </x-button>

                    <x-button variant="sm-success" wire:click="saveAllWeeklyEntries" wireTarget="saveAllWeeklyEntries"
                        loading="Saving…">
                        <i class="bx bx-save"></i> Save All
                    </x-button>
                @endif
            </div>

        </x-wizard.step-header>

        {{-- ── Info cards grid ─────────────────────────────────────────────── --}}
        <div class="grid gap-4 text-sm lg:grid-cols-2">

            {{-- Schedule chips (LEC = emerald, LAB = blue) --}}
            @if ($courseComponents ?? null)
                @php
                    $hasLEC = isset($courseComponents['LEC']);
                    $hasLAB = isset($courseComponents['LAB']);
                @endphp
                @if ($hasLEC || $hasLAB)
                    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-4">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-3">
                            Class Schedule
                        </p>
                        <div class="flex gap-2 flex-wrap">
                            @if ($hasLEC)
                                <div
                                    class="flex-1 min-w-28 rounded-xl bg-emerald-50 border border-emerald-300 px-3 py-2">
                                    <div class="flex items-center gap-1.5 mb-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                        <span
                                            class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">LEC</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="text-xs font-semibold text-slate-800 leading-snug">
                                            {{ $courseComponents['LEC']['schedule'] ?? '—' }}
                                        </div>
                                        <div class="text-[11px] text-emerald-600 mt-0.5">
                                            {{ $courseComponents['LEC']['class_hours'] ?? '—' }} hrs/wk
                                        </div>
                                    </div class="flex items-center justify-between">
                                </div>
                            @endif
                            @if ($hasLAB)
                                <div class="flex-1 min-w-28 rounded-xl bg-blue-50 border border-blue-300 px-3 py-2">
                                    <div class="flex items-center gap-1.5 mb-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 shrink-0"></span>
                                        <span
                                            class="text-[10px] font-bold uppercase tracking-wider text-blue-700">LAB</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="text-xs font-semibold text-slate-800 leading-snug">
                                            {{ $courseComponents['LAB']['schedule'] ?? '—' }}
                                        </div>
                                        <div class="text-[11px] text-blue-600 mt-0.5">
                                            {{ $courseComponents['LAB']['class_hours'] ?? '—' }} hrs/wk
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endif

            {{-- Academic Calendar — date + coverage stats --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-4">
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-3">
                    Academic Calendar
                </p>
                @if ($syllabus?->academicCalendar)
                    {{-- Date row --}}
                    <div class="flex gap-3 justify-between items-center">
                        <div class="flex items-center gap-3 rounded-xl bg-slate-50 border border-slate-300 px-3 py-2.5 mb-3">
                            <span class="shrink-0 flex items-center justify-center w-9 h-9 rounded-xl
                                        bg-white border border-slate-200 text-slate-500 shadow-sm">
                                <i class="bx bx-calendar text-base"></i>
                            </span>
                            <div>
                                <div class="text-base font-bold text-slate-800 leading-tight">
                                    {{ \Carbon\Carbon::parse($syllabus->academicCalendar->start_date)->format('M d') }}
                                    <span class="text-slate-400 font-normal mx-0.5">–</span>
                                    {{ \Carbon\Carbon::parse($syllabus->academicCalendar->end_date)->format('M d, Y') }}
                                </div>
                                @if ($syllabus->academicCalendar->academic_year ?? null)
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        {{ $syllabus->academicCalendar->academic_year }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Coverage stats (only once weeks are generated) --}}
                        @if ($weeksGenerated)
                            @php $lockedCount = count($lockedWeeks); @endphp
                            <div class="flex flex-wrap gap-2">
                                <x-feedback-status.status-indicator variant="slate" :dot="true">
                                    {{ $syllabusWeeks->count() }} Weeks
                                </x-feedback-status.status-indicator>
                                <x-feedback-status.status-indicator variant="amber" :dot="true">
                                    {{ collect($weekEvents)->flatten(1)->count() }} Events
                                </x-feedback-status.status-indicator>
                                @if ($lockedCount > 0)
                                    <x-feedback-status.status-indicator variant="rose" :dot="true">
                                        {{ $lockedCount }} Locked
                                    </x-feedback-status.status-indicator>
                                @endif
                            </div>
                        @endif
                    </div class="flex gap-3">
                @else
                    <div class="flex items-center gap-2 text-xs text-slate-400 italic">
                        <i class="bx bx-calendar-x text-slate-300 text-base"></i> Not set
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
