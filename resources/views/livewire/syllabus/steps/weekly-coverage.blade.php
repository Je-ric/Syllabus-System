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
                    <p class="mb-3 text-xs text-slate-500 flex items-center gap-1.5">
                        <i class="bx bx-info-circle text-slate-400"></i>
                        Keep Course Outcomes consistent across Lecture and Laboratory tabs for each week.
                    </p>
                @endif
            @endif

            {{-- Generate / Regenerate / Save All ──────────────────────────── --}}
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
                        <span class="text-xs text-amber-600 flex items-center gap-1">
                            <i class="bx bx-error-circle"></i> Select a calendar first
                        </span>
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

        {{-- Right: info card ─────────────────────────────────────────────── --}}
        <x-wizard.info-card color="slate">
            <div class="grid grid-cols-2 gap-4">

                {{-- Class schedule --}}
                @if ($courseComponents ?? null)
                    @php $hasLEC = isset($courseComponents['LEC']); $hasLAB = isset($courseComponents['LAB']); @endphp
                    <div>
                        <p class="text-xs font-semibold text-slate-700 mb-2">Class Schedule</p>
                        <div class="space-y-2.5">
                            @if ($hasLEC)
                                <div>
                                    <div class="font-semibold text-emerald-700 text-xs mb-0.5">Lecture (LEC)</div>
                                    <div class="text-slate-600">{{ $courseComponents['LEC']['schedule'] ?? '—' }}</div>
                                    <div class="text-slate-400">{{ $courseComponents['LEC']['class_hours'] ?? '—' }} hrs</div>
                                </div>
                            @endif
                            @if ($hasLAB)
                                <div>
                                    <div class="font-semibold text-blue-700 text-xs mb-0.5">Laboratory (LAB)</div>
                                    <div class="text-slate-600">{{ $courseComponents['LAB']['schedule'] ?? '—' }}</div>
                                    <div class="text-slate-400">{{ $courseComponents['LAB']['class_hours'] ?? '—' }} hrs</div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Calendar summary --}}
                <div>
                    <p class="text-xs font-semibold text-slate-700 mb-2">Academic Calendar</p>
                    @if ($syllabus?->academicCalendar)
                        <div class="text-slate-700 font-medium text-xs">
                            {{ \Carbon\Carbon::parse($syllabus->academicCalendar->start_date)->format('M d, Y') }}
                            <span class="text-slate-400 mx-0.5">–</span>
                            {{ \Carbon\Carbon::parse($syllabus->academicCalendar->end_date)->format('M d, Y') }}
                        </div>
                    @else
                        <span class="text-slate-400 italic text-xs">Not set</span>
                    @endif

                    @if ($weeksGenerated)
                        <div class="mt-2.5 pt-2.5 border-t border-slate-200 space-y-1">
                            <x-wizard.info-row label="Weeks" :value="$syllabusWeeks->count()" bold />
                            <x-wizard.info-row label="Events" :value="collect($weekEvents)->flatten(1)->count()" />
                            @if (count($lockedWeeks) > 0)
                                <div class="flex items-start justify-between gap-2 py-1">
                                    <span class="text-xs font-medium text-slate-500">Locked</span>
                                    <x-wizard.badge variant="rose" icon="lock-alt">
                                        {{ count($lockedWeeks) }} weeks
                                    </x-wizard.badge>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </x-wizard.info-card>
    </div>

    {{-- ══ Empty State ═════════════════════════════════════════════════════════ --}}
    @if ($syllabusWeeks->isEmpty())
        <x-wizard.empty
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
        </x-wizard.empty>
    @else

        @php $hasLEC = isset($courseComponents['LEC']); $hasLAB = isset($courseComponents['LAB']); @endphp

        {{--
            ── LEC / LAB Tab Switcher ────────────────────────────────────────────
            UX design:
            - Clicking a tab fires setComponentType() which in one Livewire request:
            (a) saves the current component silently, (b) switches, (c) repopulates.
            - wire:loading on wire:target="setComponentType" shows a spinner ON the
            tab that was clicked while the request is in flight.
            - The active tab is always highlighted correctly from $activeComponent
            (server-driven, not Alpine-driven) so it stays consistent after re-render.
        --}}
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

                {{-- Inline status while switching --}}
                <div wire:loading wire:target="setComponentType"
                    class="mt-2 flex items-center gap-1.5 text-xs text-slate-400">
                    <i class="bx bx-loader-alt bx-spin"></i>
                    Saving current data and loading {{ $activeComponent === 'LEC' ? 'Laboratory' : 'Lecture' }} content…
                </div>
            </div>

        @elseif ($hasLEC || $hasLAB)
            {{-- Single component label --}}
            <div class="mb-4">
                <x-wizard.badge :variant="$hasLEC ? 'emerald' : 'blue'" :dot="true">
                    {{ $hasLEC ? 'Lecture (LEC)' : 'Laboratory (LAB)' }}
                </x-wizard.badge>
            </div>
        @endif

        {{-- ── Accordion ────────────────────────────────────────────────────── --}}
        {{--
            Alpine $watch: when a week is collapsed (oldVal changes), saveWeek(oldVal)
            fires — committing edits before moving to another week.
            This also ensures wire:model.lazy has had a chance to sync on the blur
            that happens when the user clicks the next accordion header.
        --}}
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

                    $savedTopic = $weekInputs[$wKey]['topic'] ?? '';
                    $refCount   = count(array_filter($weekInputs[$wKey]['references'] ?? [], fn ($r) => trim($r['text'] ?? '') !== ''));
                    $matCount   = count(array_filter($weekInputs[$wKey]['materials'] ?? [], fn ($m) => trim($m['name'] ?? '') !== '' || trim($m['url'] ?? '') !== ''));

                    $lockLabel = match($lockType) {
                        'exam'         => 'Exam Week',
                        'non_teaching' => 'Non-Teaching Week',
                        default        => 'Locked',
                    };

                    // CO code lookup for the accordion badge
                    $coId   = $weekInputs[$wKey]['course_outcome_id'] ?? null;
                    $coCode = null;
                    if ($coId) {
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
                            'hover:bg-rose-50/40 bg-rose-50/20' => $isLocked,
                            'hover:bg-slate-50'                 => ! $isLocked,
                        ])>

                        {{-- Left: week badge + label + date + lock/CO pill --}}
                        <div class="flex items-center gap-3 min-w-0 flex-1">

                            <span @class([
                                'inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold shrink-0',
                                'bg-rose-100 text-rose-700 ring-1 ring-rose-300' => $isLocked,
                                'bg-slate-100 text-slate-600'                    => ! $isLocked,
                            ])>
                                {{ $week->week_no }}
                            </span>

                            <div class="flex items-center gap-2 flex-wrap min-w-0">
                                <span class="font-semibold text-sm {{ $isLocked ? 'text-rose-700' : 'text-slate-800' }} shrink-0">
                                    Week {{ $week->week_no }}
                                </span>

                                <span class="text-xs text-slate-400 shrink-0">
                                    {{ $start->format('M d') }}–{{ $end->format('M d, Y') }}
                                </span>

                                @if ($isLocked)
                                    <x-wizard.badge variant="rose" icon="lock-alt">{{ $lockLabel }}</x-wizard.badge>
                                @else
                                    @if ($coCode)
                                        <x-wizard.badge variant="emerald">{{ $coCode }}</x-wizard.badge>
                                    @endif
                                    @if ($savedTopic)
                                        <span class="text-xs text-slate-400 truncate max-w-xs hidden md:block">
                                            — {{ \Illuminate\Support\Str::limit($savedTopic, 55) }}
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </div>

                        {{-- Right: event/ref/material pills + chevron --}}
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
                        class="px-5 pb-5 pt-1 {{ $isLocked ? 'bg-rose-50/20' : 'bg-white' }}">

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

                            {{-- Other non-locking events --}}
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

                        {{-- EDITABLE ──────────────────────────────────────── --}}
                        @else

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

                            {{--
                                EDITABLE FORM FIELDS
                                ─────────────────────────────────────────────────────────────────
                                wire:model.lazy binds each field to weekInputs.w{n}.field.
                                On mount(), populateWeekInputs() fills these from the DB,
                                so existing saved data appears immediately as editable content.

                                KEY: 'w{week_no}' e.g. 'w1', 'w3' — the 'w' prefix prevents
                                PHP from silently casting the key to an integer, which would
                                break the Livewire JSON snapshot round-trip.

                                NEVER add loadData() to save/blur paths — it would overwrite
                                what the user is actively typing with stale DB values.
                            --}}

                            {{-- Form fields --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
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
                                </div>

                                <div>
                                    <x-form.label for="lo_{{ $wKey }}">Unit Learning Outcomes</x-form.label>
                                    <x-form.textarea id="lo_{{ $wKey }}" rows="4"
                                        placeholder="Enter learning outcomes…"
                                        wire:model.lazy="weekInputs.{{ $wKey }}.learning_outcomes" />
                                </div>
                                <div>
                                    <x-form.label for="at_{{ $wKey }}">Assessment Task</x-form.label>
                                    <x-form.textarea id="at_{{ $wKey }}" rows="4"
                                        placeholder="Enter assessment task…"
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
                                <p class="text-xs text-slate-400 flex items-center gap-1">
                                    <i class="bx bx-info-circle"></i>
                                    Auto-saves when you collapse this week or use Save All above.
                                </p>
                                <x-wizard.btn variant="sm-success"
                                    wire:click="saveWeek({{ $week->week_no }})"
                                    wire:loading.attr="disabled"
                                    wire:target="saveWeek({{ $week->week_no }})"
                                    loading="Saving…">
                                    <i class="bx bx-save"></i> Save Week {{ $week->week_no }}
                                </x-wizard.btn>
                            </div>

                        @endif {{-- end locked/editable --}}
                    </div>{{-- /body --}}
                </div>{{-- /wire:key --}}
            @endforeach

        </div>{{-- /accordion --}}

        <p class="text-xs text-slate-400 mt-4 flex items-center gap-1">
            <i class="bx bx-bulb"></i>
            Use <strong>Save Week N</strong> per accordion section or <strong>Save All</strong> to persist everything at once.
            Navigating steps (Next / Previous) also auto-saves.
        </p>

    @endif
</div>
