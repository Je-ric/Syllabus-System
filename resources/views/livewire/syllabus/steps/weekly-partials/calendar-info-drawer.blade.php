{{-- weekly-partials/calendar-info-drawer.blade.php
     Requires: $syllabus, $syllabusWeeks, $weekEvents, $lockedWeeks, $weeksGenerated
     Alpine state in parent: calInfoOpen
--}}
<x-offcanvas title="Academic Calendar" subtitle="Semester dates and event overview" icon="bx-calendar" open="calInfoOpen">

    @if ($syllabus?->academicCalendar)
        @php $cal = $syllabus->academicCalendar; @endphp

        {{-- Date range — hero card --}}
        <div class="relative overflow-hidden rounded-xl border border-emerald-200 mb-4"
             style="background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);">
            <div class="flex items-center gap-3 px-4 py-4">
                <span class="shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600">
                    <i class="bx bx-calendar-week text-xl leading-none"></i>
                </span>
                <div class="min-w-0">
                    <p class="text-sm font-bold text-slate-800 leading-tight">
                        {{ \Carbon\Carbon::parse($cal->start_date)->format('M d') }}
                        <span class="text-emerald-300 mx-1">→</span>
                        {{ \Carbon\Carbon::parse($cal->end_date)->format('M d, Y') }}
                    </p>
                    @if ($cal->academic_year ?? null)
                        <p class="text-xs text-emerald-600 font-semibold uppercase tracking-wide mt-0.5">{{ $cal->academic_year }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Stats --}}
        @if ($weeksGenerated)
            @php $lockedCount = count($lockedWeeks); @endphp
            <div class="grid grid-cols-3 gap-2 mb-5">
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-3 text-center">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-emerald-100 text-emerald-600 mb-1.5">
                        <i class="bx bx-calendar text-sm leading-none"></i>
                    </span>
                    <p class="text-xl font-bold text-emerald-700 leading-none">{{ $syllabusWeeks->count() }}</p>
                    <p class="text-[10px] text-emerald-600 font-bold uppercase tracking-widest mt-1">Weeks</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-center">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-slate-200 text-slate-600 mb-1.5">
                        <i class="bx bx-calendar-event text-sm leading-none"></i>
                    </span>
                    <p class="text-xl font-bold text-slate-700 leading-none">{{ collect($weekEvents)->flatten(1)->count() }}</p>
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1">Events</p>
                </div>
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-3 text-center">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-amber-100 text-amber-600 mb-1.5">
                        <i class="bx bx-lock-alt text-sm leading-none"></i>
                    </span>
                    <p class="text-xl font-bold text-amber-700 leading-none">{{ $lockedCount }}</p>
                    <p class="text-[10px] text-amber-600 font-bold uppercase tracking-widest mt-1">Locked</p>
                </div>
            </div>
        @endif

        {{-- Events grouped --}}
        @php
            $drawerGrouped = [
                'exam'         => ['label' => 'Exams',        'dot' => 'bg-amber-400', 'icon' => 'bx-clipboard',   'ring' => 'ring-amber-200'],
                'non_teaching' => ['label' => 'Non-Teaching', 'dot' => 'bg-rose-400',  'icon' => 'bx-block',       'ring' => 'ring-rose-200'],
                'holiday'      => ['label' => 'Holidays',     'dot' => 'bg-blue-400',  'icon' => 'bx-sun',         'ring' => 'ring-blue-200'],
                'break'        => ['label' => 'Breaks',       'dot' => 'bg-slate-400', 'icon' => 'bx-coffee',      'ring' => 'ring-slate-200'],
                'other'        => ['label' => 'Other',        'dot' => 'bg-slate-300', 'icon' => 'bx-dots-horizontal-rounded', 'ring' => 'ring-slate-200'],
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
                <div class="mb-5">
                    <div class="flex items-center gap-2 mb-2.5">
                        <span class="w-1.5 h-1.5 rounded-full {{ $meta['dot'] }}"></span>
                        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-500">{{ $meta['label'] }}</p>
                        <span class="text-[10px] font-semibold text-slate-300">({{ count($typeEvents) }})</span>
                    </div>
                    <div class="space-y-1.5 border-l-2 border-slate-100 pl-3 ml-0.5">
                        @foreach ($typeEvents as $ev)
                            <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2.5 hover:border-slate-300 transition-colors"
                                 style="box-shadow: 0 1px 3px rgba(0,0,0,.04);">
                                <span class="shrink-0 flex items-center justify-center w-7 h-7 rounded-lg bg-slate-50 ring-1 ring-inset {{ $meta['ring'] }}">
                                    <i class="bx {{ $meta['icon'] }} text-sm leading-none text-slate-500"></i>
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-700 truncate">{{ $ev['name'] }}</p>
                                    <p class="text-xs text-slate-400">{{ $ev['date_display'] }}</p>
                                </div>
                                <span class="shrink-0 text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded-full">Wk {{ $ev['week_no'] }}</span>
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