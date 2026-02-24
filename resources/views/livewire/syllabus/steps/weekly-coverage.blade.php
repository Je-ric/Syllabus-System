<div>
    <div class="mb-4 flex items-start justify-between gap-4">
        <div>
            <h3 class="text-xl font-semibold text-slate-900">Weekly Coverage</h3>
            <p class="text-sm text-slate-600">
                Weeks are generated from the academic calendar. Events and dates are shown per week.
            </p>
            <div class="mt-2 flex items-center gap-2">
                <button type="button"
                    wire:click="generateWeeklyCoverage"
                    @if (!$academic_calendar_id || $weeksGenerated) disabled @endif
                    class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold rounded border border-emerald-300 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 disabled:bg-slate-100 disabled:text-slate-400 disabled:border-slate-200 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="generateWeeklyCoverage">
                        <i class="bx bx-refresh"></i> {{ $weeksGenerated ? 'Weeks Generated' : 'Generate Weeks' }}
                    </span>
                    <span wire:loading wire:target="generateWeeklyCoverage">
                        <i class="bx bx-loader-alt bx-spin"></i> Generating...
                    </span>
                </button>
                <span class="text-xs text-slate-500">
                    Generation is manual to keep step switching fast.
                </span>
            </div>
        </div>
        <div class="text-xs text-slate-600 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2">
            <div>
                Calendar:
                @if ($syllabus?->academicCalendar)
                    {{ \Carbon\Carbon::parse($syllabus->academicCalendar->start_date)->format('M d, Y') }}
                    -
                    {{ \Carbon\Carbon::parse($syllabus->academicCalendar->end_date)->format('M d, Y') }}
                @else
                    Not set
                @endif
            </div>
            <div class="flex justify-between">
                <div>Total weeks: {{ $syllabusWeeks->count() }}</div>
                <div>Total events: {{ collect($weekEvents)->flatten(1)->count() }}</div>
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
        @php
            $weekTabs = collect($syllabusWeeks)
                ->map(function ($week) {
                    return [
                        'id' => 'week_' . $week->week_no,
                        'label' => 'Week ' . $week->week_no,
                    ];
                })
                ->toArray();
        @endphp

        <div x-data="{ activeWeek: '{{ $activeWeekTab ?? ($weekTabs[0]['id'] ?? '') }}' }" class="border border-slate-200 rounded-xl bg-white">
            <div class="border-b border-slate-200">
                <nav class="flex flex-wrap px-2 sm:px-4 gap-x-2 md:gap-x-4" aria-label="Weeks">
                    @foreach ($weekTabs as $tab)
                        <button type="button" @click="activeWeek = '{{ $tab['id'] }}'"
                            :class="activeWeek === '{{ $tab['id'] }}'
                                ?
                                'border-b-2 border-[#1a2235] text-[#1a2235] font-semibold' :
                                'border-b-0 text-slate-500 hover:text-[#ffb51b] hover:border-b-2 hover:border-[#ffb51b]'"
                            class="whitespace-nowrap py-2 px-3 text-sm transition-all duration-200 focus:outline-none">
                            {{ $tab['label'] }}
                        </button>
                    @endforeach
                </nav>
            </div>

            <div class="px-3 sm:px-4 md:px-6 py-4">
                @foreach ($syllabusWeeks as $week)
                    @php
                        $panelId = 'week_' . $week->week_no;
                        $start = \Carbon\Carbon::parse($week->start_date);
                        $end = \Carbon\Carbon::parse($week->end_date);
                        $day = $start->copy();
                    @endphp
                    <div x-show="activeWeek === '{{ $panelId }}'" x-cloak class="space-y-4">
                        <div class="bg-white border border-slate-200 rounded-lg p-4 space-y-4">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="text-xs font-semibold text-slate-800">
                                    Class Schedule:
                                </div>
                            </div>

                            @if ($courseComponents)
                                <div class="bg-slate-50 border border-slate-200 rounded p-3 space-y-2 mb-4">
                                    @if (isset($courseComponents['LEC']))
                                        <div>
                                            <div class="font-semibold text-emerald-700 text-xs">Lecture (LEC)</div>
                                            <p class="text-xs">Class Schedule</p><div class="text-slate-700 text-xs">{{ $courseComponents['LEC']['schedule'] ?? 'N/A' }}</div>
                                            <p class="text-xs">Class Hours</p><div class="text-slate-500 text-xs">{{ $courseComponents['LEC']['class_hours'] ?? '' }}</div>
                                        </div>
                                    @endif
                                    @if (isset($courseComponents['LAB']))
                                        <div class="border-t border-slate-300 pt-2">
                                            <div class="font-semibold text-blue-700 text-xs">Laboratory (LAB)</div>
                                            <p class="text-xs">Class Schedule</p><div class="text-slate-700 text-xs">{{ $courseComponents['LAB']['schedule'] ?? 'N/A' }}</div>
                                            <p class="text-xs">Class Hours</p><div class="text-slate-500 text-xs">{{ $courseComponents['LAB']['class_hours'] ?? '' }}</div>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <div>
                                <div class="text-xs font-semibold text-slate-600 mb-2">Weekdays</div>
                                <div class="text-xs text-slate-700 mb-2">
                                    {{ $start->format('M d') }} - {{ $end->format('M d, Y') }}
                                </div>
                                <div class="flex flex-wrap gap-2 text-xs text-slate-700">
                                    @while ($day->lte($end))
                                        <span class="px-2 py-1 rounded bg-slate-100 border border-slate-200">
                                            {{ $day->format('D, M d') }}
                                        </span>
                                        @php $day->addDay(); @endphp
                                    @endwhile
                                </div>
                            </div>

                            <div>
                                <div class="text-xs font-semibold text-slate-600 mb-2">Events</div>
                                @php
                                    $events = $weekEvents[$week->week_no] ?? collect();
                                @endphp
                                @if ($events->isEmpty())
                                    <div class="text-sm text-slate-500">No events for this week.</div>
                                @else
                                    <ul class="space-y-2 text-sm text-slate-700">
                                        @foreach ($events as $event)
                                            <li class="flex items-start gap-2">
                                                <span class="text-slate-400">*</span>
                                                <span>
                                                    <span class="font-medium">{{ $event->name }}</span>
                                                    <span class="text-slate-500">
                                                        ({{ \Carbon\Carbon::parse($event->date)->format('M d, Y') }})
                                                    </span>
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
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
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
