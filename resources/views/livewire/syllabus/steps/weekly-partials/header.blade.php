{{-- weekly-partials/header.blade.php --}}

<div class="mb-5 space-y-4">

    <x-wizard.step-header title="Weekly Coverage" eyebrow="Assessments"
        description="Weeks are auto-generated from the academic calendar. Fill in coverage details per week. Exam and Non-Teaching weeks are locked automatically.">
        <div class="flex items-center gap-2">
            @if (!$weeksGenerated)
                <x-button variant="sm-add" wire:click="generateWeeklyCoverage" :disabled="!$academic_calendar_id"
                    wireTarget="generateWeeklyCoverage" loading="Generating…">
                    <i class="bx bx-calendar-plus"></i> Generate Weeks
                </x-button>
            @else
                <x-button variant="sm-warning" wire:click="regenerateWeeks" wireTarget="regenerateWeeks"
                    wire:confirm="This will delete all existing weeks and recreate them. All content will be lost. Continue?"
                    loading="Regenerating…">
                    <i class="bx bx-refresh"></i> Regenerate
                </x-button>
                <x-button variant="sm-add" wire:click="saveAllWeeklyEntries" wireTarget="saveAllWeeklyEntries"
                    loading="Saving…">
                    <i class="bx bx-save"></i> Save All
                </x-button>
            @endif
        </div>
    </x-wizard.step-header>

    @if (!$weeksGenerated && !$academic_calendar_id)
        <x-feedback-status.alert type="error" :showTitle="false">
            No academic calendar selected. Go back to the previous step and select one to generate weeks.
        </x-feedback-status.alert>
    @endif

    @if ($weeksGenerated || ($courseComponents ?? null))
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-5">

            {{-- Schedule chips --}}
            @if ($courseComponents ?? null)
                @php
                    $hasLEC = isset($courseComponents['LEC']);
                    $hasLAB = isset($courseComponents['LAB']);
                @endphp
                @if ($hasLEC || $hasLAB)
                    <div class="rounded-xl border border-slate-200 bg-white overflow-hidden">

                        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                            <h3 class="text-[14px] font-semibold text-slate-800">
                                Class Schedule
                            </h3>
                        </div>

                        <div class="divide-y divide-slate-100">

                            @if ($hasLEC)
                                <div class="flex items-center justify-between px-4 py-3">
                                    <div>
                                        <p class="text-xs font-semibold text-emerald-700">
                                            Lecture (LEC)
                                        </p>
                                        <p class="text-sm text-slate-800">
                                            {{ $courseComponents['LEC']['schedule'] ?? '—' }}
                                        </p>
                                    </div>

                                    <span class="text-xs text-slate-500">
                                        {{ $courseComponents['LEC']['class_hours'] ?? '—' }} hrs/wk
                                    </span>
                                </div>
                            @endif

                            @if ($hasLAB)
                                <div class="flex items-center justify-between px-4 py-3">
                                    <div>
                                        <p class="text-xs font-semibold text-blue-700">
                                            Laboratory (LAB)
                                        </p>
                                        <p class="text-sm text-slate-800">
                                            {{ $courseComponents['LAB']['schedule'] ?? '—' }}
                                        </p>
                                    </div>

                                    <span class="text-xs text-slate-500">
                                        {{ $courseComponents['LAB']['class_hours'] ?? '—' }} hrs/wk
                                    </span>
                                </div>
                            @endif

                        </div>

                    </div>
                @endif
            @endif

            {{-- Academic Calendar --}}
            <div class="rounded-xl border border-[#e2e8f0] bg-white p-4"
                style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">
                <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-3">Academic Calendar</p>
                @if ($syllabus?->academicCalendar)
                    <div class="flex items-center justify-between gap-3 flex-wrap">
                        <div>
                            <p class="text-[13px] font-semibold text-[#0f172a] leading-tight">
                                {{ \Carbon\Carbon::parse($syllabus->academicCalendar->start_date)->format('M d') }}
                                <span class="text-[#94a3b8] font-normal mx-0.5">–</span>
                                {{ \Carbon\Carbon::parse($syllabus->academicCalendar->end_date)->format('M d, Y') }}
                            </p>
                            @if ($syllabus->academicCalendar->academic_year ?? null)
                                <p class="text-[11px] text-[#94a3b8] mt-0.5">
                                    {{ $syllabus->academicCalendar->academic_year }}
                                </p>
                            @endif
                        </div>

                        @if ($weeksGenerated)
                            @php $lockedCount = count($lockedWeeks); @endphp
                            <div class="flex flex-wrap gap-1.5">
                                <x-feedback-status.status-indicator variant="brand" :dot="true">
                                    {{ $syllabusWeeks->count() }} Weeks
                                </x-feedback-status.status-indicator>
                                <x-feedback-status.status-indicator variant="brand" :dot="true">
                                    {{ collect($weekEvents)->flatten(1)->count() }} Events
                                </x-feedback-status.status-indicator>
                                @if ($lockedCount > 0)
                                    <x-feedback-status.status-indicator variant="rose" :dot="true">
                                        {{ $lockedCount }} Locked
                                    </x-feedback-status.status-indicator>
                                @endif
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-[13px] text-[#94a3b8] italic flex items-center gap-1.5">
                        <i class="bx bx-calendar-x text-base"></i> Not set
                    </p>
                @endif
            </div>

        </div>
    @endif

</div>
