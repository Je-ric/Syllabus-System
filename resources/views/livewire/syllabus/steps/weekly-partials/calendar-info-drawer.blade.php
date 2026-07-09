{{-- weekly-partials/calendar-info-drawer.blade.php --}}
<x-offcanvas title="Academic Calendar" subtitle="Semester dates and event overview" icon="bx-calendar" open="calInfoOpen">

    @if ($syllabus?->academicCalendar)
        @php $cal = $syllabus->academicCalendar; @endphp

        {{-- Date range hero --}}
        <div class="rounded-[14px] border border-[#d1fae5] bg-[#f0fdf4] px-4 py-4 mb-4 flex items-center gap-3">
            <span class="shrink-0 flex items-center justify-center w-10 h-10 rounded-[12px] bg-[#dcfce7] text-[#16a34a]">
                <i class="bx bx-calendar-week text-xl leading-none"></i>
            </span>
            <div class="min-w-0">
                <p class="text-[13px] font-bold text-[#09090b] leading-tight">
                    {{ \Carbon\Carbon::parse($cal->start_date)->format('M d') }}
                    <span class="text-[#86efac] mx-1">→</span>
                    {{ \Carbon\Carbon::parse($cal->end_date)->format('M d, Y') }}
                </p>
                @if ($cal->academic_year ?? null)
                    <p class="text-[11px] text-[#16a34a] font-semibold uppercase tracking-wide mt-0.5">{{ $cal->academic_year }}</p>
                @endif
            </div>
        </div>

        {{-- Stats --}}
        @if ($weeksGenerated)
            @php $lockedCount = count($lockedWeeks); @endphp
            <div class="grid grid-cols-3 gap-2 mb-5">
                <div class="rounded-[12px] border border-[#d1fae5] bg-[#f0fdf4] px-3 py-3 text-center">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-[8px] bg-[#dcfce7] text-[#16a34a] mb-1.5">
                        <i class="bx bx-calendar text-sm leading-none"></i>
                    </span>
                    <p class="text-xl font-bold text-[#166534] leading-none">{{ $syllabusWeeks->count() }}</p>
                    <p class="text-[10px] text-[#16a34a] font-bold uppercase tracking-widest mt-1">Weeks</p>
                </div>
                <div class="rounded-[12px] border border-[#e4e4e7] bg-[#fafafa] px-3 py-3 text-center">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-[8px] bg-[#f4f4f5] text-[#52525b] mb-1.5">
                        <i class="bx bx-calendar-event text-sm leading-none"></i>
                    </span>
                    <p class="text-xl font-bold text-[#18181b] leading-none">{{ collect($weekEvents)->flatten(1)->count() }}</p>
                    <p class="text-[10px] text-[#71717a] font-bold uppercase tracking-widest mt-1">Events</p>
                </div>
                <div class="rounded-[12px] border border-[#fde68a] bg-[#fffbeb] px-3 py-3 text-center">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-[8px] bg-[#fef3c7] text-[#d97706] mb-1.5">
                        <i class="bx bx-lock-alt text-sm leading-none"></i>
                    </span>
                    <p class="text-xl font-bold text-[#92400e] leading-none">{{ $lockedCount }}</p>
                    <p class="text-[10px] text-[#d97706] font-bold uppercase tracking-widest mt-1">Locked</p>
                </div>
            </div>
        @endif

        {{-- Events grouped --}}
        @php
            $drawerGrouped = [
                'exam'         => ['label' => 'Exams',        'dot' => 'bg-[#d97706]',  'icon' => 'bx-clipboard',              'bg' => 'bg-[#fffbeb]',  'border' => 'border-[#fde68a]',  'icon_bg' => 'bg-[#fef3c7] text-[#d97706]'],
                'non_teaching' => ['label' => 'Non-Teaching', 'dot' => 'bg-[#e11d48]',  'icon' => 'bx-block',                  'bg' => 'bg-[#fff1f2]',  'border' => 'border-[#fecdd3]',  'icon_bg' => 'bg-[#ffe4e6] text-[#e11d48]'],
                'holiday'      => ['label' => 'Holidays',     'dot' => 'bg-[#2563eb]',  'icon' => 'bx-sun',                    'bg' => 'bg-[#eff6ff]',  'border' => 'border-[#bfdbfe]',  'icon_bg' => 'bg-[#dbeafe] text-[#2563eb]'],
                'break'        => ['label' => 'Breaks',       'dot' => 'bg-[#71717a]',  'icon' => 'bx-coffee',                 'bg' => 'bg-[#fafafa]',  'border' => 'border-[#e4e4e7]',  'icon_bg' => 'bg-[#f4f4f5] text-[#52525b]'],
                'other'        => ['label' => 'Other',        'dot' => 'bg-[#a1a1aa]',  'icon' => 'bx-dots-horizontal-rounded','bg' => 'bg-[#fafafa]',  'border' => 'border-[#e4e4e7]',  'icon_bg' => 'bg-[#f4f4f5] text-[#71717a]'],
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
                        <span class="w-1.5 h-1.5 rounded-full {{ $meta['dot'] }} shrink-0"></span>
                        <p class="text-[11px] font-bold uppercase tracking-[0.1em] text-[#52525b]">{{ $meta['label'] }}</p>
                        <span class="text-[11px] text-[#a1a1aa]">({{ count($typeEvents) }})</span>
                    </div>
                    <div class="space-y-1.5 border-l-2 border-[#e4e4e7] pl-3 ml-0.5">
                        @foreach ($typeEvents as $ev)
                            <div class="flex items-center gap-3 rounded-[12px] border {{ $meta['border'] }} {{ $meta['bg'] }} px-3 py-2.5">
                                <span class="shrink-0 flex items-center justify-center w-7 h-7 rounded-[8px] {{ $meta['icon_bg'] }}">
                                    <i class="bx {{ $meta['icon'] }} text-sm leading-none"></i>
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[13px] font-medium text-[#18181b] truncate">{{ $ev['name'] }}</p>
                                    <p class="text-[11px] text-[#71717a]">{{ $ev['date_display'] }}</p>
                                </div>
                                <span class="shrink-0 text-[10px] font-bold text-[#52525b] bg-white border border-[#e4e4e7] px-2 py-1 rounded-full">Wk {{ $ev['week_no'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach

        @if (!$hasAnyEvent)
            <x-feedback-status.empty-state icon="bx-calendar-x" title="No events" message="No calendar events found for this semester." />
        @endif

    @else
        <x-feedback-status.empty-state icon="bx-calendar-x" title="No calendar selected" message="Select an academic calendar in Step 1." />
    @endif

</x-offcanvas>
