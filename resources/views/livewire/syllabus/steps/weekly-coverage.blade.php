<div>
    <div class="mb-4 grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- LEFT: Title + Generate --}}
        <div>
            <h3 class="text-xl font-semibold text-slate-900">
                Weekly Coverage
            </h3>

            <p class="text-sm text-slate-600">
                Weeks are generated from the academic calendar. Events and dates are shown per week.
            </p>

            <div class="mt-2 flex items-center gap-2">
                <button type="button" wire:click="generateWeeklyCoverage"
                    @if (!$academic_calendar_id || $weeksGenerated) disabled @endif
                    class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold rounded border border-emerald-300 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 disabled:bg-slate-100 disabled:text-slate-400 disabled:border-slate-200 disabled:cursor-not-allowed">

                    <span wire:loading.remove wire:target="generateWeeklyCoverage">
                        <i class="bx bx-refresh"></i>
                        {{ $weeksGenerated ? 'Weeks Generated' : 'Generate Weeks' }}
                    </span>

                    <span wire:loading wire:target="generateWeeklyCoverage">
                        <i class="bx bx-loader-alt bx-spin"></i>
                        Generating...
                    </span>
                </button>

                @if ($weeksGenerated)
                    <button type="button" wire:click="saveAllWeeklyEntries"
                        class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold rounded border border-blue-300 bg-blue-50 text-blue-700 hover:bg-blue-100">
                        <span wire:loading.remove wire:target="saveAllWeeklyEntries">
                            <i class="bx bx-save"></i>
                            Save All
                        </span>
                        <span wire:loading wire:target="saveAllWeeklyEntries">
                            <i class="bx bx-loader-alt bx-spin"></i>
                            Saving...
                        </span>
                    </button>
                @endif

                <span class="text-xs text-slate-500">
                    Generation is manual to keep step switching fast.
                </span>
            </div>
        </div>

        {{-- CENTER: Class Schedule --}}
        @if ($courseComponents)
            <div class="bg-slate-50 border border-slate-200 rounded-lg p-3 text-xs">
                <div class="font-semibold text-slate-700 mb-3">
                    Class Schedule
                </div>

                @php
                    $hasLEC = isset($courseComponents['LEC']);
                    $hasLAB = isset($courseComponents['LAB']);
                    $columnClass = $hasLEC && $hasLAB ? 'md:grid-cols-2' : 'md:grid-cols-1';
                @endphp

                <div class="grid grid-cols-1 {{ $columnClass }} gap-4">

                    {{-- LECTURE --}}
                    @if ($hasLEC)
                        <div class="space-y-1">
                            <div class="font-semibold text-emerald-700">
                                Lecture (LEC)
                            </div>

                            <div class="flex justify-between">
                                <span class="text-slate-500">Schedule</span>
                                <span class="text-slate-800 text-right">
                                    {{ $courseComponents['LEC']['schedule'] ?? 'N/A' }}
                                </span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-slate-500">Hours</span>
                                <span class="text-slate-800">
                                    {{ $courseComponents['LEC']['class_hours'] ?? '-' }}
                                </span>
                            </div>
                        </div>
                    @endif

                    {{-- LAB --}}
                    @if ($hasLAB)
                        <div class="space-y-1">
                            <div class="font-semibold text-blue-700">
                                Laboratory (LAB)
                            </div>

                            <div class="flex justify-between">
                                <span class="text-slate-500">Schedule</span>
                                <span class="text-slate-800 text-right">
                                    {{ $courseComponents['LAB']['schedule'] ?? 'N/A' }}
                                </span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-slate-500">Hours</span>
                                <span class="text-slate-800">
                                    {{ $courseComponents['LAB']['class_hours'] ?? '-' }}
                                </span>
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
                <div>
                    <span class="font-semibold">{{ $syllabusWeeks->count() }}</span> weeks
                </div>
                <div>
                    <span class="font-semibold">{{ collect($weekEvents)->flatten(1)->count() }}</span> events
                </div>
            </div>
        </div>

    </div>

    @if ($syllabusWeeks->isEmpty())
        <div class="text-center py-12 bg-slate-50 rounded-lg border-2 border-dashed">
            <p class="text-slate-500">
                No weeks generated yet. Please select an academic calendar with start/end dates first.
            </p>
        </div>
    @else
        <div x-data="{
            openWeek: {{ $syllabusWeeks->first()?->week_no ?? 1 }},
            init() {
                this.$watch('openWeek', (newVal, oldVal) => {
                    if (oldVal !== null && oldVal !== newVal) {
                        $wire.saveWeek(oldVal);
                    }
                });
            }
        }" class="border border-slate-200 rounded-xl bg-white divide-y divide-slate-200">
            @foreach ($syllabusWeeks as $week)
                @php
                    $start = \Carbon\Carbon::parse($week->start_date);
                    $end = \Carbon\Carbon::parse($week->end_date);
                    $day = $start->copy();
                    $events = $weekEvents[$week->week_no] ?? collect();
                @endphp
                <div>
                    {{-- Accordion Header --}}
                    <button type="button"
                        @click="openWeek = openWeek === {{ $week->week_no }} ? null : {{ $week->week_no }}"
                        class="w-full flex items-center justify-between px-4 py-3 text-left hover:bg-slate-50 transition-colors focus:outline-none">
                        <div class="flex items-center gap-3">
                            <span class="font-semibold text-sm text-slate-800">Week {{ $week->week_no }}</span>
                            <span class="text-xs text-slate-500">
                                {{ $start->format('M d') }} &ndash; {{ $end->format('M d, Y') }}
                            </span>
                            @if ($week->exam_type)
                                <span
                                    class="px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700 border border-red-200">
                                    {{ str_replace('_', ' ', ucfirst($week->exam_type)) }}
                                </span>
                            @endif
                        </div>
                        <i class="bx text-slate-400 transition-transform duration-200"
                            :class="openWeek === {{ $week->week_no }} ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
                    </button>

                    {{-- Accordion Body --}}
                    <div x-show="openWeek === {{ $week->week_no }}" x-cloak class="px-4 pb-4 space-y-4">
                        <div class="bg-white border border-slate-200 rounded-lg p-4 space-y-4">
                            <div class="bg-slate-50 border border-slate-200 rounded-lg p-3">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                    {{-- Events --}}
                                    <div>
                                        <div class="text-xs font-semibold text-slate-600 mb-1">
                                            Events
                                        </div>

                                        @if ($events->isEmpty())
                                            <div class="text-xs text-slate-500">
                                                No events this week
                                            </div>
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

                                </div>

                            </div>

                            <div>
                                <div class="text-xs font-semibold text-slate-600 mb-2">Exam Weeks</div>
                                <div class="flex flex-wrap gap-2">
                                    @php
                                        $types = [
                                            'first_term' => '1st Term',
                                            'second_term' => '2nd Term',
                                            'final_term' => 'Final Term',
                                        ];
                                    @endphp
                                    @foreach ($types as $type => $label)
                                        @php
                                            $assignedWeek = $examWeeks[$type] ?? null;
                                            $isAssignedHere = $assignedWeek === $week->week_no;
                                            $isAssignedElsewhere = $assignedWeek !== null && !$isAssignedHere;
                                        @endphp
                                        @if ($isAssignedHere)
                                            <button type="button" wire:click="clearExamWeek('{{ $type }}')"
                                                class="px-3 py-1 text-xs font-medium rounded bg-red-50 text-red-700 border border-red-200 hover:bg-red-100">
                                                Remove {{ $label }} Exam
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
                                @if ($week->exam_type)
                                    <div class="mt-2 text-xs font-semibold text-red-700">
                                        Exam Week: {{ str_replace('_', ' ', ucfirst($week->exam_type)) }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <x-form.label>Course Outcomes</x-form.label>
                                <x-form.select wire:model="weekInputs.{{ $week->week_no }}.course_outcome_id">
                                    <option value="">Select Course Outcome</option>
                                    @foreach ($courseOutcomes as $outcome)
                                        <option value="{{ $outcome['id'] }}">{{ $outcome['co_code'] }} -
                                            {{ \Illuminate\Support\Str::limit($outcome['description'], 50) }}
                                        </option>
                                    @endforeach
                                </x-form.select>
                            </div>

                            <div>
                                <x-form.label>Unit Learning Outcomes</x-form.label>
                                <x-form.textarea rows="3"
                                    wire:model="weekInputs.{{ $week->week_no }}.learning_outcomes" />
                            </div>

                            <div>
                                <x-form.label>Assessment Task</x-form.label>
                                <x-form.textarea rows="3"
                                    wire:model="weekInputs.{{ $week->week_no }}.assessment_task" />
                            </div>

                            <div>
                                <x-form.label>Topics</x-form.label>
                                <x-form.textarea rows="3"
                                    wire:model="weekInputs.{{ $week->week_no }}.topic" />
                            </div>

                            <div>
                                <x-form.label>Teaching and Learning Activities</x-form.label>
                                <x-form.textarea rows="3"
                                    wire:model="weekInputs.{{ $week->week_no }}.teaching_activities" />
                            </div>
                        </div>

                        <hr>

                        <div class="flex flex-wrap gap-4">
                            <div class="w-full md:w-[48%]">
                                <x-form.label>References</x-form.label>
                                <x-form.input type="text"
                                    wire:model="weekInputs.{{ $week->week_no }}.reference_text" />
                            </div>

                            <div class="w-full md:w-[48%]">
                                <x-form.label>Online Material Name</x-form.label>
                                <x-form.input type="text"
                                    wire:model="weekInputs.{{ $week->week_no }}.material_name" />
                                <x-form.label class="mt-2">Online Material URL</x-form.label>
                                <x-form.input type="text"
                                    wire:model="weekInputs.{{ $week->week_no }}.material_url" />
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
