<div>
    {{-- ══ Header ══════════════════════════════════════════════════════════════ --}}
    <div class="mb-5 grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div>
            <h3 class="text-xl font-semibold text-slate-900">Weekly Coverage</h3>
            <p class="mt-0.5 text-sm text-slate-500">
                Weeks are auto-generated from the academic calendar. Fill in coverage details per week. <br
                Weeks containing <strong>Exam</strong> or <strong>Non-Teaching</strong> calendar events are locked automatically.
            </p>

            <div class="mt-3 flex items-center gap-2 flex-wrap">
                @if (! $weeksGenerated)
                    <button type="button" wire:click="generateWeeklyCoverage"
                        @if (! $academic_calendar_id) disabled @endif
                        wire:loading.attr="disabled" wire:target="generateWeeklyCoverage"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg
                                border border-emerald-300 bg-emerald-50 text-emerald-700 hover:bg-emerald-100
                                disabled:bg-slate-100 disabled:text-slate-400 disabled:border-slate-200 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="generateWeeklyCoverage">
                            <i class="bx bx-calendar-plus"></i> Generate Weeks
                        </span>
                        <span wire:loading wire:target="generateWeeklyCoverage">
                            <i class="bx bx-loader-alt bx-spin"></i> Generating…
                        </span>
                    </button>
                @else
                    <button type="button" wire:click="regenerateWeeks"
                        wire:loading.attr="disabled" wire:target="regenerateWeeks"
                        wire:confirm="This will delete all existing weeks and recreate them. All content will be lost. Continue?"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg
                                border border-amber-300 bg-amber-50 text-amber-700 hover:bg-amber-100
                                disabled:opacity-60 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="regenerateWeeks">
                            <i class="bx bx-refresh"></i> Regenerate Weeks
                        </span>
                        <span wire:loading wire:target="regenerateWeeks">
                            <i class="bx bx-loader-alt bx-spin"></i> Regenerating…
                        </span>
                    </button>

                    <button type="button" wire:click="saveAllWeeklyEntries"
                        wire:loading.attr="disabled" wire:target="saveAllWeeklyEntries"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg
                                border border-blue-300 bg-blue-50 text-blue-700 hover:bg-blue-100
                                disabled:opacity-60 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="saveAllWeeklyEntries">
                            <i class="bx bx-save"></i> Save All
                        </span>
                        <span wire:loading wire:target="saveAllWeeklyEntries">
                            <i class="bx bx-loader-alt bx-spin"></i> Saving…
                        </span>
                    </button>
                @endif
            </div>
        </div>

        {{-- Info card --}}
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-xs text-slate-600">
            <div class="grid grid-cols-2 gap-4">
                @if ($courseComponents)
                    @php $hasLEC = isset($courseComponents['LEC']); $hasLAB = isset($courseComponents['LAB']); @endphp
                    <div>
                        <div class="font-semibold text-slate-700 mb-2">Class Schedule</div>
                        <div class="space-y-2.5">
                            @if ($hasLEC)
                                <div>
                                    <div class="font-semibold text-emerald-700 mb-0.5">Lecture (LEC)</div>
                                    <div class="text-slate-600">{{ $courseComponents['LEC']['schedule'] ?? 'N/A' }}</div>
                                    <div class="text-slate-500">{{ $courseComponents['LEC']['class_hours'] ?? '—' }} hours</div>
                                </div>
                            @endif
                            @if ($hasLAB)
                                <div>
                                    <div class="font-semibold text-blue-700 mb-0.5">Laboratory (LAB)</div>
                                    <div class="text-slate-600">{{ $courseComponents['LAB']['schedule'] ?? 'N/A' }}</div>
                                    <div class="text-slate-500">{{ $courseComponents['LAB']['class_hours'] ?? '—' }} hours</div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                <div>
                    <div class="font-semibold text-slate-700 mb-2">Academic Calendar</div>
                    @if ($syllabus?->academicCalendar)
                        <div class="text-slate-700 font-medium">
                            {{ \Carbon\Carbon::parse($syllabus->academicCalendar->start_date)->format('M d, Y') }}
                            <span class="text-slate-400 mx-0.5">–</span>
                            {{ \Carbon\Carbon::parse($syllabus->academicCalendar->end_date)->format('M d, Y') }}
                        </div>
                    @else
                        <span class="text-slate-400 italic">Not set</span>
                    @endif

                    @if ($weeksGenerated)
                        <div class="mt-3 pt-2.5 border-t border-slate-200 space-y-1">
                            <div><span class="font-semibold text-slate-700">{{ $syllabusWeeks->count() }}</span> weeks</div>
                            <div><span class="font-semibold text-slate-700">{{ collect($weekEvents)->flatten(1)->count() }}</span> calendar events</div>
                            @if (count($lockedWeeks) > 0)
                                <div>
                                    <span class="font-semibold text-red-600">{{ count($lockedWeeks) }}</span>
                                    <span class="text-red-500"> locked</span>
                                    <span class="text-slate-400">(exam/non-teaching)</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ══ Empty State ═════════════════════════════════════════════════════════ --}}
    @if ($syllabusWeeks->isEmpty())
        <div class="text-center py-14 bg-slate-50 rounded-xl border-2 border-dashed border-slate-200">
            <i class="bx bx-calendar-x text-5xl text-slate-300"></i>
            <p class="mt-2 text-sm font-medium text-slate-500">No weeks generated yet.</p>
            <p class="mt-1 text-xs text-slate-400">Select an academic calendar and click <strong>Generate Weeks</strong> to begin.</p>
        </div>

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
                        {{-- Spinner shown on THIS tab while switching TO it --}}
                        <span wire:loading wire:target="setComponentType('LEC')" class="flex items-center gap-1.5">
                            <i class="bx bx-loader-alt bx-spin text-emerald-600"></i> Switching…
                        </span>
                        <span wire:loading.remove wire:target="setComponentType('LEC')" class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full {{ $activeComponent === 'LEC' ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                            Lecture (LEC)
                        </span>
                        {{-- Subtle "saving…" hint shown on the active tab when switching AWAY from it --}}
                        @if ($activeComponent === 'LEC')
                            <span wire:loading wire:target="setComponentType('LAB')"
                                class="absolute -top-1.5 -right-1.5 flex items-center gap-0.5
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
                                class="absolute -top-1.5 -right-1.5 flex items-center gap-0.5
                                        bg-amber-100 text-amber-700 text-[10px] font-semibold
                                        px-1.5 py-0.5 rounded-full border border-amber-200">
                                <i class="bx bx-loader-alt bx-spin text-[10px]"></i> saving
                            </span>
                        @endif
                    </button>
                </div>

                {{-- Inline status bar shown during any setComponentType request --}}
                <div wire:loading wire:target="setComponentType"
                    class="mt-2 flex items-center gap-1.5 text-xs text-slate-400">
                    <i class="bx bx-loader-alt bx-spin"></i>
                    Saving current data and loading {{ $activeComponent === 'LEC' ? 'Laboratory' : 'Lecture' }} content…
                </div>
            </div>

        {{-- Single component — no switcher needed, just a label --}}
        @elseif ($hasLEC || $hasLAB)
            <div class="mb-4">
                <span @class([
                    'inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg border',
                    'bg-emerald-50 text-emerald-700 border-emerald-200' => $hasLEC,
                    'bg-blue-50 text-blue-700 border-blue-200'          => $hasLAB && ! $hasLEC,
                ])>
                    <span class="w-2 h-2 rounded-full {{ $hasLEC ? 'bg-emerald-500' : 'bg-blue-500' }}"></span>
                    {{ $hasLEC ? 'Lecture (LEC)' : 'Laboratory (LAB)' }}
                </span>
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
                    $wKey       = 'w' . $week->week_no;
                    $start      = \Carbon\Carbon::parse($week->start_date);
                    $end        = \Carbon\Carbon::parse($week->end_date);
                    $events     = $weekEvents[$week->week_no] ?? [];
                    $isLocked   = isset($lockedWeeks[$week->week_no]);
                    $lockType   = $lockedWeeks[$week->week_no] ?? null;
                    $savedTopic = $weekInputs[$wKey]['topic'] ?? '';
                    $refCount   = count(array_filter($weekInputs[$wKey]['references'] ?? [], fn ($r) => trim($r['text'] ?? '') !== ''));
                    $matCount   = count(array_filter($weekInputs[$wKey]['materials'] ?? [], fn ($m) => trim($m['name'] ?? '') !== '' || trim($m['url'] ?? '') !== ''));

                    $lockLabel = match($lockType) {
                        'exam'         => 'Exam Week',
                        'non_teaching' => 'Non-Teaching Week',
                        default        => 'Locked',
                    };
                @endphp

                <div wire:key="week-{{ $week->week_no }}-{{ $activeComponent }}">

                    {{-- Accordion Header --}}
                    <button type="button"
                        @click="openWeek = openWeek === {{ $week->week_no }} ? null : {{ $week->week_no }}"
                        @class([
                            'w-full flex items-center px-5 py-3.5 transition-colors duration-100 focus:outline-none',
                            'hover:bg-red-50/40 bg-red-50/20' => $isLocked,
                            'hover:bg-slate-50'               => ! $isLocked,
                        ])>

                        {{-- Left --}}
                        <div class="flex items-center gap-3 min-w-0">
                            {{-- Week badge --}}
                            <span @class([
                                'inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold shrink-0',
                                'bg-red-100 text-red-700 ring-1 ring-red-300'   => $isLocked,
                                'bg-slate-100 text-slate-600'                   => ! $isLocked,
                            ])>
                                {{ $week->week_no }}
                            </span>

                            <div class="flex items-center gap-2 min-w-0">
                                <span class="font-semibold text-sm {{ $isLocked ? 'text-red-700' : 'text-slate-800' }} shrink-0">
                                    Week {{ $week->week_no }}
                                </span>
                                <span class="text-xs text-slate-400 shrink-0">
                                    ({{ $start->format('M d') }} – {{ $end->format('M d, Y') }})
                                </span>
                                @if ($isLocked)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-semibold
                                                rounded-full bg-red-100 text-red-700 border border-red-200 shrink-0">
                                        <i class="bx bx-lock-alt text-xs"></i> {{ $lockLabel }}
                                    </span>
                                @elseif ($savedTopic)
                                    <span class="text-xs text-slate-400 truncate max-w-xs hidden md:block">
                                        — {{ \Illuminate\Support\Str::limit($savedTopic, 55) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="flex-1"></div>

                        {{-- Right: pills + chevron --}}
                        <div class="flex items-center gap-3 shrink-0">
                            @if (! $isLocked && (count($events) > 0 || $refCount > 0 || $matCount > 0))
                                <div class="flex items-center gap-1.5">
                                    @if (count($events) > 0)
                                        <span class="text-[10px] font-medium px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                            {{ count($events) }} event{{ count($events) !== 1 ? 's' : '' }}
                                        </span>
                                    @endif
                                    @if ($refCount > 0)
                                        <span class="text-[10px] font-medium px-2 py-0.5 rounded-full bg-violet-50 text-violet-700 border border-violet-200">
                                            {{ $refCount }} ref{{ $refCount !== 1 ? 's' : '' }}
                                        </span>
                                    @endif
                                    @if ($matCount > 0)
                                        <span class="text-[10px] font-medium px-2 py-0.5 rounded-full bg-sky-50 text-sky-700 border border-sky-200">
                                            {{ $matCount }} material{{ $matCount !== 1 ? 's' : '' }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                            <i class="bx text-slate-400 text-lg transition-transform duration-200"
                                :class="openWeek === {{ $week->week_no }} ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
                        </div>
                    </button>

                    {{-- Accordion Body --}}
                    <div x-show="openWeek === {{ $week->week_no }}" x-cloak
                        class="px-5 pb-5 pt-1 {{ $isLocked ? 'bg-red-50/30' : 'bg-white' }}">

                        {{-- ── LOCKED BANNER ───────────────────────────────── --}}
                        @if ($isLocked)
                            <div class="mb-4 flex items-start gap-3 rounded-lg border border-red-200
                                        bg-red-50 px-4 py-3 text-sm text-red-700">
                                <i class="bx bx-lock-alt text-xl shrink-0 mt-0.5"></i>
                                <div>
                                    <div class="font-semibold">{{ $lockLabel }}</div>
                                    <div class="mt-0.5 text-xs text-red-600">
                                        This week contains a
                                        <strong>{{ $lockType === 'exam' ? 'scheduled exam' : 'non-teaching class' }}</strong>
                                        in the academic calendar. Coverage details cannot be entered for this week.
                                    </div>
                                    {{-- Show which events triggered the lock --}}
                                    @if (count($events) > 0)
                                        <ul class="mt-2 space-y-0.5 text-xs text-red-600">
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
                                </div>
                            </div>

                            {{-- Show other (non-locking) events if any --}}
                            @php $otherEvents = array_filter($events, fn ($ev) => ! in_array($ev['type'], ['exam', 'non_teaching'])); @endphp
                            @if (count($otherEvents) > 0)
                                <div class="mb-3 rounded-lg border border-slate-200 bg-white p-3 text-xs">
                                    <div class="font-semibold text-slate-600 mb-1">Other events this week</div>
                                    <ul class="space-y-1">
                                        @foreach ($otherEvents as $ev)
                                            <li class="flex items-center gap-1.5 text-slate-600">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 shrink-0"></span>
                                                {{ $ev['name'] }} <span class="text-slate-400">({{ $ev['date_display'] }})</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                        {{-- ── EDITABLE BODY ───────────────────────────────── --}}
                        @else

                            {{-- Events row (non-locking events only shown here since locked weeks show their own block) --}}
                            @if (count($events) > 0)
                                <div class="mb-4 rounded-lg border border-slate-200 bg-slate-50 p-3">
                                    <div class="text-xs font-semibold text-slate-600 mb-1.5">
                                        <i class="bx bx-calendar-event"></i> Events this week
                                    </div>
                                    <ul class="space-y-1">
                                        @foreach ($events as $ev)
                                            @php
                                                $evColor = match($ev['type']) {
                                                    'holiday' => 'bg-green-400',
                                                    'break'   => 'bg-blue-400',
                                                    'other'   => 'bg-slate-400',
                                                    default   => 'bg-amber-400',
                                                };
                                            @endphp
                                            <li class="flex items-center gap-1.5 text-xs text-slate-600">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $evColor }} shrink-0"></span>
                                                <span class="font-medium">{{ $ev['name'] }}</span>
                                                <span class="text-slate-400">({{ $ev['date_display'] }})</span>
                                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 capitalize">
                                                    {{ str_replace('_', ' ', $ev['type']) }}
                                                </span>
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
                                        <span class="text-xs font-semibold text-slate-700">
                                            <i class="bx bx-book-open text-slate-500 mr-0.5"></i> References
                                        </span>
                                        <button type="button"
                                            wire:click="addReference({{ $week->week_no }})"
                                            class="inline-flex items-center gap-1 px-2 py-1 text-[11px] font-semibold rounded-md
                                                    border border-violet-200 bg-white text-violet-700
                                                    hover:bg-violet-50 hover:border-violet-300 transition-colors">
                                            <i class="bx bx-plus text-sm leading-none"></i> Add
                                        </button>
                                    </div>
                                    <div class="space-y-2">
                                        @foreach ($weekInputs[$wKey]['references'] ?? [['text' => '']] as $rIdx => $ref)
                                            <div class="flex items-center gap-2" wire:key="ref-{{ $wKey }}-{{ $rIdx }}">
                                                <input type="text"
                                                    wire:model.lazy="weekInputs.{{ $wKey }}.references.{{ $rIdx }}.text"
                                                    placeholder="e.g. Author (Year). Title. Publisher."
                                                    class="flex-1 text-sm rounded-lg border border-slate-300 bg-white px-3 py-1.5
                                                            focus:border-violet-400 focus:ring-1 focus:ring-violet-300 focus:outline-none
                                                            placeholder:text-slate-300">
                                                @if (count($weekInputs[$wKey]['references'] ?? []) > 1)
                                                    <button type="button"
                                                        wire:click="removeReference({{ $week->week_no }}, {{ $rIdx }})"
                                                        class="shrink-0 p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-md transition-colors"
                                                        title="Remove">
                                                        <i class="bx bx-trash text-sm leading-none"></i>
                                                    </button>
                                                @else
                                                    <span class="w-7.5 shrink-0"></span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Online Materials --}}
                                <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                                    <div class="flex items-center justify-between mb-2.5">
                                        <span class="text-xs font-semibold text-slate-700">
                                            <i class="bx bx-link text-slate-500 mr-0.5"></i> Online Materials
                                        </span>
                                        <button type="button"
                                            wire:click="addMaterial({{ $week->week_no }})"
                                            class="inline-flex items-center gap-1 px-2 py-1 text-[11px] font-semibold rounded-md
                                                    border border-sky-200 bg-white text-sky-700
                                                    hover:bg-sky-50 hover:border-sky-300 transition-colors">
                                            <i class="bx bx-plus text-sm leading-none"></i> Add
                                        </button>
                                    </div>
                                    <div class="space-y-3">
                                        @foreach ($weekInputs[$wKey]['materials'] ?? [['name' => '', 'url' => '']] as $mIdx => $mat)
                                            <div class="flex items-start gap-2" wire:key="mat-{{ $wKey }}-{{ $mIdx }}">
                                                <div class="flex-1 space-y-1.5">
                                                    <input type="text"
                                                        wire:model.lazy="weekInputs.{{ $wKey }}.materials.{{ $mIdx }}.name"
                                                        placeholder="Name (e.g. Week {{ $week->week_no }} Slides)"
                                                        class="w-full text-sm rounded-lg border border-slate-300 bg-white px-3 py-1.5
                                                                focus:border-sky-400 focus:ring-1 focus:ring-sky-300 focus:outline-none
                                                                placeholder:text-slate-300">
                                                    <input type="url"
                                                        wire:model.lazy="weekInputs.{{ $wKey }}.materials.{{ $mIdx }}.url"
                                                        placeholder="https://…"
                                                        class="w-full text-sm rounded-lg border border-slate-300 bg-white px-3 py-1.5
                                                                focus:border-sky-400 focus:ring-1 focus:ring-sky-300 focus:outline-none
                                                                placeholder:text-slate-300">
                                                </div>
                                                @if (count($weekInputs[$wKey]['materials'] ?? []) > 1)
                                                    <button type="button"
                                                        wire:click="removeMaterial({{ $week->week_no }}, {{ $mIdx }})"
                                                        class="shrink-0 mt-1 p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-md transition-colors"
                                                        title="Remove">
                                                        <i class="bx bx-trash text-sm leading-none"></i>
                                                    </button>
                                                @else
                                                    <span class="w-7.5 shrink-0 mt-1"></span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- Per-week save --}}
                            <div class="flex items-center justify-between mt-5 pt-3 border-t border-slate-100">
                                <p class="text-xs text-slate-400">
                                    <i class="bx bx-info-circle"></i>
                                    Auto-saves when you collapse this week or use Save All.
                                </p>
                                <button type="button"
                                    wire:click="saveWeek({{ $week->week_no }})"
                                    wire:loading.attr="disabled"
                                    wire:target="saveWeek({{ $week->week_no }})"
                                    class="inline-flex items-center gap-1.5 px-4 py-1.5 text-xs font-semibold rounded-lg
                                            border border-green-300 bg-green-50 text-green-700 hover:bg-green-100
                                            disabled:opacity-60 disabled:cursor-not-allowed transition-colors">
                                    <span wire:loading.remove wire:target="saveWeek({{ $week->week_no }})">
                                        <i class="bx bx-save"></i> Save Week {{ $week->week_no }}
                                    </span>
                                    <span wire:loading wire:target="saveWeek({{ $week->week_no }})">
                                        <i class="bx bx-loader-alt bx-spin"></i> Saving…
                                    </span>
                                </button>
                            </div>

                        @endif {{-- end locked / editable --}}
                    </div>{{-- /body --}}
                </div>{{-- /wire:key --}}
            @endforeach
        </div>{{-- /accordion --}}

        <p class="text-xs text-slate-400 mt-5">
            <i class="bx bx-bulb"></i>
            Use <strong>Save Week N</strong> per section or <strong>Save All</strong> to persist everything at once.
            Navigating steps (Next / Previous) also auto-saves.
        </p>
    @endif
</div>
