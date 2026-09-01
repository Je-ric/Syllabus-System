{{--
    Dashboard Bento Grid Component
    Props: $data (array), $user (User model)
    Usage: <x-dashboard.bento-grid :data="$data" :user="$user" />
--}}
@props([
    'data',
    'user',
    'iedEmptyMessage'    => 'All courses have IED mappings configured.',
    'syllabiEmptyMessage' => 'No syllabi updated recently.',
])

@php
    $semester = $data['active_semester'] ?? null;
    $totalWeeks = $semester['total_weeks'] ?? 0;
    $currentWeek = $semester['current_week'] ?? 0;
    $daysRemaining = $semester['days_remaining'] ?? 0;
    $progressPct = $totalWeeks > 0 ? min(100, round(($currentWeek / $totalWeeks) * 100)) : 0;
@endphp

<div class="grid grid-cols-4 gap-4" style="grid-template-rows: auto auto 1fr 1fr 1fr;">

    {{-- ═══════════════════════════════════════════════════════════
         CARD 1 — HERO: Semester Progress (cols 1-2, rows 1-2)
    ═══════════════════════════════════════════════════════════ --}}
    <div class="col-span-2 row-span-2">
        <div class="h-full rounded-2xl border border-[#c8ddd4] bg-white overflow-hidden flex flex-col"
             style="box-shadow: 0 1px 3px rgba(0,58,16,0.06), 0 0 0 0 transparent;">

            {{-- Header --}}
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

            {{-- Body --}}
            <div class="p-5 flex-1 flex flex-col gap-4">
                @if ($semester)
                    {{-- Semester label + week badge --}}
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

                    {{-- Progress bar --}}
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

                    {{-- Detail row --}}
                    <div class="grid grid-cols-3 gap-3 mt-auto">
                        <div class="rounded-xl p-3 border border-[#AEFFE2] bg-[#f0fff8]">
                            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#00965F] mb-1">Today</p>
                            <p class="text-sm font-bold text-[#003a10] leading-snug">{{ $semester['current_date'] ?? '—' }}</p>
                        </div>
                        <div class="rounded-xl p-3 border border-[#E9D5FF] bg-[#faf5ff]">
                            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#7C3AED] mb-1">Days Left</p>
                            <p class="text-2xl font-black text-[#4C1D95] leading-none">{{ $daysRemaining }}</p>
                        </div>
                        <div class="rounded-xl p-3 border border-[#FFE9B5] bg-[#fffcf0]">
                            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#B45309] mb-1">End Date</p>
                            <p class="text-sm font-bold text-[#78350F] leading-snug">{{ $semester['end_date'] ?? '—' }}</p>
                        </div>
                    </div>
                @else
                    <div class="flex-1 flex flex-col items-center justify-center text-center gap-3 py-4">
                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-amber-50 border border-amber-200">
                            <i class="bx bx-calendar-x text-amber-500 text-2xl leading-none"></i>
                        </span>
                        <div>
                            <p class="font-bold text-[#394056]">No active semester</p>
                            <p class="text-xs text-[#72809E] mt-1">Set an active calendar under Academic Calendars.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         CARD 2 — Stat: Courses with Syllabus (col 3, row 1)
    ═══════════════════════════════════════════════════════════ --}}
    <div class="col-start-3 row-start-1">
        <div class="h-full rounded-2xl border border-[#AEDFFF] bg-[#f5fbff] overflow-hidden flex flex-col justify-between p-4"
             style="box-shadow: 0 1px 2px rgba(49,151,214,0.07);">
            <div class="flex items-center justify-between">
                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#3197D6]">Courses with Syllabus</p>
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-[#DAF1FF] text-[#143D57]">
                    <i class="bx bx-book-content text-sm leading-none"></i>
                </span>
            </div>
            <div>
                <p class="text-4xl font-black text-[#143D57] leading-none">{{ number_format($data['courses_with_syllabus_count'] ?? 0) }}</p>
                <p class="text-[11px] text-[#3197D6] mt-1 font-medium">courses covered</p>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         CARD 3 — Stat: Total Courses (col 3, row 2)
    ═══════════════════════════════════════════════════════════ --}}
    <div class="col-start-3 row-start-2">
        <div class="h-full rounded-2xl border border-[#AEFFE2] bg-[#f0fff8] overflow-hidden flex flex-col justify-between p-4"
             style="box-shadow: 0 1px 2px rgba(0,150,63,0.07);">
            <div class="flex items-center justify-between">
                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#00965F]">Total Courses</p>
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-[#D5FFF0] text-[#003a10]">
                    <i class="bx bx-book text-sm leading-none"></i>
                </span>
            </div>
            <div>
                <p class="text-4xl font-black text-[#003a10] leading-none">{{ number_format($data['total_courses_count'] ?? 0) }}</p>
                <p class="text-[11px] text-[#00965F] mt-1 font-medium">in your scope</p>
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
                        $isPast = $event['is_past'] ?? false;
                        $daysUntil = $event['days_until'] ?? null;
                        $urgent = !$isPast && $daysUntil !== null && $daysUntil <= 3;
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
                    <div class="h-full flex flex-col items-center justify-center text-center gap-2 py-8">
                        <i class="bx bx-calendar-check text-3xl text-[#c8ddd4]"></i>
                        <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-[#93A1AF]">No upcoming events</p>
                        <p class="text-[11px] text-[#93A1AF]">No academic events in the next 30 days.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         CARD 5 — Quick Links (col 1, rows 3-5)
    ═══════════════════════════════════════════════════════════ --}}
    <div class="col-start-1 row-start-3 row-span-3">
        <div class="h-full rounded-2xl border border-[#c8ddd4] bg-white overflow-hidden flex flex-col"
             style="box-shadow: 0 1px 3px rgba(0,58,16,0.06);">
            <div class="px-4 py-3.5 border-b border-[#e4ede9] shrink-0"
                 style="background: linear-gradient(135deg, #f2f6f5 0%, #eaf2ee 100%);">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-[#003a10]/10">
                        <i class="bx bx-run text-[#003a10] text-xs leading-none"></i>
                    </span>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#1a5f30]/70">Navigation</p>
                        <p class="text-sm font-bold text-[#003a10] leading-tight">Quick Links</p>
                    </div>
                </div>
            </div>
            <div class="p-4 flex-1 flex flex-col gap-2.5">
                @if ($user->hasRole('dean'))
                    <x-dashboard.bento-grid.quick-link href="{{ route('syllabus.index') }}" icon="bx-list-ul" primary>View Syllabi</x-dashboard.bento-grid.quick-link>
                    <x-dashboard.bento-grid.quick-link href="{{ route('syllabus.courses') }}" icon="bx-book-content">Browse Courses</x-dashboard.bento-grid.quick-link>
                    <x-dashboard.bento-grid.quick-link href="{{ route('goals.index') }}" icon="bxs-bullseye">Manage Goals</x-dashboard.bento-grid.quick-link>
                    <x-dashboard.bento-grid.quick-link href="{{ route('user-assignments.hierarchy') }}" icon="bx-sitemap">Manage Departments</x-dashboard.bento-grid.quick-link>
                @elseif ($user->hasRole('chair'))
                    <x-dashboard.bento-grid.quick-link href="{{ route('syllabus.index') }}" icon="bx-list-ul" primary>View Syllabi</x-dashboard.bento-grid.quick-link>
                    <x-dashboard.bento-grid.quick-link href="{{ route('syllabus.courses') }}" icon="bx-book-content">Browse Courses</x-dashboard.bento-grid.quick-link>
                    <x-dashboard.bento-grid.quick-link href="{{ route('programs.index') }}" icon="bx-network-chart">Manage Programs</x-dashboard.bento-grid.quick-link>
                    <x-dashboard.bento-grid.quick-link href="{{ route('objective.index') }}" icon="bx-target-lock">Manage Objectives</x-dashboard.bento-grid.quick-link>
                @else
                    <x-dashboard.bento-grid.quick-link href="{{ route('syllabus.create') }}" icon="bx-notepad" primary>Create Syllabus</x-dashboard.bento-grid.quick-link>
                    @if ($data['has_draft'] ?? false)
                        <x-dashboard.bento-grid.quick-link href="{{ route('syllabus.edit', ['syllabus' => $data['latest_draft_id']]) }}" icon="bx-pencil" gold>Continue Draft</x-dashboard.bento-grid.quick-link>
                    @endif
                    <x-dashboard.bento-grid.quick-link href="{{ route('syllabus.index') }}" icon="bx-list-ul">View Syllabi</x-dashboard.bento-grid.quick-link>
                    <x-dashboard.bento-grid.quick-link href="{{ route('syllabus.courses') }}" icon="bx-book-content">Browse Courses</x-dashboard.bento-grid.quick-link>
                @endif
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         CARD 6 — Courses Without IED Mapping (col 2, rows 3-5)
    ═══════════════════════════════════════════════════════════ --}}
    <div class="col-start-2 row-start-3 row-span-3">
        <div class="h-full rounded-2xl border border-[#c8ddd4] bg-white overflow-hidden flex flex-col"
             style="box-shadow: 0 1px 3px rgba(0,58,16,0.06);">
            <div class="px-4 py-3.5 border-b border-[#e4ede9] shrink-0 flex items-center justify-between"
                 style="background: linear-gradient(135deg, #f2f6f5 0%, #eaf2ee 100%);">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-rose-100">
                        <i class="bx bx-error-circle text-rose-600 text-xs leading-none"></i>
                    </span>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#1a5f30]/70">Attention Required</p>
                        <p class="text-sm font-bold text-[#003a10] leading-tight">Missing IED Mapping</p>
                    </div>
                </div>
                @if (!empty($data['courses_without_ied']))
                    <span class="inline-flex items-center justify-center min-w-[22px] h-5 px-1.5 rounded-full
                                 text-[10px] font-bold bg-rose-600 text-white">
                        {{ count($data['courses_without_ied']) }}
                    </span>
                @endif
            </div>
            <div class="flex-1 overflow-y-auto p-3 space-y-2">
                @forelse ($data['courses_without_ied'] ?? [] as $course)
                    <div class="rounded-xl border border-rose-200 bg-rose-50 p-3">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <p class="text-[12px] font-bold text-rose-900 truncate">
                                    {{ $course['course_code'] }} — {{ $course['title'] }}
                                </p>
                                <p class="text-[11px] text-rose-600 mt-0.5 truncate">{{ $course['program'] ?? 'Unknown program' }}</p>
                            </div>
                            <a href="{{ route('courses.edit', ['course' => $course['id']]) }}"
                               class="shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold
                                      bg-rose-600 text-white hover:bg-rose-700 transition-colors min-h-[28px]">
                                <i class="bx bx-edit text-xs leading-none"></i> Fix
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="h-full flex flex-col items-center justify-center text-center gap-2 py-8">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-[#D5FFF0] border border-[#AEFFE2]">
                            <i class="bx bx-check-shield text-[#009639] text-xl leading-none"></i>
                        </span>
                        <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-[#00965F]">All courses mapped</p>
                        <p class="text-[11px] text-[#72809E] px-4">{{ $iedEmptyMessage ?? 'All courses have IED mappings configured.' }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         CARD 7 — Recent Syllabi Activity (col 3, rows 3-5)
    ═══════════════════════════════════════════════════════════ --}}
    <div class="col-start-3 row-start-3 row-span-3">
        <div class="h-full rounded-2xl border border-[#c8ddd4] bg-white overflow-hidden flex flex-col"
             style="box-shadow: 0 1px 3px rgba(0,58,16,0.06);">
            <div class="px-4 py-3.5 border-b border-[#e4ede9] shrink-0"
                 style="background: linear-gradient(135deg, #f2f6f5 0%, #eaf2ee 100%);">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-[#003a10]/10">
                        <i class="bx bx-time text-[#003a10] text-xs leading-none"></i>
                    </span>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#1a5f30]/70">Activity</p>
                        <p class="text-sm font-bold text-[#003a10] leading-tight">Recent Syllabi</p>
                    </div>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto p-3 space-y-2">
                @forelse ($data['recent_syllabi'] ?? [] as $syllabus)
                    @php
                        $statusDot = match($syllabus['status'] ?? '') {
                            'approved'     => 'bg-[#009639]',
                            'under_review' => 'bg-amber-400',
                            'draft'        => 'bg-slate-400',
                            default        => 'bg-slate-300',
                        };
                        $statusText = match($syllabus['status'] ?? '') {
                            'approved'     => 'text-[#009639]',
                            'under_review' => 'text-amber-600',
                            'draft'        => 'text-slate-500',
                            default        => 'text-slate-400',
                        };
                    @endphp
                    <div class="rounded-xl border border-[#e4ede9] bg-[#f2f6f5] p-3">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <p class="text-[12px] font-bold text-[#003a10] truncate">
                                    {{ $syllabus['course_code'] ?? '—' }} — {{ $syllabus['title'] ?? 'Untitled' }}
                                </p>
                                <div class="flex items-center gap-1.5 mt-1">
                                    <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $statusDot }}"></span>
                                    <span class="text-[11px] font-semibold {{ $statusText }}">
                                        {{ $syllabus['status_label'] ?? 'Unknown' }}
                                    </span>
                                    @if (isset($syllabus['updated_at']))
                                        <span class="text-[11px] text-[#93A1AF]">· {{ $syllabus['updated_at'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="h-full flex flex-col items-center justify-center text-center gap-2 py-8">
                        <i class="bx bx-file text-3xl text-[#c8ddd4]"></i>
                        <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-[#93A1AF]">No recent activity</p>
                        <p class="text-[11px] text-[#93A1AF] px-4">{{ $syllabiEmptyMessage ?? 'No syllabi updated recently.' }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
