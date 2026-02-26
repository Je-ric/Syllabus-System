<div>
    <div class="mb-4 grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- LEFT: Title + Generate --}}
        <div>
            <h3 class="text-xl font-semibold text-slate-900">Weekly Coverage</h3>
            <p class="text-sm text-slate-600">
                Weeks are generated from the academic calendar. Events and dates are shown per week.
            </p>

            <div class="mt-2 flex items-center gap-2 flex-wrap">
                {{--
                    Generate button:
                    - Enabled only when: calendar is set AND weeks not yet generated
                    - Original disables when $weeksGenerated — now we show Regenerate instead
                --}}
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

        {{-- CENTER: Class Schedule --}}
        @if ($courseComponents)
            <div class="bg-slate-50 border border-slate-200 rounded-lg p-3 text-xs">
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
                                <span class="text-slate-500">Schedule</span>
                                <span class="text-slate-800 text-right">{{ $courseComponents['LEC']['schedule'] ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Hours</span>
                                <span class="text-slate-800">{{ $courseComponents['LEC']['class_hours'] ?? '-' }}</span>
                            </div>
                        </div>
                    @endif
                    @if ($hasLAB)
                        <div class="space-y-1">
                            <div class="font-semibold text-blue-700">Laboratory (LAB)</div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Schedule</span>
                                <span class="text-slate-800 text-right">{{ $courseComponents['LAB']['schedule'] ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Hours</span>
                                <span class="text-slate-800">{{ $courseComponents['LAB']['class_hours'] ?? '-' }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- RIGHT: Calendar Summary --}}
        <div class="text-xs text-slate-600 bg-slate-50 border border-slate-200 rounded-lg px-3 py-3">
            <div>
                <span class="font-semibold text-slate-700">Calendar:</span>
                <div class="mt-1">
                    @if ($syllabus?->academicCalendar)
                        {{ \Carbon\Carbon::parse($syllabus->academicCalendar->start_date)->format('M d, Y') }}
                        –
                        {{ \Carbon\Carbon::parse($syllabus->academicCalendar->end_date)->format('M d, Y') }}
                    @else
                        Not set
                    @endif
                </div>
            </div>
            <div class="flex justify-between mt-3 text-slate-700">
                <div><span class="font-semibold">{{ $syllabusWeeks->count() }}</span> weeks</div>
                <div><span class="font-semibold">{{ collect($weekEvents)->flatten(1)->count() }}</span> events</div>
            </div>
        </div>

    </div>

    {{-- ── Empty State ────────────────────────────────────────────────────── --}}
    @if ($syllabusWeeks->isEmpty())
        <div class="text-center py-12 bg-slate-50 rounded-lg border-2 border-dashed border-slate-300">
            <p class="text-slate-500 text-sm">
                No weeks generated yet. Please select an academic calendar with start/end dates first.
            </p>
        </div>

    @else

        @php $hasLEC = isset($courseComponents['LEC']); $hasLAB = isset($courseComponents['LAB']); @endphp

        {{-- LEC / LAB switcher --}}
        @if ($hasLEC || $hasLAB)
            <div class="mb-3 border-b border-slate-200">
                <nav class="flex gap-2 text-xs">
                    @if ($hasLEC)
                        <button type="button" wire:click="setComponentType('LEC')"
                            class="px-3 py-1.5 rounded-t-md border-b-2
                                {{ $activeComponent === 'LEC'
                                    ? 'border-emerald-500 bg-emerald-50 text-emerald-700 font-semibold'
                                    : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50' }}">
                            Lecture
                        </button>
                    @endif
                    @if ($hasLAB)
                        <button type="button" wire:click="setComponentType('LAB')"
                            class="px-3 py-1.5 rounded-t-md border-b-2
                                {{ $activeComponent === 'LAB'
                                    ? 'border-blue-500 bg-blue-50 text-blue-700 font-semibold'
                                    : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50' }}">
                            Laboratory
                        </button>
                    @endif
                </nav>
            </div>
        @endif

        {{-- ── Accordion ──────────────────────────────────────────────────── --}}
        {{--
            Alpine watches openWeek changes. When the user collapses a week
            (oldVal → newVal), saveWeek(oldVal) fires — same as original.
            This means data is committed each time the user moves to another week.
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
                    $start  = \Carbon\Carbon::parse($week->start_date);
                    $end    = \Carbon\Carbon::parse($week->end_date);
                    $events = $weekEvents[$week->week_no] ?? collect();
                @endphp

                <div wire:key="week-{{ $week->week_no }}">

                    {{-- Header --}}
                    <button type="button"
                        @click="openWeek = openWeek === {{ $week->week_no }} ? null : {{ $week->week_no }}"
                        class="w-full flex items-center justify-between px-4 py-3 text-left
                               hover:bg-slate-50 transition-colors focus:outline-none">
                        <div class="flex items-center gap-3">
                            <span class="font-semibold text-sm text-slate-800">Week {{ $week->week_no }}</span>
                            <span class="text-xs text-slate-500">
                                {{ $start->format('M d') }} &ndash; {{ $end->format('M d, Y') }}
                            </span>
                            @if ($week->exam_type)
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                             bg-red-100 text-red-700 border border-red-200">
                                    {{ str_replace('_', ' ', ucfirst($week->exam_type)) }}
                                </span>
                            @endif
                        </div>
                        <i class="bx text-slate-400 transition-transform duration-200"
                           :class="openWeek === {{ $week->week_no }} ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
                    </button>

                    {{-- Body --}}
                    <div x-show="openWeek === {{ $week->week_no }}" x-cloak class="px-4 pb-4 space-y-4">
                        <div class="bg-white border border-slate-200 rounded-lg p-4 space-y-4">

                            {{-- Events + Exam assignment --}}
                            <div class="bg-slate-50 border border-slate-200 rounded-lg p-3 space-y-4">

                                {{-- Events --}}
                                <div>
                                    <div class="text-xs font-semibold text-slate-600 mb-1">Events</div>
                                    @if ($events->isEmpty())
                                        <div class="text-xs text-slate-500">No events this week</div>
                                    @else
                                        <ul class="space-y-1 text-xs text-slate-700">
                                            @foreach ($events as $event)
                                                <li>
                                                    <span class="font-medium">{{ $event->name }}</span>
                                                    <span class="text-slate-500">
                                                        ({{ \Carbon\Carbon::parse($event->date)->format('M d') }})
                                                    </span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>

                                {{-- Exam week assignment --}}
                                <div>
                                    <div class="text-xs font-semibold text-slate-600 mb-2">Exam Weeks</div>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach (['first_term' => '1st Term', 'second_term' => '2nd Term', 'final_term' => 'Final Term'] as $type => $label)
                                            @php
                                                $assignedWeek      = $examWeeks[$type] ?? null;
                                                $isAssignedHere    = $assignedWeek === $week->week_no;
                                                $isAssignedElsewhere = $assignedWeek !== null && ! $isAssignedHere;
                                            @endphp
                                            @if ($isAssignedHere)
                                                <button type="button" wire:click="clearExamWeek('{{ $type }}')"
                                                    class="px-3 py-1 text-xs font-medium rounded
                                                           bg-red-50 text-red-700 border border-red-200 hover:bg-red-100">
                                                    Remove {{ $label }} Exam
                                                </button>
                                            @else
                                                <button type="button"
                                                    wire:click="assignExamWeek('{{ $type }}', {{ $week->week_no }})"
                                                    @if ($isAssignedElsewhere) disabled @endif
                                                    class="px-3 py-1 text-xs font-medium rounded border border-blue-200
                                                           {{ $isAssignedElsewhere
                                                               ? 'bg-slate-100 text-slate-400 cursor-not-allowed'
                                                               : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}">
                                                    Set {{ $label }} Exam
                                                </button>
                                            @endif
                                        @endforeach
                                    </div>
                                    @if ($week->exam_type)
                                        <div class="mt-2 text-xs font-semibold text-red-700">
                                            Exam Week: {{ str_replace('_', ' ', ucfirst($week->exam_type)) }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{--
                                FORM FIELDS
                                wire:model.lazy — syncs on blur (exactly like original).
                                Keys: weekInputs.{week_no}.field
                                {week_no} renders as an integer string e.g. "1", "2".
                                After JSON round-trip Livewire stores string keys in $weekInputs.
                                populateWeekInputs() also writes string keys via (string) cast
                                so the initial render and subsequent reads are consistent.
                            --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Course Outcomes</label>
                                    <select class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                        wire:model.lazy="weekInputs.{{ $week->week_no }}.course_outcome_id">
                                        <option value="">Select Course Outcome</option>
                                        @foreach ($courseOutcomes as $outcome)
                                            <option value="{{ $outcome['id'] }}">
                                                {{ $outcome['co_code'] }} -
                                                {{ \Illuminate\Support\Str::limit($outcome['description'], 50) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit Learning Outcomes</label>
                                    <textarea rows="3"
                                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                        wire:model.lazy="weekInputs.{{ $week->week_no }}.learning_outcomes"></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Assessment Task</label>
                                    <textarea rows="3"
                                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                        wire:model.lazy="weekInputs.{{ $week->week_no }}.assessment_task"></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Topics</label>
                                    <textarea rows="3"
                                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                        wire:model.lazy="weekInputs.{{ $week->week_no }}.topic"></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Teaching and Learning Activities
                                    </label>
                                    <textarea rows="3"
                                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                        wire:model.lazy="weekInputs.{{ $week->week_no }}.teaching_activities"></textarea>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="flex flex-wrap gap-4">
                                <div class="w-full md:w-[48%]">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">References</label>
                                    <input type="text"
                                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                        wire:model.lazy="weekInputs.{{ $week->week_no }}.reference_text">
                                </div>

                                <div class="w-full md:w-[48%]">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Online Material Name</label>
                                    <input type="text"
                                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                        wire:model.lazy="weekInputs.{{ $week->week_no }}.material_name">

                                    <label class="block text-sm font-medium text-gray-700 mt-3 mb-1">Online Material URL</label>
                                    <input type="text"
                                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                        wire:model.lazy="weekInputs.{{ $week->week_no }}.material_url">
                                </div>
                            </div>

                            {{-- Per-week save --}}
                            <div class="flex justify-end pt-2 border-t border-slate-100">
                                <button type="button"
                                    wire:click="saveWeek({{ $week->week_no }})"
                                    wire:loading.attr="disabled"
                                    wire:target="saveWeek({{ $week->week_no }})"
                                    class="inline-flex items-center gap-2 px-4 py-1.5 text-xs font-semibold rounded
                                           border border-indigo-300 bg-indigo-50 text-indigo-700 hover:bg-indigo-100
                                           disabled:opacity-60 disabled:cursor-not-allowed">
                                    <span wire:loading.remove wire:target="saveWeek({{ $week->week_no }})">
                                        <i class="bx bx-save"></i> Save Week {{ $week->week_no }}
                                    </span>
                                    <span wire:loading wire:target="saveWeek({{ $week->week_no }})">
                                        <i class="bx bx-loader-alt bx-spin"></i> Saving...
                                    </span>
                                </button>
                            </div>

                        </div>
                    </div>

                </div>
            @endforeach
        </div>
    @endif
</div>
