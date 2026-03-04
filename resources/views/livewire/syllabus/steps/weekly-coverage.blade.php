<div>

    <div class="mb-5">
        <div class="space-y-3">
            <x-wizard.step-header
                title="Weekly Coverage"
                icon="calendar-week"
                description="Weeks are auto-generated from the academic calendar. 
                            Fill in coverage details per week. Exam and Non-Teaching weeks are locked automatically." >
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
                            <x-feedback-status.alert type="error" :showTitle="false" class="text-xs">
                                No academic calendar selected. Please go back to the previous step and select one to generate weeks.
                            </x-feedback-status.alert>
                        @endif
                    @else
                        <x-wizard.btn variant="sm-warning"
                            wire:click="regenerateWeeks"
                            wire:target="regenerateWeeks"
                            wire:confirm="This will delete all existing weeks and recreate them. All content will be lost. Continue?"
                            loading="Regenerating…">
                            <i class="bx bx-refresh"></i> Regenerate Weeks
                        </x-wizard.btn>

                        <x-wizard.btn variant="sm-success"
                            wire:click="saveAllWeeklyEntries"
                            wire:target="saveAllWeeklyEntries"
                            loading="Saving…">
                            <i class="bx bx-save"></i> Save All
                        </x-wizard.btn>
                    @endif
                </div>
            </div>
            </x-wizard.step-header>

            
        <div class="grid gap-4 text-sm 
                    md:grid-cols-2 
                    xl:grid-cols-3">

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
                        <span class="shrink-0 flex items-center justify-center w-7 h-7 rounded-lg bg-slate-100 text-slate-500">
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

            @if ($weeksGenerated)
                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-2">
                            Coverage Overview
                        </p>

                        @php $lockedCount = count($lockedWeeks); @endphp
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
                            <div class="text-xs text-slate-400 mt-1">
                                Total Weeks
                            </div>
                        </div>

                        <div>
                            <div class="text-2xl font-bold text-amber-600 leading-none">
                                {{ collect($weekEvents)->flatten(1)->count() }}
                            </div>
                            <div class="text-xs text-slate-400 mt-1">
                                Calendar Events
                            </div>
                        </div>

                        <div>
                            <div class="text-2xl font-bold {{ $lockedCount > 0 ? 'text-rose-500' : 'text-slate-300' }} leading-none">
                                {{ $lockedCount }}
                            </div>
                            <div class="text-xs text-slate-400 mt-1">
                                Locked Weeks
                            </div>
                        </div>

                    </div>
                </div>
            @endif

        </div>
    </div>{{-- /header grid --}}

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

        {{-- ── LEC / LAB Tab Switcher ──────────────────────────────────────── --}}
        @if ($hasLEC && $hasLAB)
            <div class="mb-5">
                <div class="inline-flex items-center gap-1 p-1 bg-slate-100 rounded-xl">

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
                <x-feedback-status.status-indicator :variant="$hasLEC ? 'emerald' : 'blue'" :dot="true" size="sm">
                    {{ $hasLEC ? 'Lecture (LEC)' : 'Laboratory (LAB)' }}
                </x-feedback-status.status-indicator>
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

                    // Week 1 is always the MVGO week — no CO dropdown, static badge
                    $isMvgo = ((int) $week->week_no === 1);

                    $savedTopic = $weekInputs[$wKey]['topic'] ?? '';
                    $refCount   = count(array_filter($weekInputs[$wKey]['references'] ?? [], fn ($r) => trim($r['text'] ?? '') !== ''));
                    $matCount   = count(array_filter($weekInputs[$wKey]['materials'] ?? [], fn ($m) => trim($m['name'] ?? '') !== '' || trim($m['url'] ?? '') !== ''));

                    $lockLabel = match($lockType) {
                        'exam'         => 'Exam Week',
                        'non_teaching' => 'Non-Teaching Week',
                        default        => 'Locked',
                    };

                    $coId   = $isMvgo ? null : ($weekInputs[$wKey]['course_outcome_id'] ?? null);
                    $coCode = null;
                    if ($coId) {
                        foreach ($courseOutcomes as $co) {
                            if ($co['id'] == $coId) { $coCode = $co['co_code']; break; }
                        }
                    }
                @endphp

                <div wire:key="week-{{ $week->week_no }}-{{ $activeComponent }}">

                    {{-- ── Accordion Header ──────────────────────────────────── --}}
                    <button type="button"
                        @click="openWeek = openWeek === {{ $week->week_no }} ? null : {{ $week->week_no }}"
                        @class([
                            'w-full flex items-center px-5 py-3.5 transition-colors duration-100 focus:outline-none text-left',
                            'hover:bg-rose-50/30 bg-rose-50/10' => $isLocked,
                            'hover:bg-slate-50'                  => ! $isLocked,
                        ])>

                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            {{-- Week number circle: rose=locked, slate=normal --}}
                            <span @class([
                                'inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold shrink-0',
                                'bg-rose-100 text-rose-700 ring-1 ring-rose-200' => $isLocked,
                                'bg-slate-100 text-slate-600'                    => ! $isLocked,
                            ])>
                                {{ $week->week_no }}
                            </span>

                            <div class="flex items-center gap-2 flex-wrap min-w-0">
                                <span class="font-semibold text-sm shrink-0 {{ $isLocked ? 'text-rose-700' : 'text-slate-800' }}">
                                    Week {{ $week->week_no }}
                                </span>
                                <span class="text-xs text-slate-400 shrink-0">
                                    {{ $start->format('M d') }}–{{ $end->format('M d, Y') }}
                                </span>

                                @if ($isLocked)
                                    {{-- Locked type badge: exam=amber, non-teaching=rose --}}
                                    @if ($lockType === 'exam')
                                        <x-feedback-status.status-indicator variant="rose" icon="bx bx-clipboard" size="sm">{{ $lockLabel }}</x-feedback-status.status-indicator>
                                    @else
                                        <x-feedback-status.status-indicator variant="rose" icon="bx bx-lock-alt" size="sm">{{ $lockLabel }}</x-feedback-status.status-indicator>
                                    @endif
                                @else
                                    @if ($isMvgo)
                                        {{-- Week 1 always labelled MVGO --}}
                                        <x-feedback-status.status-indicator variant="emerald" size="sm">MVGO</x-feedback-status.status-indicator>
                                    @elseif ($coCode)
                                        <x-feedback-status.status-indicator variant="emerald" size="sm">{{ $coCode }}</x-feedback-status.status-indicator>
                                    @endif
                                    @if ($savedTopic)
                                        <span class="text-xs text-slate-400 truncate max-w-xs hidden md:block">
                                            — {{ \Illuminate\Support\Str::limit($savedTopic, 55) }}
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </div>

                        {{-- Right side meta badges --}}
                        <div class="flex items-center gap-1.5 shrink-0 ml-3">
                            @if (! $isLocked)
                                @if (count($events) > 0)
                                    <x-feedback-status.status-indicator variant="amber" size="sm">{{ count($events) }} event{{ count($events) !== 1 ? 's' : '' }}</x-feedback-status.status-indicator>
                                @endif
                                @if ($refCount > 0)
                                    <x-feedback-status.status-indicator variant="emerald" size="sm">{{ $refCount }} ref{{ $refCount !== 1 ? 's' : '' }}</x-feedback-status.status-indicator>
                                @endif
                                @if ($matCount > 0)
                                    <x-feedback-status.status-indicator variant="blue" size="sm">{{ $matCount }} mat{{ $matCount !== 1 ? 's' : '' }}</x-feedback-status.status-indicator>
                                @endif
                            @endif
                            <i class="bx text-slate-400 text-lg transition-transform duration-200"
                                :class="openWeek === {{ $week->week_no }} ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
                        </div>
                    </button>

                    {{-- ── Accordion Body ─────────────────────────────────────── --}}
                    <div x-show="openWeek === {{ $week->week_no }}" x-cloak
                        class="px-5 pb-5 pt-2 {{ $isLocked ? 'bg-rose-50/10' : 'bg-white' }}">

                        {{-- LOCKED ──────────────────────────────────────────── --}}
                        @if ($isLocked)
                            <x-feedback-status.alert type="{{ $lockType === 'exam' ? 'warning' : 'error' }}" :title="$lockLabel" class="mb-4">
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
                            </x-feedback-status.alert>

                            @php $otherEvents = array_filter($events, fn ($ev) => ! in_array($ev['type'], ['exam', 'non_teaching'])); @endphp
                            @if (count($otherEvents) > 0)
                                <x-feedback-status.alert type="info" title="Other events this week">
                                    <ul class="space-y-1 mt-1">
                                        @foreach ($otherEvents as $ev)
                                            <li class="flex items-center gap-1.5">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-400 shrink-0"></span>
                                                {{ $ev['name'] }} <span class="opacity-60">({{ $ev['date_display'] }})</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </x-feedback-status.alert>
                            @endif

                        {{-- EDITABLE ─────────────────────────────────────────── --}}
                        @else

                            {{-- MVGO notice (Week 1 only) --}}
                            @if ($isMvgo)
                                <x-feedback-status.alert type="info" :showTitle="false" class="mb-4">
                                    <strong>Week 1 — MVGO.</strong>
                                    This week covers the Mission, Vision, Goals &amp; Objectives.
                                    All fields are editable. Assessment task is optional — if entered,
                                    it will appear in Course Evaluation.
                                </x-feedback-status.alert>
                            @endif

                            {{-- Calendar events for this week --}}
                            @if (count($events) > 0)
                                <div class="mb-4 rounded-lg border border-slate-200 bg-slate-50 p-3">
                                    <div class="text-xs font-semibold text-slate-600 mb-1.5">
                                        <i class="bx bx-calendar-event"></i> Events this week
                                    </div>
                                    <ul class="space-y-1">
                                        @foreach ($events as $ev)
                                            @php
                                                $evDot = match($ev['type']) {
                                                    'holiday' => 'bg-emerald-400',
                                                    'break'   => 'bg-blue-400',
                                                    default   => 'bg-amber-400',
                                                };
                                            @endphp
                                            <li class="flex items-center gap-1.5 text-xs text-slate-600">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $evDot }} shrink-0"></span>
                                                <span class="font-medium">{{ $ev['name'] }}</span>
                                                <span class="text-slate-400">({{ $ev['date_display'] }})</span>
                                                <x-feedback-status.status-indicator variant="slate" size="sm">{{ str_replace('_', ' ', $ev['type']) }}</x-feedback-status.status-indicator>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            {{-- Form fields --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                {{-- CO field: MVGO badge for Week 1, select for all others --}}
                                <div class="md:col-span-2">
                                    @if ($isMvgo)
                                        <x-form.label>Outcome</x-form.label>
                                        <div class="flex items-center gap-2 mt-1 px-3 py-2.5
                                                    rounded-xl border border-slate-200 bg-slate-50">
                                            <x-feedback-status.status-indicator variant="slate" size="sm">MVGO</x-feedback-status.status-indicator>
                                            <span class="text-xs text-slate-500">Mission-Vision-Goals-Objectives</span>
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
                                    <x-form.label for="at_{{ $wKey }}">
                                        Assessment Task
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
                                        <x-form.label>
                                            <i class="bx bx-book-open text-slate-500"></i> References
                                        </x-form.label>
                                        <x-wizard.btn variant="sm-primary"
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
                                                           focus:border-emerald-400 focus:ring-1 focus:ring-emerald-300 focus:outline-none
                                                           placeholder:text-slate-300" />
                                                @if (count($weekInputs[$wKey]['references'] ?? []) > 1)
                                                    <button type="button"
                                                        wire:click="removeReference({{ $week->week_no }}, {{ $rIdx }})"
                                                        class="shrink-0 p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-md transition-colors"
                                                        title="Remove">
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
                                        <x-form.label>
                                            <i class="bx bx-link text-slate-500"></i> Online Materials
                                        </x-form.label>
                                        <x-wizard.btn variant="sm-primary"
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
                                                               focus:border-blue-400 focus:ring-1 focus:ring-blue-300 focus:outline-none
                                                               placeholder:text-slate-300" />
                                                    <input type="url"
                                                        wire:model.lazy="weekInputs.{{ $wKey }}.materials.{{ $mIdx }}.url"
                                                        placeholder="https://…"
                                                        class="w-full text-xs rounded-lg border border-slate-300 bg-white px-3 py-1.5
                                                               focus:border-blue-400 focus:ring-1 focus:ring-blue-300 focus:outline-none
                                                               placeholder:text-slate-300" />
                                                </div>
                                                @if (count($weekInputs[$wKey]['materials'] ?? []) > 1)
                                                    <button type="button"
                                                        wire:click="removeMaterial({{ $week->week_no }}, {{ $mIdx }})"
                                                        class="shrink-0 mt-1 p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-md transition-colors"
                                                        title="Remove">
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

                            {{-- Per-week footer: hint + reset + save --}}
                            <div class="flex items-center justify-between mt-5 pt-3 border-t border-slate-100">
                                <x-feedback-status.alert type="info" :showTitle="false" class="text-xs">
                                    <span>Changes are saved automatically when you collapse or use Save All above.</span>
                                </x-feedback-status.alert>

                                <div class="flex items-center gap-2">
                                    {{--
                                        Reset Week — clears all content for this week from DB
                                        and resets the form fields to blank. Useful when the
                                        faculty wants to start a week's content over entirely.
                                        Locked weeks are guarded server-side; this button is
                                        only shown on editable weeks.
                                    --}}
                                    <x-wizard.btn variant="sm-cancel"
                                        wire:click="resetWeek({{ $week->week_no }})"
                                        wire:loading.attr="disabled"
                                        wire:target="resetWeek({{ $week->week_no }})"
                                        wire:confirm="Reset Week {{ $week->week_no }}? This will clear all content for this week. Cannot be undone."
                                        loading="Resetting…">
                                        <i class="bx bx-reset"></i> Reset
                                    </x-wizard.btn>

                                    <x-wizard.btn variant="sm-success"
                                        wire:click="saveWeek({{ $week->week_no }})"
                                        wire:loading.attr="disabled"
                                        wire:target="saveWeek({{ $week->week_no }})"
                                        loading="Saving…">
                                        <i class="bx bx-save"></i> Save Week {{ $week->week_no }}
                                    </x-wizard.btn>
                                </div>
                            </div>

                        @endif
                    </div>
                </div>
            @endforeach

        </div>{{-- /accordion --}}

        <x-feedback-status.alert type="info" :showTitle="false" class="mt-2">
            Weeks with scheduled exams or non-teaching classes are locked automatically based on the academic calendar. You can identify them by the badges and red highlight. Click on locked weeks to see details.
        </x-feedback-status.alert>

    @endif
</div>
