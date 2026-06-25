{{-- weekly-partials/calendar-info-drawer.blade.php
     Requires: $syllabus, $syllabusWeeks, $weekEvents, $lockedWeeks, $weeksGenerated
     Alpine state in parent: calInfoOpen
--}}
<x-offcanvas title="Academic Calendar" subtitle="Semester dates and event overview" icon="bx-calendar" open="calInfoOpen">

    @if ($syllabus?->academicCalendar)
        @php $cal = $syllabus->academicCalendar; @endphp

        {{-- Date range --}}
        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 mb-4">
            <p class="text-sm font-semibold text-slate-800 leading-tight">
                {{ \Carbon\Carbon::parse($cal->start_date)->format('M d') }}
                <span class="text-slate-300 mx-1">–</span>
                {{ \Carbon\Carbon::parse($cal->end_date)->format('M d, Y') }}
            </p>
            @if ($cal->academic_year ?? null)
                <p class="text-xs text-slate-400 mt-0.5">{{ $cal->academic_year }}</p>
            @endif
        </div>

        {{-- Stats --}}
        @if ($weeksGenerated)
            @php $lockedCount = count($lockedWeeks); @endphp
            <div class="grid grid-cols-3 gap-2 mb-4">
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2.5 text-center">
                    <p class="text-lg font-bold text-emerald-700">{{ $syllabusWeeks->count() }}</p>
                    <p class="text-xs text-emerald-600 font-semibold uppercase tracking-wide">Weeks</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-center">
                    <p class="text-lg font-bold text-slate-700">{{ collect($weekEvents)->flatten(1)->count() }}</p>
                    <p class="text-xs text-slate-500 font-semibold uppercase tracking-wide">Events</p>
                </div>
                <div class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2.5 text-center">
                    <p class="text-lg font-bold text-amber-700">{{ $lockedCount }}</p>
                    <p class="text-xs text-amber-600 font-semibold uppercase tracking-wide">Locked</p>
                </div>
            </div>
        @endif

        {{-- Events grouped --}}
        @php
            $drawerGrouped = [
                'exam'         => ['label' => 'Exams',        'dot' => 'bg-amber-400'],
                'non_teaching' => ['label' => 'Non-Teaching', 'dot' => 'bg-rose-400'],
                'holiday'      => ['label' => 'Holidays',     'dot' => 'bg-blue-400'],
                'break'        => ['label' => 'Breaks',       'dot' => 'bg-slate-400'],
                'other'        => ['label' => 'Other',        'dot' => 'bg-slate-300'],
            ];
            $hasAnyEvent = false;
        @endphp

        @foreach ($drawerGrouped as $type => $meta)
            @php
                $typeEvents = [];
                foreach ($weekEvents as $weekNo => $evList) {
                    foreach ($evList as $ev) {
                        if ($ev['type'] === $type) {
                            $typeEvents[] = array_merge($ev, ['week_no' => $weekNo]);
                        }
                    }
                }
            @endphp
            @if (count($typeEvents) > 0)
                @php $hasAnyEvent = true; @endphp
                <div class="mb-4">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">{{ $meta['label'] }}</p>
                    <div class="space-y-1.5">
                        @foreach ($typeEvents as $ev)
                            <div class="flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                                <span class="shrink-0 w-1.5 h-1.5 rounded-full {{ $meta['dot'] }}"></span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-700">{{ $ev['name'] }}</p>
                                    <p class="text-xs text-slate-400">{{ $ev['date_display'] }}</p>
                                </div>
                                <span class="shrink-0 text-xs font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">Wk {{ $ev['week_no'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach

        @if (!$hasAnyEvent)
            <x-empty-state icon="bx-calendar-x" title="No events" message="No calendar events found for this semester." />
        @endif

    @else
        <x-empty-state icon="bx-calendar-x" title="No calendar selected" message="Select an academic calendar in Step 1." />
    @endif

</x-offcanvas>
