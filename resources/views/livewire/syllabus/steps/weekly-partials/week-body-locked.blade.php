{{-- weekly-partials/week-body-locked.blade.php --}}

@php
    $lockingEvents = array_filter($events, fn ($ev) => in_array($ev['type'], ['exam', 'non_teaching'], true));
    $otherEvents   = array_filter($events, fn ($ev) => ! in_array($ev['type'], ['exam', 'non_teaching'], true));
    $isExam        = $lockType === 'exam';

    $bannerBorder = $isExam ? 'border-[#fcd34d] bg-[#fffbeb]' : 'border-[#fda4af] bg-[#fff1f2]';
    $iconWrap     = $isExam ? 'bg-[#fef3c7] text-[#d97706]'  : 'bg-[#ffe4e6] text-[#e11d48]';
    $titleColor   = $isExam ? 'text-[#92400e]'               : 'text-[#9f1239]';
    $bodyColor    = $isExam ? 'text-[#92400e]'               : 'text-[#9f1239]';
    $dotColor     = $isExam ? 'bg-[#f59e0b]'                 : 'bg-[#f43f5e]';
@endphp

{{-- Lock banner --}}
<div class="rounded-xl border {{ $bannerBorder }} p-4 mb-4">
    <div class="flex items-start gap-3">
        <span class="flex items-center justify-center w-9 h-9 rounded-lg shrink-0 {{ $iconWrap }}">
            <i class="bx {{ $isExam ? 'bx-clipboard' : 'bx-lock-alt' }} text-lg leading-none"></i>
        </span>
        <div class="flex-1 min-w-0">
            <p class="text-[13px] font-bold {{ $titleColor }} mb-0.5">{{ $lockLabel }}</p>
            <p class="text-[13px] {{ $bodyColor }} leading-relaxed">
                This week contains a
                <strong>{{ $isExam ? 'scheduled exam' : 'non-teaching class' }}</strong>
                in the academic calendar. Coverage details cannot be entered.
            </p>
            @if (count($lockingEvents) > 0)
                <ul class="mt-2.5 space-y-1.5">
                    @foreach ($lockingEvents as $ev)
                        <li class="flex items-center gap-2 text-[13px] {{ $bodyColor }}">
                            <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $dotColor }}"></span>
                            <span class="font-medium">{{ $ev['name'] }}</span>
                            <span class="opacity-60">— {{ $ev['date_display'] }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>

{{-- Other events --}}
@if (count($otherEvents) > 0)
    <div class="rounded-xl border border-[#e2e8f0] bg-[#f8fafc] p-4">
        <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-2.5 flex items-center gap-1.5">
            <i class="bx bx-calendar-event text-[#94a3b8]"></i> Other events this week
        </p>
        <ul class="space-y-1.5">
            @foreach ($otherEvents as $ev)
                <li class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full shrink-0 bg-[#94a3b8]"></span>
                    <span class="text-[13px] font-medium text-[#0f172a]">{{ $ev['name'] }}</span>
                    <span class="text-[13px] text-[#94a3b8]">— {{ $ev['date_display'] }}</span>
                    <span class="ml-auto text-[11px] font-semibold px-2 py-0.5 rounded-full bg-[#e2e8f0] text-[#475569]">
                        {{ str_replace('_', ' ', ucfirst($ev['type'])) }}
                    </span>
                </li>
            @endforeach
        </ul>
    </div>
@endif
