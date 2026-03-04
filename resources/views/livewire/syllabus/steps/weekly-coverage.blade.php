<div>

    {{-- ══ Header ══════════════════════════════════════════════════════════════ --}}
    <div class="mb-5 grid grid-cols-1 lg:grid-cols-2 gap-4 items-start">

        {{-- Left: heading + action buttons --}}
        <div>
            <x-wizard.step-header
                title="Weekly Coverage"
                icon="calendar-week"
                description="Weeks are auto-generated from the academic calendar. Fill in coverage details per week. Exam and Non-Teaching weeks are locked automatically." />

            @if ($courseComponents)
                @php $hasLEC = isset($courseComponents['LEC']); $hasLAB = isset($courseComponents['LAB']); @endphp
                @if ($hasLEC && $hasLAB)
                    <x-wizard.alert type="info" class="mb-3">
                        Keep Course Outcomes consistent across Lecture and Laboratory tabs for each week.
                    </x-wizard.alert>
                @endif
            @endif

            {{-- Generate / Regenerate / Save All --}}
            <div class="flex items-center gap-2 flex-wrap">
                @if (! $weeksGenerated)
                    <x-wizard.btn variant="sm-success"
                        wire:click="generateWeeklyCoverage"
                        :disabled="! $academic_calendar_id"
                        wire:target="generateWeeklyCoverage"
                        loading="Generating…">
                        <i class="bx bx-calendar-plus"></i> Generate Weeks
                    </x-wizard.btn>
                    @if (! $academic_calendar_id)
                        <x-wizard.alert type="danger">Select a calendar first</x-wizard.alert>
                    @endif
                @else
                    <x-wizard.btn variant="sm-warning"
                        wire:click="regenerateWeeks"
                        wire:target="regenerateWeeks"
                        wire:confirm="This will delete all existing weeks and recreate them. All content will be lost. Continue?"
                        loading="Regenerating…">
                        <i class="bx bx-refresh"></i> Regenerate Weeks
                    </x-wizard.btn>

                    <x-wizard.btn variant="sm-info"
                        wire:click="saveAllWeeklyEntries"
                        wire:target="saveAllWeeklyEntries"
                        loading="Saving…">
                        <i class="bx bx-save"></i> Save All
                    </x-wizard.btn>
                @endif
            </div>
        </div>

        {{-- ══ Right: Info card — improved UI ════════════════════════════════ --}}
        {{--
            Replaces the flat gray card with a richer layout:
            – LEC/LAB schedule items use their brand colours (emerald/blue)
            – Calendar date range is shown with an icon and clear formatting
            – Stats (weeks, events, locked) use count badges instead of plain rows
            – Each section has a small coloured label so nothing gets lost visually
        --}}
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">

            @if ($courseComponents ?? null)
                @php $hasLEC = isset($courseComponents['LEC']); $hasLAB = isset($courseComponents['LAB']); @endphp

                {{-- ── Class Schedule section ──────────────────────────────── --}}
                <div class="px-4 pt-4 pb-3 border-b border-slate-100">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-2.5">
                        Class Schedule
                    </p>
                    <div class="flex flex-wrap gap-3">

                        @if ($hasLEC)
                            <div class="flex-1 min-w-[120px] rounded-lg bg-emerald-50 border border-emerald-100 px-3 py-2.5">
                                <div class="flex items-center gap-1.5 mb-1">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Lecture</span>
                                </div>
                                <div class="text-xs font-semibold text-slate-800 leading-snug">
                                    {{ $courseComponents['LEC']['schedule'] ?? '—' }}
                                </div>
                                <div class="mt-0.5 flex items-center gap-1 text-[11px] text-emerald-600">
                                    <i class="bx bx-time-five text-xs"></i>
                                    {{ $courseComponents['LEC']['class_hours'] ?? '—' }} hrs/week
                                </div>
                            </div>
                        @endif

                        @if ($hasLAB)
                            <div class="flex-1 min-w-[120px] rounded-lg bg-blue-50 border border-blue-100 px-3 py-2.5">
                                <div class="flex items-center gap-1.5 mb-1">
                                    <span class="w-2 h-2 rounded-full bg-blue-500 shrink-0"></span>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-blue-700">Laboratory</span>
                                </div>
                                <div class="text-xs font-semibold text-slate-800 leading-snug">
                                    {{ $courseComponents['LAB']['schedule'] ?? '—' }}
                                </div>
                                <div class="mt-0.5 flex items-center gap-1 text-[11px] text-blue-600">
                                    <i class="bx bx-time-five text-xs"></i>
                                    {{ $courseComponents['LAB']['class_hours'] ?? '—' }} hrs/week
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            @endif

            {{-- ── Academic Calendar section ────────────────────────────────── --}}
            <div class="px-4 pt-3 pb-3 {{ $weeksGenerated ? 'border-b border-slate-100' : '' }}">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-2">
                    Academic Calendar
                </p>

                @if ($syllabus?->academicCalendar)
                    <div class="flex items-center gap-2">
                        <span class="shrink-0 flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 text-slate-500">
                            <i class="bx bx-calendar text-base"></i>
                        </span>
                        <div>
                            <div class="text-xs font-semibold text-slate-800">
                                {{ \Carbon\Carbon::parse($syllabus->academicCalendar->start_date)->format('M d, Y') }}
                                <span class="text-slate-400 font-normal mx-0.5">to</span>
                                {{ \Carbon\Carbon::parse($syllabus->academicCalendar->end_date)->format('M d, Y') }}
                            </div>
                            @if (isset($syllabus->academicCalendar->academic_year))
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    {{ $syllabus->academicCalendar->academic_year }}
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-2 text-slate-400 text-xs italic">
                        <i class="bx bx-calendar-x text-base"></i>
                        No calendar selected yet
                    </div>
                @endif
            </div>

            {{-- ── Coverage stats section (only after weeks are generated) ───── --}}
            @if ($weeksGenerated)
                <div class="px-4 pt-3 pb-4 bg-slate-50/60">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-2.5">
                        Coverage Overview
                    </p>
                    <div class="grid grid-cols-3 gap-2">

                        {{-- Total weeks --}}
                        <div class="rounded-lg bg-white border border-slate-200 px-3 py-2 text-center shadow-sm">
                            <div class="text-lg font-bold text-slate-800 leading-none">
                                {{ $syllabusWeeks->count() }}
                            </div>
                            <div class="text-[10px] text-slate-400 mt-0.5 font-medium">Weeks</div>
                        </div>

                        {{-- Total events --}}
                        @php $totalEvents = collect($weekEvents)->flatten(1)->count(); @endphp
                        <div class="rounded-lg bg-white border border-amber-100 px-3 py-2 text-center shadow-sm">
                            <div class="text-lg font-bold text-amber-600 leading-none">
                                {{ $totalEvents }}
                            </div>
                            <div class="text-[10px] text-slate-400 mt-0.5 font-medium">Events</div>
                        </div>

                        {{-- Locked weeks --}}
                        @php $lockedCount = count($lockedWeeks); @endphp
                        <div class="rounded-lg bg-white border border-rose-100 px-3 py-2 text-center shadow-sm">
                            <div class="text-lg font-bold {{ $lockedCount > 0 ? 'text-rose-500' : 'text-slate-300' }} leading-none">
                                {{ $lockedCount }}
                            </div>
                            <div class="text-[10px] text-slate-400 mt-0.5 font-medium flex items-center justify-center gap-0.5">
                                @if ($lockedCount > 0)
                                    <i class="bx bx-lock-alt text-rose-400 text-xs"></i>
                                @endif
                                Locked
                            </div>
                        </div>

                    </div>

                    {{-- Exam weeks breakdown --}}
                    @php
                        $examWeekNos = collect($lockedWeeks)->filter(fn ($t) => $t === 'exam')->keys();
                        $ntWeekNos   = collect($lockedWeeks)->filter(fn ($t) => $t === 'non_teaching')->keys();
                    @endphp
                    @if ($examWeekNos->isNotEmpty() || $ntWeekNos->isNotEmpty())
                        <div class="mt-2.5 flex flex-wrap gap-1.5">
                            @foreach ($examWeekNos as $wn)
                                <x-wizard.badge variant="amber" icon="clipboard">Wk {{ $wn }} Exam</x-wizard.badge>
                            @endforeach
                            @foreach ($ntWeekNos as $wn)
                                <x-wizard.badge variant="rose" icon="calendar-x">Wk {{ $wn }} Non-Teaching</x-wizard.badge>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

        </div>{{-- /info card --}}
    </div>

    {{-- ══ Empty State ═════════════════════════════════════════════════════════ --}}
    @if ($syllabusWeeks->isEmpty())
        <x-empty-state
            icon="calendar-x"
            title="No weeks generated yet"
            message="Select an academic calendar in the previous step, then click Generate Weeks.">
            <x-wizard.btn variant="sm-success"
                wire:click="generateWeeklyCoverage"
                :disabled="! $academic_calendar_id"
                wire:target="generateWeeklyCoverage"
                loading="Generating…">
                <i class="bx bx-calendar-plus"></i> Generate Weeks
            </x-wizard.btn>
        </x-empty-state>
    @else

        @php $hasLEC = isset($courseComponents['LEC']); $hasLAB = isset($courseComponents['LAB']); @endphp

        {{-- ── LEC / LAB Tab Switcher ────────────────────────────────────────── --}}
        @if ($hasLEC && $hasLAB)
            <div class="mb-5">
                <div class="inline-flex items-center gap-1 p-1 bg-slate-100 rounded-xl">

                    {{-- LEC tab --}}
                    <button type="button"
                        wire:click="setComponentType('LEC')"
                        wire:loading.attr="disabled" wire:target="setComponentType"
                        @class([
                            'relative flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-all duration-150',
                            'bg-white text-emerald-700 shadow-sm ring-1 ring-slate-200' => $activeComponent === 'LEC',
                            'text-slate-500 hover:text-slate-700 hover:bg-white/60'     => $activeComponent !== 'LEC',
                        ])>
                        <span wire:loading wire:target="setComponentType('LEC')" class="flex items-center gap-1.5">
                            <i class="bx bx-loader-alt bx-spin text-emerald-600"></i> Switching…
                        </span>
                        <span wire:loading.remove wire:target="setComponentType('LEC')" class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full {{ $activeComponent === 'LEC' ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                            Lecture (LEC)
                        </span>
                        @if ($activeComponent === 'LEC')
                            <span wire:loading wire:target="setComponentType('LAB')"
                                class="absolute -top-1.5 -right-1.5 inline-flex items-center gap-0.5
                                       bg-amber-100 text-amber-700 text-[10px] font-semibold
                                       px-1.5 py-0.5 rounded-full border border-amber-200">
                                <i class="bx bx-loader-alt bx-spin text-[10px]"></i> saving
                            </span>
                        @endif
                    </button>

                    {{-- LAB tab --}}
                    <button type="button"
                        wire:click="setComponentType('LAB')"
                        wire:loading.attr="disabled" wire:target="setComponentType"
                        @class([
                            'relative flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-all duration-150',
                            'bg-white text-blue-700 shadow-sm ring-1 ring-slate-200' => $activeComponent === 'LAB',
                            'text-slate-500 hover:text-slate-700 hover:bg-white/60'  => $activeComponent !== 'LAB',
                        ])>
                        <span wire:loading wire:target="setComponentType('LAB')" class="flex items-center gap-1.5">
                            <i class="bx bx-loader-alt bx-spin text-blue-600"></i> Switching…
                        </span>
                        <span wire:loading.remove wire:target="setComponentType('LAB')" class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full {{ $activeComponent === 'LAB' ? 'bg-blue-500' : 'bg-slate-300' }}"></span>
                            Laboratory (LAB)
                        </span>
                        @if ($activeComponent === 'LAB')
                            <span wire:loading wire:target="setComponentType('LEC')"
                                class="absolute -top-1.5 -right-1.5 inline-flex items-center gap-0.5
                                       bg-amber-100 text-amber-700 text-[10px] font-semibold
                                       px-1.5 py-0.5 rounded-full border border-amber-200">
                                <i class="bx bx-loader-alt bx-spin text-[10px]"></i> saving
                            </span>
                        @endif
                    </button>

                </div>

                <div wire:loading wire:target="setComponentType"
                    class="mt-2 flex items-center gap-1.5 text-xs text-slate-400">
                    <i class="bx bx-loader-alt bx-spin"></i>
                    Saving current data and loading {{ $activeComponent === 'LEC' ? 'Laboratory' : 'Lecture' }} content…
                </div>
            </div>

        @elseif ($hasLEC || $hasLAB)
            <div class="mb-4">
                <x-wizard.badge :variant="$hasLEC ? 'emerald' : 'blue'" :dot="true">
                    {{ $hasLEC ? 'Lecture (LEC)' : 'Laboratory (LAB)' }}
                </x-wizard.badge>
            </div>
        @endif

        {{-- ── Accordion ────────────────────────────────────────────────────── --}}
        <div x-data="{
                openWeek: {{ $syllabusWeeks->first()?->week_no ?? 1 }},
                init() {
                    this.$watch('openWeek', (newVal, oldVal) => {
                        if (oldVal !== null && oldVal !== newVal) {
                            $wire.saveWeek(oldVal);
                        }
                    });
                }
            }"
            class="rounded-xl border border-slate-200 bg-white shadow-sm divide-y divide-slate-100">

            @foreach ($syllabusWeeks as $week)
                @php
                    $wKey     = 'w' . $week->week_no;
                    $start    = \Carbon\Carbon::parse($week->start_date);
                    $end      = \Carbon\Carbon::parse($week->end_date);
                    $events   = $weekEvents[$week->week_no] ?? [];
                    $isLocked = isset($lockedWeeks[$week->week_no]);
                    $lockType = $lockedWeeks[$week->week_no] ?? null;

                    // Week 1 is always the MVGO week — no CO dropdown, just a badge
                    $isMvgo = ((int) $week->week_no === 1);

                    $savedTopic = $weekInputs[$wKey]['topic'] ?? '';
                    $refCount   = count(array_filter($weekInputs[$wKey]['references'] ?? [], fn ($r) => trim($r['text'] ?? '') !== ''));
                    $matCount   = count(array_filter($weekInputs[$wKey]['materials'] ?? [], fn ($m) => trim($m['name'] ?? '') !== '' || trim($m['url'] ?? '') !== ''));

                    $lockLabel = match($lockType) {
                        'exam'         => 'Exam Week',
                        'non_teaching' => 'Non-Teaching Week',
                        default        => 'Locked',
                    };

                    // CO badge in accordion header (only for non-MVGO weeks)
                    $coId   = $weekInputs[$wKey]['course_outcome_id'] ?? null;
                    $coCode = null;
                    if (! $isMvgo && $coId) {
                        foreach ($courseOutcomes as $co) {
                            if ($co['id'] == $coId) { $coCode = $co['co_code']; break; }
                        }
                    }
                @endphp

                <div wire:key="week-{{ $week->week_no }}-{{ $activeComponent }}">

                    {{-- Accordion Header ──────────────────────────────────── --}}
                    <button type="button"
                        @click="openWeek = openWeek === {{ $week->week_no }} ? null : {{ $week->week_no }}"
                        @class([
                            'w-full flex items-center px-5 py-3.5 transition-colors duration-100 focus:outline-none text-left',
                            'hover:bg-rose-50/40 bg-rose-50/20'   => $isLocked,
                            'hover:bg-violet-50/30 bg-violet-50/10' => ! $isLocked && $isMvgo,
                            'hover:bg-slate-50'                   => ! $isLocked && ! $isMvgo,
                        ])>

                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <span @class([
                                'inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold shrink-0',
                                'bg-rose-100 text-rose-700 ring-1 ring-rose-300'     => $isLocked,
                                'bg-violet-100 text-violet-700 ring-1 ring-violet-300' => ! $isLocked && $isMvgo,
                                'bg-slate-100 text-slate-600'                        => ! $isLocked && ! $isMvgo,
                            ])>
                                {{ $week->week_no }}
                            </span>

                            <div class="flex items-center gap-2 flex-wrap min-w-0">
                                <span class="font-semibold text-sm shrink-0 {{
                                    $isLocked ? 'text-rose-700' : ($isMvgo ? 'text-violet-800' : 'text-slate-800')
                                }}">
                                    Week {{ $week->week_no }}
                                </span>

                                <span class="text-xs text-slate-400 shrink-0">
                                    {{ $start->format('M d') }}–{{ $end->format('M d, Y') }}
                                </span>

                                @if ($isLocked)
                                    <x-wizard.badge variant="rose" icon="lock-alt">{{ $lockLabel }}</x-wizard.badge>
                                @elseif ($isMvgo)
                                    {{-- Week 1 always shows MVGO badge --}}
                                    <x-wizard.badge variant="violet" icon="star">MVGO</x-wizard.badge>
                                @else
                                    @if ($coCode)
                                        <x-wizard.badge variant="emerald">{{ $coCode }}</x-wizard.badge>
                                    @endif
                                @endif

                                @if (! $isLocked && $savedTopic)
                                    <span class="text-xs text-slate-400 truncate max-w-xs hidden md:block">
                                        — {{ \Illuminate\Support\Str::limit($savedTopic, 55) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0 ml-3">
                            @if (! $isLocked && (count($events) > 0 || $refCount > 0 || $matCount > 0))
                                @if (count($events) > 0)
                                    <x-wizard.badge variant="amber">
                                        {{ count($events) }} event{{ count($events) !== 1 ? 's' : '' }}
                                    </x-wizard.badge>
                                @endif
                                @if ($refCount > 0)
                                    <x-wizard.badge variant="violet">{{ $refCount }} ref{{ $refCount !== 1 ? 's' : '' }}</x-wizard.badge>
                                @endif
                                @if ($matCount > 0)
                                    <x-wizard.badge variant="sky">{{ $matCount }} mat{{ $matCount !== 1 ? 's' : '' }}</x-wizard.badge>
                                @endif
                            @endif
                            <i class="bx text-slate-400 text-lg transition-transform duration-200"
                                :class="openWeek === {{ $week->week_no }} ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
                        </div>
                    </button>

                    {{-- Accordion Body ────────────────────────────────────── --}}
                    <div x-show="openWeek === {{ $week->week_no }}" x-cloak
                        class="px-5 pb-5 pt-1 {{ $isLocked ? 'bg-rose-50/20' : ($isMvgo ? 'bg-violet-50/10' : 'bg-white') }}">

                        {{-- LOCKED ────────────────────────────────────────── --}}
                        @if ($isLocked)
                            <x-wizard.alert type="danger" :title="$lockLabel" class="mb-4">
                                This week contains a
                                <strong>{{ $lockType === 'exam' ? 'scheduled exam' : 'non-teaching class' }}</strong>
                                in the academic calendar. Coverage details cannot be entered.
                                @if (count($events) > 0)
                                    <ul class="mt-2 space-y-0.5">
                                        @foreach ($events as $ev)
                                            @if (in_array($ev['type'], ['exam', 'non_teaching']))
                                                <li class="flex items-center gap-1.5">
                                                    <i class="bx bx-calendar-event text-xs"></i>
                                                    {{ $ev['name'] }} — {{ $ev['date_display'] }}
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @endif
                            </x-wizard.alert>

                            @php $otherEvents = array_filter($events, fn ($ev) => ! in_array($ev['type'], ['exam', 'non_teaching'])); @endphp
                            @if (count($otherEvents) > 0)
                                <x-wizard.alert type="info" title="Other events this week">
                                    <ul class="space-y-1 mt-1">
                                        @foreach ($otherEvents as $ev)
                                            <li class="flex items-center gap-1.5">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-400 shrink-0"></span>
                                                {{ $ev['name'] }} <span class="opacity-60">({{ $ev['date_display'] }})</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </x-wizard.alert>
                            @endif

                        {{-- EDITABLE (all weeks including MVGO) ────────────── --}}
                        @else

                            {{--
                                MVGO notice — shown at top of Week 1 body only.
                                Explains why there is no CO dropdown.
                                Fields are NOT disabled — faculty can fill all details.
                            --}}
                            @if ($isMvgo)
                                <x-wizard.alert type="info" class="mb-4">
                                    <div class="flex items-start gap-2">
                                        <x-wizard.badge variant="violet" icon="star" class="shrink-0 mt-0.5">MVGO</x-wizard.badge>
                                        <span>
                                            Week 1 is the <strong>Mission-Vision-Goals-Objectives</strong> week.
                                            It is required for every syllabus. You may optionally add an assessment task —
                                            if provided, it will appear in Course Evaluation.
                                        </span>
                                    </div>
                                </x-wizard.alert>
                            @endif

                            {{-- Non-locking calendar events --}}
                            @if (count($events) > 0)
                                <div class="mb-4 rounded-lg border border-slate-200 bg-slate-50 p-3">
                                    <div class="text-xs font-semibold text-slate-600 mb-1.5">
                                        <i class="bx bx-calendar-event"></i> Events this week
                                    </div>
                                    <ul class="space-y-1">
                                        @foreach ($events as $ev)
                                            @php
                                                $evDot = match($ev['type']) {
                                                    'holiday' => 'bg-green-400',
                                                    'break'   => 'bg-blue-400',
                                                    default   => 'bg-amber-400',
                                                };
                                            @endphp
                                            <li class="flex items-center gap-1.5 text-xs text-slate-600">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $evDot }} shrink-0"></span>
                                                <span class="font-medium">{{ $ev['name'] }}</span>
                                                <span class="text-slate-400">({{ $ev['date_display'] }})</span>
                                                <x-wizard.badge variant="slate">{{ str_replace('_', ' ', $ev['type']) }}</x-wizard.badge>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            {{-- Form fields --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                {{-- CO field — MVGO badge for Week 1, select for all others --}}
                                <div class="md:col-span-2">
                                    @if ($isMvgo)
                                        {{--
                                            Week 1: no CO dropdown. Show a read-only MVGO label.
                                            All other fields (ULO, assessment, topics, TLA) remain
                                            fully editable — faculty can optionally add tasks.
                                        --}}
                                        <x-form.label>Outcome</x-form.label>
                                        <div class="flex items-center gap-2 mt-1 px-3 py-2.5
                                                    rounded-xl border border-violet-200 bg-violet-50/60">
                                            <x-wizard.badge variant="violet" icon="star">MVGO</x-wizard.badge>
                                            <span class="text-xs text-violet-700 font-medium">
                                                Mission-Vision-Goals-Objectives
                                            </span>
                                        </div>
                                    @else
                                        <x-form.label for="co_{{ $wKey }}">Course Outcome</x-form.label>
                                        <x-form.select id="co_{{ $wKey }}"
                                            wire:model.lazy="weekInputs.{{ $wKey }}.course_outcome_id">
                                            <option value="">— Select Course Outcome —</option>
                                            @foreach ($courseOutcomes as $outcome)
                                                <option value="{{ $outcome['id'] }}">
                                                    {{ $outcome['co_code'] }} – {{ \Illuminate\Support\Str::limit($outcome['description'], 70) }}
                                                </option>
                                            @endforeach
                                        </x-form.select>
                                    @endif
                                </div>

                                <div>
                                    <x-form.label for="lo_{{ $wKey }}">Unit Learning Outcomes</x-form.label>
                                    <x-form.textarea id="lo_{{ $wKey }}" rows="4"
                                        placeholder="Enter learning outcomes…"
                                        wire:model.lazy="weekInputs.{{ $wKey }}.learning_outcomes" />
                                </div>
                                <div>
                                    <x-form.label for="at_{{ $wKey }}">Assessment Task
                                        @if ($isMvgo)
                                            <span class="text-slate-400 font-normal text-xs ml-1">(optional)</span>
                                        @endif
                                    </x-form.label>
                                    <x-form.textarea id="at_{{ $wKey }}" rows="4"
                                        placeholder="{{ $isMvgo ? 'Optional — e.g. Orientation Quiz' : 'Enter assessment task…' }}"
                                        wire:model.lazy="weekInputs.{{ $wKey }}.assessment_task" />
                                </div>
                                <div>
                                    <x-form.label for="tp_{{ $wKey }}">Topics</x-form.label>
                                    <x-form.textarea id="tp_{{ $wKey }}" rows="4"
                                        placeholder="Enter topics covered…"
                                        wire:model.lazy="weekInputs.{{ $wKey }}.topic" />
                                </div>
                                <div>
                                    <x-form.label for="tla_{{ $wKey }}">Teaching &amp; Learning Activities</x-form.label>
                                    <x-form.textarea id="tla_{{ $wKey }}" rows="4"
                                        placeholder="Enter teaching activities…"
                                        wire:model.lazy="weekInputs.{{ $wKey }}.teaching_activities" />
                                </div>
                            </div>

                            {{-- References & Materials --}}
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">

                                {{-- References --}}
                                <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                                    <div class="flex items-center justify-between mb-2.5">
                                        <span class="text-xs font-semibold text-slate-700 flex items-center gap-1">
                                            <i class="bx bx-book-open text-slate-500"></i> References
                                        </span>
                                        <x-wizard.btn variant="sm-soft"
                                            wire:click="addReference({{ $week->week_no }})">
                                            <i class="bx bx-plus text-sm"></i> Add
                                        </x-wizard.btn>
                                    </div>
                                    <div class="space-y-2">
                                        @foreach ($weekInputs[$wKey]['references'] ?? [['text' => '']] as $rIdx => $ref)
                                            <div class="flex items-center gap-2" wire:key="ref-{{ $wKey }}-{{ $rIdx }}">
                                                <input type="text"
                                                    wire:model.lazy="weekInputs.{{ $wKey }}.references.{{ $rIdx }}.text"
                                                    placeholder="e.g. Author (Year). Title. Publisher."
                                                    class="flex-1 text-xs rounded-lg border border-slate-300 bg-white px-3 py-1.5
                                                           focus:border-violet-400 focus:ring-1 focus:ring-violet-300 focus:outline-none
                                                           placeholder:text-slate-300" />
                                                @if (count($weekInputs[$wKey]['references'] ?? []) > 1)
                                                    <button type="button"
                                                        wire:click="removeReference({{ $week->week_no }}, {{ $rIdx }})"
                                                        class="shrink-0 p-1.5 text-slate-400 hover:text-rose-500
                                                               hover:bg-rose-50 rounded-md transition-colors"
                                                        title="Remove reference">
                                                        <i class="bx bx-trash text-sm"></i>
                                                    </button>
                                                @else
                                                    <span class="w-7 shrink-0"></span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Online Materials --}}
                                <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                                    <div class="flex items-center justify-between mb-2.5">
                                        <span class="text-xs font-semibold text-slate-700 flex items-center gap-1">
                                            <i class="bx bx-link text-slate-500"></i> Online Materials
                                        </span>
                                        <x-wizard.btn variant="sm-info"
                                            wire:click="addMaterial({{ $week->week_no }})">
                                            <i class="bx bx-plus text-sm"></i> Add
                                        </x-wizard.btn>
                                    </div>
                                    <div class="space-y-3">
                                        @foreach ($weekInputs[$wKey]['materials'] ?? [['name' => '', 'url' => '']] as $mIdx => $mat)
                                            <div class="flex items-start gap-2" wire:key="mat-{{ $wKey }}-{{ $mIdx }}">
                                                <div class="flex-1 space-y-1.5">
                                                    <input type="text"
                                                        wire:model.lazy="weekInputs.{{ $wKey }}.materials.{{ $mIdx }}.name"
                                                        placeholder="Name (e.g. Week {{ $week->week_no }} Slides)"
                                                        class="w-full text-xs rounded-lg border border-slate-300 bg-white px-3 py-1.5
                                                               focus:border-sky-400 focus:ring-1 focus:ring-sky-300 focus:outline-none
                                                               placeholder:text-slate-300" />
                                                    <input type="url"
                                                        wire:model.lazy="weekInputs.{{ $wKey }}.materials.{{ $mIdx }}.url"
                                                        placeholder="https://…"
                                                        class="w-full text-xs rounded-lg border border-slate-300 bg-white px-3 py-1.5
                                                               focus:border-sky-400 focus:ring-1 focus:ring-sky-300 focus:outline-none
                                                               placeholder:text-slate-300" />
                                                </div>
                                                @if (count($weekInputs[$wKey]['materials'] ?? []) > 1)
                                                    <button type="button"
                                                        wire:click="removeMaterial({{ $week->week_no }}, {{ $mIdx }})"
                                                        class="shrink-0 mt-1 p-1.5 text-slate-400 hover:text-rose-500
                                                               hover:bg-rose-50 rounded-md transition-colors"
                                                        title="Remove material">
                                                        <i class="bx bx-trash text-sm"></i>
                                                    </button>
                                                @else
                                                    <span class="w-7 shrink-0 mt-1"></span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- Per-week save footer --}}
                            <div class="flex items-center justify-between mt-5 pt-3 border-t border-slate-100">
                                <x-wizard.alert type="info" class="px-3 py-1.5">
                                    Changes are auto-saved when you collapse this section or navigate away. Use the Save buttons to persist without leaving the section.
                                </x-wizard.alert>
                                <x-wizard.btn variant="sm-success"
                                    wire:click="saveWeek({{ $week->week_no }})"
                                    wire:loading.attr="disabled"
                                    wire:target="saveWeek({{ $week->week_no }})"
                                    loading="Saving…">
                                    <i class="bx bx-save"></i> Save Week {{ $week->week_no }}
                                </x-wizard.btn>
                                <x-wizard.btn variant="cancel" type="reset">
                                    Reset
                                </x-wizard.btn>
                            </div>

                        @endif
                    </div>
                </div>
            @endforeach

        </div>{{-- /accordion --}}

        <x-wizard.alert type="info" class="mt-4">
            <i class="bx bx-info-circle text-sky-400"></i>
            Remember to click <strong>Save Week N</strong> for each week or <strong>Save All</strong> at the top to persist your changes.
        </x-wizard.alert>

    @endif
</div>