{{-- weekly-partials/week-body-locked.blade.php --}}

@php
    $lockingEvents = array_filter($events, fn ($ev) => in_array($ev['type'], ['exam', 'non_teaching'], true));
    $otherEvents   = array_filter($events, fn ($ev) => !in_array($ev['type'], ['exam', 'non_teaching'], true));
    $isExam        = $lockType === 'exam';
@endphp

{{-- Lock banner --}}
<div class="rounded-xl border px-4 py-4 mb-3
    {{ $isExam
        ? 'border-amber-200 bg-amber-50/60'
        : 'border-rose-200 bg-rose-50/60' }}">

    <div class="flex items-start gap-3">

        {{-- Icon --}}
        <span class="shrink-0 flex items-center justify-center w-8 h-8 rounded-lg
            {{ $isExam
                ? 'bg-amber-100 text-amber-600'
                : 'bg-rose-100 text-rose-500' }}">
            <i class="bx {{ $isExam ? 'bx-clipboard' : 'bx-lock-alt' }} text-[16px] leading-none"></i>
        </span>

        <div class="flex-1 min-w-0">

            <p class="text-[12px] font-bold uppercase tracking-[0.12em] mb-0.5
                {{ $isExam ? 'text-amber-700' : 'text-rose-700' }}">
                {{ $lockLabel }}
            </p>

            <p class="text-[13px] leading-relaxed
                {{ $isExam ? 'text-amber-800/80' : 'text-rose-800/80' }}">
                This week contains a
                <strong>{{ $isExam ? 'scheduled exam' : 'non-teaching period' }}</strong>
                in the academic calendar — coverage details are disabled.
            </p>

            {{-- Locking events list --}}
            @if (count($lockingEvents) > 0)
                <ul class="mt-3 space-y-1.5">
                    @foreach ($lockingEvents as $ev)
                        <li class="flex items-center gap-2 text-[12px]
                            {{ $isExam ? 'text-amber-800' : 'text-rose-800' }}">
                            <span class="w-1 h-1 rounded-full shrink-0
                                {{ $isExam ? 'bg-amber-400' : 'bg-rose-400' }}">
                            </span>
                            <span class="font-semibold">{{ $ev['name'] }}</span>
                            <span class="opacity-50">·</span>
                            <span class="opacity-70">{{ $ev['date_display'] }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif

        </div>

    </div>

</div>

{{-- Other events this week --}}
@if (count($otherEvents) > 0)
    <div class="rounded-xl border border-slate-200 bg-white px-4 py-3.5">

        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400 mb-2.5 flex items-center gap-1.5">
            <i class="bx bx-calendar-event text-slate-300 text-[13px]"></i>
            Other events this week
        </p>

        <ul class="space-y-2">
            @foreach ($otherEvents as $ev)
                <li class="flex items-center gap-2.5 text-[13px]">

                    <span class="w-1.5 h-1.5 rounded-full shrink-0 bg-slate-300"></span>

                    <span class="font-medium text-slate-700">{{ $ev['name'] }}</span>

                    <span class="text-slate-400">·</span>

                    <span class="text-slate-400 text-[12px]">{{ $ev['date_display'] }}</span>

                    <span class="ml-auto shrink-0 text-[10px] font-semibold px-2 py-0.5 rounded-full
                                 bg-slate-100 text-slate-500 uppercase tracking-wide">
                        {{ str_replace('_', ' ', $ev['type']) }}
                    </span>

                </li>
            @endforeach
        </ul>

    </div>
@endif