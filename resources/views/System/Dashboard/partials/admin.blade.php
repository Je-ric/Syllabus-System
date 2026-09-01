@php
    $semester     = $data['active_semester'] ?? null;
    $totalWeeks   = $semester['total_weeks']  ?? 0;
    $currentWeek  = $semester['current_week'] ?? 0;
    $daysRemaining= $semester['days_remaining'] ?? 0;
    $progressPct  = $totalWeeks > 0 ? min(100, round(($currentWeek / $totalWeeks) * 100)) : 0;

    $sc = $data['syllabus_counts']  ?? [];
    $rc = $data['role_counts']      ?? [];
    $st = $data['structure_counts'] ?? [];
    $totalSyllabi = array_sum($sc);
@endphp

<div class="grid grid-cols-4 gap-4" style="grid-template-rows: auto auto 1fr 1fr 1fr;">

    {{-- ═══════════════════════════════════════════════════════════
         CARD 1 — HERO: Semester Overview (cols 1-2, rows 1-2)
    ═══════════════════════════════════════════════════════════ --}}
    <div class="col-span-2 row-span-2">
        <div class="h-full rounded-2xl border border-[#c8ddd4] bg-white overflow-hidden flex flex-col"
             style="box-shadow: 0 1px 3px rgba(0,58,16,0.06);">
            <div class="px-5 py-3.5 border-b border-[#e4ede9] flex items-center justify-between shrink-0"
                 style="background: linear-gradient(135deg, #f2f6f5 0%, #eaf2ee 100%);">
                <div class="flex items-center gap-2.5">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-[#003a10]/10">
                        <i class="bx bx-calendar-star text-[#003a10] text-sm leading-none"></i>
                    </span>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#1a5f30]/70">Academic Calendar</p>
                        <p class="text-sm font-bold text-[#003a10] leading-tight">Semester Overview</p>
                    </div>
                </div>
                @if ($semester)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold
                                 bg-[#009639]/10 text-[#003a10] ring-1 ring-inset ring-[#009639]/25">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#009639] animate-pulse"></span>
                        Active
                    </span>
                @endif
            </div>
            <div class="p-5 flex-1 flex flex-col gap-4">
                @if ($semester)
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#72809E] mb-1">Active Semester</p>
                            <p class="text-xl font-bold text-[#003a10] leading-tight">{{ $semester['label'] }}</p>
                        </div>
                        <div class="shrink-0 text-center px-4 py-2 rounded-xl bg-[#003a10] text-white">
                            <p class="text-2xl font-black leading-none">{{ $currentWeek }}</p>
                            <p class="text-[9px] font-bold uppercase tracking-widest mt-0.5 opacity-70">of {{ $totalWeeks }} wks</p>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#72809E]">Semester Progress</p>
                            <p class="text-[11px] font-bold text-[#1a5f30]">{{ $progressPct }}%</p>
                        </div>
                        <div class="h-2.5 rounded-full bg-[#e4ede9] overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500"
                                 style="width: {{ $progressPct }}%; background: linear-gradient(90deg, #009639 0%, #1a5f30 100%);"></div>
                        </div>
                        <div class="flex justify-between mt-1">
                            <span class="text-[10px] text-[#72809E]">{{ $semester['start_date'] ?? '—' }}</span>
                            <span class="text-[10px] text-[#72809E]">{{ $semester['end_date'] ?? '—' }}</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-3 mt-auto">
                        <div class="rounded-xl p-3 border border-[#AEFFE2] bg-[#f0fff8]">
                            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#00965F] mb-1">Today</p>
                            <p class="text-sm font-bold text-[#003a10] leading-snug">{{ $semester['current_date'] ?? '—' }}</p>
                        </div>
                        <div class="rounded-xl p-3 border border-[#1a5f30]"
                             style="background: linear-gradient(135deg, #009639 0%, #1a5f30 100%);">
                            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#D5FFF0] mb-1">Days Left</p>
                            <p class="text-2xl font-black text-white leading-none">{{ $daysRemaining }}</p>
                        </div>
                        <div class="rounded-xl p-3 border border-[#E2E8F0] bg-[#F8FAFC]">
                            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#64748B] mb-1">End Date</p>
                            <p class="text-sm font-bold text-[#1E293B] leading-snug">{{ $semester['end_date'] ?? '—' }}</p>
                        </div>
                    </div>
                @else
                    <x-dashboard.empty-state
                        icon="bx-calendar-x"
                        title="No active semester"
                        message="Set an active calendar under Academic Calendars."
                    />
                @endif
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         CARD 2 — Stat: Total Users + Pending (col 3, row 1)
    ═══════════════════════════════════════════════════════════ --}}
    <div class="col-start-3 row-start-1">
        <div class="h-full rounded-2xl border border-[#c8ddd4] bg-white overflow-hidden flex flex-col justify-between p-4"
             style="box-shadow: 0 1px 2px rgba(0,58,16,0.06);">
            <div class="flex items-center justify-between">
                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#72809E]">Total Users</p>
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-[#D5FFF0] text-[#003a10]">
                    <i class="bx bx-group text-sm leading-none"></i>
                </span>
            </div>
            <div class="flex items-end justify-between gap-2">
                <div>
                    <p class="text-4xl font-black text-[#003a10] leading-none">{{ number_format($data['total_users'] ?? 0) }}</p>
                    <p class="text-[11px] text-[#72809E] mt-1 font-medium">registered accounts</p>
                </div>
                @if (($data['pending_approvals'] ?? 0) > 0)
                    <a href="{{ route('accounts.approval') }}"
                       class="shrink-0 inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-[11px] font-bold
                              bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200 hover:bg-amber-100 transition-colors">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                        {{ $data['pending_approvals'] }} pending
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         CARD 3 — Stat: Total Syllabi (col 3, row 2)
    ═══════════════════════════════════════════════════════════ --}}
    <div class="col-start-3 row-start-2">
        <div class="h-full rounded-2xl border border-[#AEFFE2] bg-[#f0fff8] overflow-hidden flex flex-col justify-between p-4"
             style="box-shadow: 0 1px 2px rgba(0,150,63,0.07);">
            <div class="flex items-center justify-between">
                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#00965F]">Total Syllabi</p>
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-[#D5FFF0] text-[#003a10]">
                    <i class="bx bx-file text-sm leading-none"></i>
                </span>
            </div>
            <div>
                <p class="text-4xl font-black text-[#003a10] leading-none">{{ number_format($totalSyllabi) }}</p>
                <p class="text-[11px] text-[#00965F] mt-1 font-medium">across all programs</p>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         CARD 4 — Semester Events Feed (col 4, rows 1-5)
    ═══════════════════════════════════════════════════════════ --}}
    <div class="col-start-4 row-start-1 row-span-5">
        <div class="h-full rounded-2xl border border-[#c8ddd4] bg-white overflow-hidden flex flex-col"
             style="box-shadow: 0 1px 3px rgba(0,58,16,0.06);">
            <div class="px-4 py-3.5 border-b border-[#e4ede9] shrink-0 flex items-center justify-between"
                 style="background: linear-gradient(135deg, #f2f6f5 0%, #eaf2ee 100%);">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-[#003a10]/10">
                        <i class="bx bx-calendar-event text-[#003a10] text-xs leading-none"></i>
                    </span>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#1a5f30]/70">Schedule</p>
                        <p class="text-sm font-bold text-[#003a10] leading-tight">Semester Events</p>
                    </div>
                </div>
                @if (!empty($data['upcoming_events']))
                    <span class="inline-flex items-center justify-center min-w-[22px] h-5 px-1.5 rounded-full
                                 text-[10px] font-bold bg-[#003a10] text-white">
                        {{ count($data['upcoming_events']) }}
                    </span>
                @endif
            </div>
            <div class="flex-1 overflow-y-auto p-3 space-y-2">
                @forelse ($data['upcoming_events'] ?? [] as $event)
                    @php
                        $isPast    = $event['is_past'] ?? false;
                        $daysUntil = $event['days_until'] ?? null;
                        $urgent    = !$isPast && $daysUntil !== null && $daysUntil <= 3;
                        $typeBadge = match($event['type'] ?? 'other') {
                            'holiday'      => 'bg-blue-100 text-blue-700 ring-blue-200',
                            'exam'         => 'bg-rose-100 text-rose-700 ring-rose-200',
                            'break'        => 'bg-purple-100 text-purple-700 ring-purple-200',
                            'non_teaching' => 'bg-amber-100 text-amber-700 ring-amber-200',
                            default        => 'bg-slate-100 text-slate-600 ring-slate-200',
                        };
                        $rowBg = $isPast
                            ? 'bg-slate-50 border-slate-100 opacity-50'
                            : ($urgent ? 'bg-rose-50 border-rose-200' : 'bg-[#f2f6f5] border-[#c8ddd4]');
                    @endphp
                    <div class="rounded-xl border p-3 {{ $rowBg }}">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-[12px] font-semibold text-[#394056] leading-snug flex-1 min-w-0 truncate">
                                {{ $event['name'] }}
                            </p>
                            <span class="shrink-0 text-[10px] font-bold px-2 py-0.5 rounded-full ring-1 ring-inset {{ $typeBadge }}">
                                {{ ucfirst(str_replace('_', ' ', $event['type'] ?? 'other')) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between mt-1.5">
                            <p class="text-[11px] text-[#72809E]">{{ $event['date'] }}</p>
                            @if (!$isPast && $daysUntil !== null)
                                <span class="text-[11px] font-bold {{ $urgent ? 'text-rose-600' : 'text-[#009639]' }}">
                                    {{ $daysUntil === 0 ? 'Today' : ($daysUntil === 1 ? 'Tomorrow' : $daysUntil . 'd away') }}
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <x-dashboard.empty-state
                        icon="bx-calendar-check"
                        title="No upcoming events"
                        message="No academic events in the next 30 days."
                    />
                @endforelse
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         CARD 5 — People: Role Breakdown (col 1, rows 3-5)
    ═══════════════════════════════════════════════════════════ --}}
    <div class="col-start-1 row-start-3 row-span-3">
        <div class="h-full rounded-2xl border border-[#c8ddd4] bg-white overflow-hidden flex flex-col"
             style="box-shadow: 0 1px 3px rgba(0,58,16,0.06);">
            <div class="px-4 py-3.5 border-b border-[#e4ede9] shrink-0"
                 style="background: linear-gradient(135deg, #f2f6f5 0%, #eaf2ee 100%);">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-[#003a10]/10">
                        <i class="bx bx-group text-[#003a10] text-xs leading-none"></i>
                    </span>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#1a5f30]/70">People</p>
                        <p class="text-sm font-bold text-[#003a10] leading-tight">Role Breakdown</p>
                    </div>
                </div>
            </div>
            <div class="p-4 flex-1 flex flex-col gap-2.5">
                @foreach ([
                    ['label' => 'Faculty',        'value' => $rc['faculty'] ?? 0, 'icon' => 'bx-user',     'bg' => 'bg-[#D5FFF0]', 'text' => 'text-[#003a10]', 'sub' => 'text-[#00965F]'],
                    ['label' => 'Chairpersons',   'value' => $rc['chair']   ?? 0, 'icon' => 'bx-user-pin', 'bg' => 'bg-[#DAF1FF]', 'text' => 'text-[#143D57]', 'sub' => 'text-[#3197D6]'],
                    ['label' => 'Deans',          'value' => $rc['dean']    ?? 0, 'icon' => 'bx-medal',    'bg' => 'bg-[#EEF2FF]', 'text' => 'text-[#3730A3]', 'sub' => 'text-[#4338CA]'],
                    ['label' => 'Administrators', 'value' => $rc['admin']   ?? 0, 'icon' => 'bx-crown',    'bg' => 'bg-[#FFF6E2]', 'text' => 'text-[#875200]', 'sub' => 'text-[#B45309]'],
                ] as $row)
                    <div class="flex items-center gap-3 rounded-xl px-3 py-2.5 border border-[#e4ede9] bg-[#f2f6f5]">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg shrink-0 {{ $row['bg'] }}">
                            <i class="bx {{ $row['icon'] }} text-sm leading-none {{ $row['text'] }}"></i>
                        </span>
                        <span class="flex-1 text-[12px] font-semibold text-[#394056]">{{ $row['label'] }}</span>
                        <span class="text-xl font-black {{ $row['text'] }}">{{ number_format($row['value']) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         CARD 6 — Structure Counts (col 2, rows 3-5)
    ═══════════════════════════════════════════════════════════ --}}
    <div class="col-start-2 row-start-3 row-span-3">
        <div class="h-full rounded-2xl border border-[#c8ddd4] bg-white overflow-hidden flex flex-col"
             style="box-shadow: 0 1px 3px rgba(0,58,16,0.06);">
            <div class="px-4 py-3.5 border-b border-[#e4ede9] shrink-0"
                 style="background: linear-gradient(135deg, #f2f6f5 0%, #eaf2ee 100%);">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-[#003a10]/10">
                        <i class="bx bx-buildings text-[#003a10] text-xs leading-none"></i>
                    </span>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#1a5f30]/70">Academic</p>
                        <p class="text-sm font-bold text-[#003a10] leading-tight">Structure</p>
                    </div>
                </div>
            </div>
            <div class="p-4 flex-1 flex flex-col gap-2.5">
                @foreach ([
                    ['label' => 'Colleges',    'value' => $st['colleges']    ?? 0, 'icon' => 'bx-buildings',    'bg' => 'bg-[#D5FFF0]', 'text' => 'text-[#003a10]'],
                    ['label' => 'Departments', 'value' => $st['departments'] ?? 0, 'icon' => 'bx-sitemap',      'bg' => 'bg-[#DAF1FF]', 'text' => 'text-[#143D57]'],
                    ['label' => 'Programs',    'value' => $st['programs']    ?? 0, 'icon' => 'bx-network-chart','bg' => 'bg-[#EEF2FF]', 'text' => 'text-[#3730A3]'],
                    ['label' => 'Courses',     'value' => $st['courses']     ?? 0, 'icon' => 'bx-book',         'bg' => 'bg-[#FFF6E2]', 'text' => 'text-[#875200]'],
                ] as $row)
                    <div class="flex items-center gap-3 rounded-xl px-3 py-2.5 border border-[#e4ede9] bg-[#f2f6f5]">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg shrink-0 {{ $row['bg'] }}">
                            <i class="bx {{ $row['icon'] }} text-sm leading-none {{ $row['text'] }}"></i>
                        </span>
                        <span class="flex-1 text-[12px] font-semibold text-[#394056]">{{ $row['label'] }}</span>
                        <span class="text-xl font-black {{ $row['text'] }}">{{ number_format($row['value']) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         CARD 7 — Syllabus Status Breakdown + Recent (col 3, rows 3-5)
    ═══════════════════════════════════════════════════════════ --}}
    <div class="col-start-3 row-start-3 row-span-3">
        <div class="h-full rounded-2xl border border-[#c8ddd4] bg-white overflow-hidden flex flex-col"
             style="box-shadow: 0 1px 3px rgba(0,58,16,0.06);">
            <div class="px-4 py-3.5 border-b border-[#e4ede9] shrink-0"
                 style="background: linear-gradient(135deg, #f2f6f5 0%, #eaf2ee 100%);">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-[#003a10]/10">
                        <i class="bx bx-file text-[#003a10] text-xs leading-none"></i>
                    </span>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#1a5f30]/70">Syllabi</p>
                        <p class="text-sm font-bold text-[#003a10] leading-tight">Status Overview</p>
                    </div>
                </div>
            </div>
            <div class="p-4 flex flex-col gap-2.5">
                @foreach ([
                    ['label' => 'Approved',     'value' => $sc['approved']     ?? 0, 'dot' => 'bg-[#009639]',  'text' => 'text-[#003a10]'],
                    ['label' => 'Under Review', 'value' => $sc['under_review'] ?? 0, 'dot' => 'bg-amber-400',  'text' => 'text-amber-700'],
                    ['label' => 'For Revision', 'value' => $sc['for_revision'] ?? 0, 'dot' => 'bg-rose-400',   'text' => 'text-rose-700'],
                    ['label' => 'Draft',        'value' => $sc['draft']        ?? 0, 'dot' => 'bg-slate-400',  'text' => 'text-slate-600'],
                ] as $row)
                    <div class="flex items-center gap-3 rounded-xl px-3 py-2.5 border border-[#e4ede9] bg-[#f2f6f5]">
                        <span class="w-2 h-2 rounded-full shrink-0 {{ $row['dot'] }}"></span>
                        <span class="flex-1 text-[12px] font-semibold text-[#394056]">{{ $row['label'] }}</span>
                        <span class="text-xl font-black {{ $row['text'] }}">{{ number_format($row['value']) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
