<div>
    <div class="mb-4 grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- LEFT: Title + Generate --}}
        <div>
            <h3 class="text-xl font-semibold text-slate-900">Weekly Coverage</h3>
            <p class="text-sm text-slate-600">
                Weeks are generated from the academic calendar. Events and dates are shown per week.
            </p>

            <div class="mt-2 flex items-center gap-2 flex-wrap">
                @if (! $weeksGenerated)
                    <button type="button" wire:click="generateWeeklyCoverage"
                        @if (! $academic_calendar_id) disabled @endif
                        wire:loading.attr="disabled"
                        wire:target="generateWeeklyCoverage"
                        class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold rounded
                               border border-emerald-300 bg-emerald-50 text-emerald-700 hover:bg-emerald-100
                               disabled:bg-slate-100 disabled:text-slate-400 disabled:border-slate-200 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="generateWeeklyCoverage">
                            <i class="bx bx-refresh"></i> Generate Weeks
                        </span>
                        <span wire:loading wire:target="generateWeeklyCoverage">
                            <i class="bx bx-loader-alt bx-spin"></i> Generating...
                        </span>
                    </button>
                @else
                    <button type="button" wire:click="regenerateWeeks"
                        wire:loading.attr="disabled"
                        wire:target="regenerateWeeks"
                        wire:confirm="This will delete all existing weeks and recreate them. All content will be lost. Continue?"
                        class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold rounded
                               border border-amber-300 bg-amber-50 text-amber-700 hover:bg-amber-100
                               disabled:opacity-60 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="regenerateWeeks">
                            <i class="bx bx-refresh"></i> Regenerate Weeks
                        </span>
                        <span wire:loading wire:target="regenerateWeeks">
                            <i class="bx bx-loader-alt bx-spin"></i> Regenerating...
                        </span>
                    </button>
                @endif

                @if ($weeksGenerated)
                    <button type="button" wire:click="saveAllWeeklyEntries"
                        wire:loading.attr="disabled"
                        wire:target="saveAllWeeklyEntries"
                        class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold rounded
                               border border-blue-300 bg-blue-50 text-blue-700 hover:bg-blue-100
                               disabled:opacity-60 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="saveAllWeeklyEntries">
                            <i class="bx bx-save"></i> Save All
                        </span>
                        <span wire:loading wire:target="saveAllWeeklyEntries">
                            <i class="bx bx-loader-alt bx-spin"></i> Saving...
                        </span>
                    </button>
                @endif
            </div>
        </div>

        {{-- RIGHT: Calendar Summary --}}
        <div class="text-xs text-slate-600 bg-slate-50 border border-slate-200 rounded-lg p-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- LEFT: Class Schedule --}}
                @if ($courseComponents)
                    <div>
                        <div class="font-semibold text-slate-700 mb-3">Class Schedule</div>

                        @php
                            $hasLEC      = isset($courseComponents['LEC']);
                            $hasLAB      = isset($courseComponents['LAB']);
                            $columnClass = ($hasLEC && $hasLAB) ? 'md:grid-cols-2' : 'md:grid-cols-1';
                        @endphp

                        <div class="grid grid-cols-1 {{ $columnClass }} gap-4">
                            @if ($hasLEC)
                                <div class="space-y-1">
                                    <div class="font-semibold text-emerald-700">Lecture (LEC)</div>
                                    <div class="flex justify-between">
                                        {{-- <span class="text-slate-500">Schedule</span> --}}
                                        <span>{{ $courseComponents['LEC']['schedule'] ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex">
                                        <span>{{ $courseComponents['LEC']['class_hours'] ?? '-' }}</span>
                                        <span class="text-slate-500"> Hours</span>
                                    </div>
                                </div>
                            @endif

                            @if ($hasLAB)
                                <div class="space-y-1">
                                    <div class="font-semibold text-blue-700">Laboratory (LAB)</div>
                                    <div class="flex justify-between">
                                        {{-- <span class="text-slate-500">Schedule</span> --}}
                                        <span>{{ $courseComponents['LAB']['schedule'] ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex">
                                        <span>{{ $courseComponents['LAB']['class_hours'] ?? '-' }}</span>
                                        <span class="text-slate-500"> Hours</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif


                {{-- RIGHT: Calendar Info --}}
                <div class="space-y-4">
                    <div>
                        <div class="font-semibold text-slate-700">Calendar</div>
                        <div class="mt-1">
                            @if ($syllabus?->academicCalendar)
                                {{ \Carbon\Carbon::parse($syllabus->academicCalendar->start_date)->format('M d, Y') }}
                                –
                                {{ \Carbon\Carbon::parse($syllabus->academicCalendar->end_date)->format('M d, Y') }}
                            @else
                                <span class="text-slate-400 italic">Not set</span>
                            @endif
                        </div>
                    </div>

                    <div class="border-t border-slate-200 pt-3">
                        <div><span class="font-semibold">{{ $syllabusWeeks->count() }}</span> weeks</div>
                        <div><span class="font-semibold">{{ collect($weekEvents)->flatten(1)->count() }}</span> events</div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ── Empty State ──────────────────────────────────────────────────────── --}}
    @if ($syllabusWeeks->isEmpty())
        <div class="text-center py-12 bg-slate-50 rounded-lg border-2 border-dashed border-slate-300">
            <p class="text-slate-500 text-sm">
                No weeks generated yet. Please select an academic calendar with start/end dates first.
            </p>
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
            <div class="mb-4">
                <div class="flex items-center gap-1 p-1 bg-slate-100 rounded-lg w-fit">

                    {{-- LEC tab --}}
                    <button type="button"
                        wire:click="setComponentType('LEC')"
                        wire:loading.attr="disabled"
                        wire:target="setComponentType"
                        @class([
                            'relative flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-md transition-all duration-150',
                            'bg-white text-emerald-700 shadow-sm ring-1 ring-slate-200' => $activeComponent === 'LEC',
                            'text-slate-500 hover:text-slate-700 hover:bg-white/60' => $activeComponent !== 'LEC',
                            'opacity-60 cursor-not-allowed' => $activeComponent !== 'LEC', // show during load
                        ])>
                        {{-- Spinner shown on THIS tab while switching TO it --}}
                        <span wire:loading wire:target="setComponentType('LEC')" class="flex items-center gap-1.5">
                            <i class="bx bx-loader-alt bx-spin text-emerald-600"></i>
                            <span>Switching…</span>
                        </span>
                        <span wire:loading.remove wire:target="setComponentType('LEC')" class="flex items-center gap-1.5">
                            <span class="inline-block w-2 h-2 rounded-full
                                {{ $activeComponent === 'LEC' ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
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
                        wire:loading.attr="disabled"
                        wire:target="setComponentType"
                        @class([
                            'relative flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-md transition-all duration-150',
                            'bg-white text-blue-700 shadow-sm ring-1 ring-slate-200' => $activeComponent === 'LAB',
                            'text-slate-500 hover:text-slate-700 hover:bg-white/60' => $activeComponent !== 'LAB',
                        ])>
                        <span wire:loading wire:target="setComponentType('LAB')" class="flex items-center gap-1.5">
                            <i class="bx bx-loader-alt bx-spin text-blue-600"></i>
                            <span>Switching…</span>
                        </span>
                        <span wire:loading.remove wire:target="setComponentType('LAB')" class="flex items-center gap-1.5">
                            <span class="inline-block w-2 h-2 rounded-full
                                {{ $activeComponent === 'LAB' ? 'bg-blue-500' : 'bg-slate-300' }}"></span>
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
                     class="mt-2 flex items-center gap-2 text-xs text-slate-500">
                    <i class="bx bx-loader-alt bx-spin"></i>
                    Saving current data and loading {{ $activeComponent === 'LEC' ? 'Laboratory' : 'Lecture' }} content…
                </div>
            </div>

        {{-- Single component — no switcher needed, just a label --}}
        @elseif ($hasLEC || $hasLAB)
            <div class="mb-4 flex items-center gap-2">
                <span class="px-3 py-1.5 text-xs font-semibold rounded-md
                    {{ $hasLEC ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                               : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
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
             class="border border-slate-200 rounded-xl bg-white divide-y divide-slate-200">

            @foreach ($syllabusWeeks as $week)
                @php
                    $wKey   = 'w' . $week->week_no;
                    $start  = \Carbon\Carbon::parse($week->start_date);
                    $end    = \Carbon\Carbon::parse($week->end_date);
                    $events = $weekEvents[$week->week_no] ?? [];

                    // Current saved values for this week — used for header preview
                    $savedTopic = $weekInputs[$wKey]['topic'] ?? '';
                @endphp

                <div wire:key="week-{{ $week->week_no }}-{{ $activeComponent }}">

                    {{-- Accordion Header --}}
                    <button type="button"
                        @click="openWeek = openWeek === {{ $week->week_no }} ? null : {{ $week->week_no }}"
                        class="w-full flex items-center justify-between px-4 py-3 text-left
                               hover:bg-slate-50 transition-colors focus:outline-none">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold shrink-0
                                         {{ $week->is_exam_week ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $week->week_no }}
                            </span>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-semibold text-sm text-slate-800">Week {{ $week->week_no }}</span>
                                    <span class="text-xs text-slate-400">
                                        {{ $start->format('M d') }} – {{ $end->format('M d, Y') }}
                                    </span>
                                    @if ($week->exam_type)
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700 border border-red-200">
                                            {{ str_replace('_', ' ', ucfirst($week->exam_type)) }}
                                        </span>
                                    @endif
                                    @if ($savedTopic)
                                        <span class="text-xs text-slate-400 truncate max-w-xs hidden sm:inline">
                                            — {{ \Illuminate\Support\Str::limit($savedTopic, 60) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            @if (count($events) > 0)
                                <span class="text-xs bg-amber-50 border border-amber-200 text-amber-700
                                            rounded-full px-2 py-0.5 hidden sm:inline">
                                    {{ count($events) }} event{{ count($events) > 1 ? 's' : '' }}
                                </span>
                            @endif
                        </div>
                        <i class="bx text-slate-400 transition-transform duration-200 shrink-0 ml-2"
                           :class="openWeek === {{ $week->week_no }} ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
                    </button>

                    {{-- Accordion Body --}}
                    <div x-show="openWeek === {{ $week->week_no }}" x-cloak class="px-4 pb-4 space-y-4">
                        <div class="bg-white border border-slate-200 rounded-lg p-4 space-y-4">

                            {{-- Events + Exam assignment --}}
                            <div class="bg-slate-50 border border-slate-200 rounded-lg p-3 space-y-4">

                                {{-- Events --}}
                                <div>
                                    <div class="text-xs font-semibold text-slate-600 mb-1">Events this week</div>
                                    @if (empty($events))
                                        <div class="text-xs text-slate-400 italic">No events</div>
                                    @else
                                        <ul class="space-y-1 text-xs text-slate-700">
                                            @foreach ($events as $event)
                                                <li class="flex items-center gap-1.5">
                                                    <span class="w-1 h-1 rounded-full bg-slate-400 shrink-0"></span>
                                                    <span class="font-medium">{{ $event['name'] }}</span>
                                                    <span class="text-slate-400">({{ $event['date_display'] }})</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>

                                {{-- Exam week assignment --}}
                                <div>
                                    <div class="text-xs font-semibold text-slate-600 mb-2">Mark as Exam Week</div>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach (['first_term' => '1st Term', 'second_term' => '2nd Term', 'final_term' => 'Final Term'] as $type => $label)
                                            @php
                                                $assignedWeek        = $examWeeks[$type] ?? null;
                                                $isAssignedHere      = $assignedWeek === $week->week_no;
                                                $isAssignedElsewhere = $assignedWeek !== null && ! $isAssignedHere;
                                            @endphp
                                            @if ($isAssignedHere)
                                                <button type="button" wire:click="clearExamWeek('{{ $type }}')"
                                                    class="px-3 py-1 text-xs font-medium rounded bg-red-50 text-red-700 border border-red-200 hover:bg-red-100">
                                                    ✕ Remove {{ $label }} Exam
                                                </button>
                                            @else
                                                <button type="button"
                                                    wire:click="assignExamWeek('{{ $type }}', {{ $week->week_no }})"
                                                    @if ($isAssignedElsewhere) disabled @endif
                                                    class="px-3 py-1 text-xs font-medium rounded border border-blue-200
                                                           {{ $isAssignedElsewhere ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}">
                                                    Set {{ $label }} Exam
                                                </button>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>

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
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Course Outcome --}}
                                <div class="md:col-span-2">
                                <x-form.label for="course_outcome_{{ $wKey }}">Course Outcome</x-form.label>
                                <x-form.select
                                    id="course_outcome_{{ $wKey }}"
                                        wire:model.lazy="weekInputs.{{ $wKey }}.course_outcome_id"
                                >
                                        <option value="">— Select Course Outcome —</option>
                                        @foreach ($courseOutcomes as $outcome)
                                            <option value="{{ $outcome['id'] }}">
                                                {{ $outcome['co_code'] }} – {{ \Illuminate\Support\Str::limit($outcome['description'], 60) }}
                                            </option>
                                        @endforeach
                                </x-form.select>
                                </div>

                            {{-- Unit Learning Outcomes --}}
                                <div>
                                <x-form.label for="learning_outcomes_{{ $wKey }}">Unit Learning Outcomes</x-form.label>
                                <x-form.textarea
                                    id="learning_outcomes_{{ $wKey }}"
                                    rows="4"
                                    placeholder="Enter learning outcomes…"
                                        wire:model.lazy="weekInputs.{{ $wKey }}.learning_outcomes"
                                />
                                </div>

                            {{-- Assessment Task --}}
                                <div>
                                <x-form.label for="assessment_task_{{ $wKey }}">Assessment Task</x-form.label>
                                <x-form.textarea
                                    id="assessment_task_{{ $wKey }}"
                                    rows="4"
                                    placeholder="Enter assessment task…"
                                        wire:model.lazy="weekInputs.{{ $wKey }}.assessment_task"
                                />
                                </div>

                            {{-- Topics --}}
                                <div>
                                <x-form.label for="topics_{{ $wKey }}">Topics</x-form.label>
                                <x-form.textarea
                                    id="topics_{{ $wKey }}"
                                    rows="4"
                                    placeholder="Enter topics covered…"
                                        wire:model.lazy="weekInputs.{{ $wKey }}.topic"
                                />
                                </div>

                            {{-- Teaching & Learning Activities --}}
                                <div>
                                <x-form.label for="tla_{{ $wKey }}">Teaching &amp; Learning Activities</x-form.label>
                                <x-form.textarea
                                    id="tla_{{ $wKey }}"
                                    rows="4"
                                    placeholder="Enter teaching activities…"
                                        wire:model.lazy="weekInputs.{{ $wKey }}.teaching_activities"
                                />
                                </div>
                            </div>

                        <hr class="border-slate-100 my-4">

                            <div class="flex flex-wrap gap-4">
                            {{-- References --}}
                                <div class="w-full md:w-[48%]">
                                <x-form.label for="reference_{{ $wKey }}">References</x-form.label>
                                <x-form.input
                                    id="reference_{{ $wKey }}"
                                    type="text"
                                    placeholder="Book / chapter / article…"
                                        wire:model.lazy="weekInputs.{{ $wKey }}.reference_text"
                                />
                                </div>

                            {{-- Online Materials --}}
                                <div class="w-full md:w-[48%]">
                                <x-form.label for="material_name_{{ $wKey }}">Online Material Name</x-form.label>
                                <x-form.input
                                    id="material_name_{{ $wKey }}"
                                    type="text"
                                    placeholder="Lecture slides, video, etc."
                                        wire:model.lazy="weekInputs.{{ $wKey }}.material_name"
                                />

                                <x-form.label for="material_url_{{ $wKey }}" class="mt-3">Online Material URL</x-form.label>
                                <x-form.input
                                    id="material_url_{{ $wKey }}"
                                    type="url"
                                    placeholder="https://…"
                                        wire:model.lazy="weekInputs.{{ $wKey }}.material_url"
                                />
                                </div>
                            </div>

                            {{-- Per-week save button --}}
                            <div class="flex justify-end pt-2 border-t border-slate-100">
                                <button type="button"
                                    wire:click="saveWeek({{ $week->week_no }})"
                                    wire:loading.attr="disabled"
                                    wire:target="saveWeek({{ $week->week_no }})"
                                    class="inline-flex items-center gap-2 px-4 py-1.5 text-xs font-semibold rounded
                                            border border-green-300 bg-green-50 text-green-700 hover:bg-green-100
                                           disabled:opacity-60 disabled:cursor-not-allowed">
                                    <span wire:loading.remove wire:target="saveWeek({{ $week->week_no }})">
                                        <i class="bx bx-save"></i> Save Week {{ $week->week_no }}
                                    </span>
                                    <span wire:loading wire:target="saveWeek({{ $week->week_no }})">
                                        <i class="bx bx-loader-alt bx-spin"></i> Saving...
                                    </span>
                                </button>
                            </div>

                        </div>{{-- /card --}}
                    </div>{{-- /x-show --}}
                </div>{{-- /wire:key --}}
            @endforeach
        </div>

        <p class="text-xs text-slate-400 mt-5">
            <i class="bx bx-bulb"></i> Use <strong>Save Week N</strong> per section or <strong>Save All</strong> to persist everything at once.
            Navigating to another step (Next / Previous) also auto-saves.
        </p>{{-- /accordion --}}
    @endif
</div>
