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
        <x-wizard.step-header
            title="Weekly Coverage"
            icon="calendar-week"
            description="Weeks are auto-generated from the academic calendar.
                         Fill in coverage details per week.
                         Exam and Non-Teaching weeks are locked automatically.">

            <div class="flex items-center gap-2 flex-wrap">
                @if (! $weeksGenerated)
                    <x-button variant="sm-success"
                        wire:click="generateWeeklyCoverage"
                        :disabled="! $academic_calendar_id"
                        wire:target="generateWeeklyCoverage"
                        loading="Generating…">
                        <i class="bx bx-calendar-plus"></i> Generate Weeks
                    </x-button>

                    @if (! $academic_calendar_id)
                        <x-feedback-status.alert type="error" :showTitle="false" class="text-xs">
                            No academic calendar selected. Please go back to the previous step
                            and select one to generate weeks.
                        </x-feedback-status.alert>
                    @endif
                @else
                    <x-button variant="sm-warning"
                        wire:click="regenerateWeeks"
                        wire:target="regenerateWeeks"
                        wire:confirm="This will delete all existing weeks and recreate them. All content will be lost. Continue?"
                        loading="Regenerating…">
                        <i class="bx bx-refresh"></i> Regenerate Weeks
                    </x-button>

                    <x-button variant="sm-success"
                        wire:click="saveAllWeeklyEntries"
                        wire:target="saveAllWeeklyEntries"
                        loading="Saving…">
                        <i class="bx bx-save"></i> Save All
                    </x-button>
                @endif
            </div>

        </x-wizard.step-header>

        {{-- ── Info cards grid ─────────────────────────────────────────────── --}}
        <div class="grid gap-4 text-sm md:grid-cols-2 xl:grid-cols-3">

            {{-- Schedule chips (LEC = emerald, LAB = blue) --}}
            @if ($courseComponents ?? null)
                @php $hasLEC = isset($courseComponents['LEC']); $hasLAB = isset($courseComponents['LAB']); @endphp
                @if ($hasLEC || $hasLAB)
                    <div class="px-4 pt-4 pb-3 border-b border-slate-100">
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-2.5">
                            Class Schedule
                        </p>
                        <div class="flex gap-2 flex-wrap">
                            @if ($hasLEC)
                                <div class="flex-1 min-w-[110px] rounded-lg bg-emerald-50 border border-emerald-100 px-3 py-2">
                                    <div class="flex items-center gap-1.5 mb-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">LEC</span>
                                    </div>
                                    <div class="text-xs font-semibold text-slate-800 leading-snug">
                                        {{ $courseComponents['LEC']['schedule'] ?? '—' }}
                                    </div>
                                    <div class="text-[11px] text-emerald-600 mt-0.5">
                                        {{ $courseComponents['LEC']['class_hours'] ?? '—' }} hrs/wk
                                    </div>
                                </div>
                            @endif
                            @if ($hasLAB)
                                <div class="flex-1 min-w-[110px] rounded-lg bg-blue-50 border border-blue-100 px-3 py-2">
                                    <div class="flex items-center gap-1.5 mb-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 shrink-0"></span>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-blue-700">LAB</span>
                                    </div>
                                    <div class="text-xs font-semibold text-slate-800 leading-snug">
                                        {{ $courseComponents['LAB']['schedule'] ?? '—' }}
                                    </div>
                                    <div class="text-[11px] text-blue-600 mt-0.5">
                                        {{ $courseComponents['LAB']['class_hours'] ?? '—' }} hrs/wk
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endif

            {{-- Calendar date range --}}
            <div class="px-4 py-3 {{ $weeksGenerated ? 'border-b border-slate-100' : '' }}">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-2">
                    Academic Calendar
                </p>
                @if ($syllabus?->academicCalendar)
                    <div class="flex items-center gap-2">
                        <span class="shrink-0 flex items-center justify-center w-7 h-7 rounded-lg
                                     bg-slate-100 text-slate-500">
                            <i class="bx bx-calendar text-sm"></i>
                        </span>
                        <div>
                            <div class="text-xs font-semibold text-slate-800">
                                {{ \Carbon\Carbon::parse($syllabus->academicCalendar->start_date)->format('M d') }}
                                <span class="text-slate-400 font-normal">–</span>
                                {{ \Carbon\Carbon::parse($syllabus->academicCalendar->end_date)->format('M d, Y') }}
                            </div>
                            @if ($syllabus->academicCalendar->academic_year ?? null)
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    {{ $syllabus->academicCalendar->academic_year }}
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="text-xs text-slate-400 italic flex items-center gap-1.5">
                        <i class="bx bx-calendar-x text-slate-300"></i> Not set
                    </div>
                @endif
            </div>

            {{-- Coverage overview stats (only when weeks exist) --}}
            @if ($weeksGenerated)
                @php $lockedCount = count($lockedWeeks); @endphp
                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-2">
                            Coverage Overview
                        </p>
                        @if ($lockedCount > 0)
                            <span class="text-xs font-medium text-rose-500">
                                {{ $lockedCount }} Locked
                            </span>
                        @endif
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <div class="text-2xl font-bold text-slate-800 leading-none">
                                {{ $syllabusWeeks->count() }}
                            </div>
                            <div class="text-xs text-slate-400 mt-1">Total Weeks</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-amber-600 leading-none">
                                {{ collect($weekEvents)->flatten(1)->count() }}
                            </div>
                            <div class="text-xs text-slate-400 mt-1">Calendar Events</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold {{ $lockedCount > 0 ? 'text-rose-500' : 'text-slate-300' }} leading-none">
                                {{ $lockedCount }}
                            </div>
                            <div class="text-xs text-slate-400 mt-1">Locked Weeks</div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>