<div>
    <h3 class="text-xl font-semibold mb-2">Weekly Coverage</h3>
    <p class="text-gray-600 text-sm mb-6">
        Weeks are generated based on the academic calendar. Events are displayed per week.
    </p>

    @if ($syllabusWeeks->isEmpty())
        <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed">
            <p class="text-gray-500">
                No weeks generated yet. Please select an academic calendar with start/end dates first.
            </p>
        </div>
    @else
        <div class="mb-4 text-xs text-gray-600 bg-gray-50 border border-gray-200 rounded px-3 py-2">
            <div class="font-semibold text-gray-700">Quick info</div>
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
            <div>Total weeks: {{ $syllabusWeeks->count() }}</div>
            <div>
                Total events:
                {{ collect($weekEvents)->flatten(1)->count() }}
            </div>
        </div>

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

        <div x-data="{ activeWeek: @entangle('activeWeekTab') }" class="border rounded-lg bg-white">
            <div class="border-b border-gray-200">
                <nav class="flex flex-wrap px-2 sm:px-4 gap-x-2 md:gap-x-4" aria-label="Weeks">
                    @foreach ($weekTabs as $tab)
                        <button type="button" @click="activeWeek = '{{ $tab['id'] }}'"
                            :class="activeWeek === '{{ $tab['id'] }}'
                                ?
                                'border-b-2 border-[#1a2235] text-[#1a2235] font-semibold' :
                                'border-b-0 text-gray-500 hover:text-[#ffb51b] hover:border-b-2 hover:border-[#ffb51b]'"
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
                    @endphp
                    <div x-show="activeWeek === '{{ $panelId }}'" x-cloak class="space-y-4">
                        <div class="bg-white border rounded-lg p-4 space-y-4">
                            <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                                <div class="font-semibold text-gray-800">
                                    Week {{ $week->week_no }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($week->start_date)->format('M d, Y') }}
                                    -
                                    {{ \Carbon\Carbon::parse($week->end_date)->format('M d, Y') }}
                                </div>
                                <div>
                                    <div class="text-xs font-semibold text-gray-600 mb-2">Events</div>
                                    @php
                                        $events = $weekEvents[$week->week_no] ?? collect();
                                    @endphp
                                    @if ($events->isEmpty())
                                        <div class="text-sm text-gray-500">No events for this week.</div>
                                    @else
                                        <ul class="space-y-2 text-sm text-gray-700">
                                            @foreach ($events as $event)
                                                <li class="flex items-start gap-2">
                                                    <span class="text-gray-400">•</span>
                                                    <span>
                                                        <span class="font-medium">{{ $event->name }}</span>
                                                        <span class="text-gray-500">
                                                            ({{ \Carbon\Carbon::parse($event->date)->format('M d, Y') }})
                                                        </span>
                                                    </span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>


                            <div class="mb-3">
                                <div class="text-xs font-semibold text-gray-600 mb-2">Exam Weeks</div>
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
                                                    {{ $isAssignedElsewhere ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}">
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
