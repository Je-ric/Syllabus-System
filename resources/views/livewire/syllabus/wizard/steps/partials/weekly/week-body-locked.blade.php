{{-- weekly-partials/week-body-locked.blade.php --}}

@php
    $lockingEvents = array_filter($events, fn($ev) => in_array($ev['type'], ['exam', 'non_teaching'], true));
    $otherEvents   = array_filter($events, fn($ev) => !in_array($ev['type'], ['exam', 'non_teaching'], true));
    $isExam        = $lockType === 'exam';
@endphp

{{-- Lock banner --}}
<div class="rounded-[12px] border px-4 py-4 mb-3
    {{ $isExam ? 'border-[#fde68a] bg-[#fffbeb]' : 'border-[#fecdd3] bg-[#fff1f2]' }}">

    <div class="flex items-start gap-3">
        <span class="shrink-0 flex items-center justify-center w-9 h-9 rounded-[10px]
            {{ $isExam ? 'bg-[#fef3c7] text-[#d97706]' : 'bg-[#ffe4e6] text-[#e11d48]' }}">
            <i class="bx {{ $isExam ? 'bx-clipboard' : 'bx-lock-alt' }} text-base leading-none"></i>
        </span>

        <div class="flex-1 min-w-0">
            <p class="text-[11px] font-bold uppercase tracking-[0.1em] mb-1
                {{ $isExam ? 'text-[#92400e]' : 'text-[#9f1239]' }}">
                {{ $lockLabel }}
            </p>
            <p class="text-[13px] leading-relaxed
                {{ $isExam ? 'text-[#78350f]' : 'text-[#881337]' }}">
                This week contains a
                <strong>{{ $isExam ? 'scheduled exam' : 'non-teaching period' }}</strong>
                in the academic calendar — coverage details are disabled.
            </p>

            @if (count($lockingEvents) > 0)
                <ul class="mt-3 space-y-1.5">
                    @foreach ($lockingEvents as $ev)
                        <li class="flex items-center gap-2 text-[13px]
                            {{ $isExam ? 'text-[#78350f]' : 'text-[#881337]' }}">
                            <span class="w-1 h-1 rounded-full shrink-0
                                {{ $isExam ? 'bg-[#d97706]' : 'bg-[#e11d48]' }}"></span>
                            <span class="font-semibold">{{ $ev['name'] }}</span>
                            <span class="opacity-40">·</span>
                            <span class="opacity-60 text-[11px]">{{ $ev['date_display'] }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>

{{-- Other events --}}
@if (count($otherEvents) > 0)
    <div class="rounded-[12px] border border-[#e4e4e7] bg-white px-4 py-3.5">
        <p class="text-[11px] font-bold uppercase tracking-[0.1em] text-[#a1a1aa] mb-2.5 flex items-center gap-1.5">
            <i class="bx bx-calendar-event text-[#d4d4d8] text-sm"></i> Other events this week
        </p>
        <ul class="space-y-2">
            @foreach ($otherEvents as $ev)
                <li class="flex items-center gap-2.5 text-[13px]">
                    <span class="w-1.5 h-1.5 rounded-full shrink-0 bg-[#d4d4d8]"></span>
                    <span class="font-medium text-[#18181b]">{{ $ev['name'] }}</span>
                    <span class="text-[#d4d4d8]">·</span>
                    <span class="text-[#a1a1aa] text-[11px]">{{ $ev['date_display'] }}</span>
                    <span class="ml-auto shrink-0 text-[11px] font-semibold px-2 py-0.5 rounded-full
                                 bg-[#f4f4f5] text-[#71717a] border border-[#e4e4e7] uppercase tracking-wide">
                        {{ str_replace('_', ' ', $ev['type']) }}
                    </span>
                </li>
            @endforeach
        </ul>
    </div>
@endif
