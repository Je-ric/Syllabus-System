{{--
    Partial: weekly-partials/week-body-locked.blade.php
    Shows lock reason, locking events, and all other events for the week.
--}}

@php
    $lockingEvents = array_filter($events, fn ($ev) => in_array($ev['type'], ['exam', 'non_teaching'], true));
    $otherEvents   = array_filter($events, fn ($ev) => ! in_array($ev['type'], ['exam', 'non_teaching'], true));
    $isExam        = $lockType === 'exam';
@endphp

{{-- Primary lock banner --}}
<div class="rounded-xl border {{ $isExam ? 'border-amber-300 bg-amber-50' : 'border-rose-200 bg-rose-50' }} p-4 mb-4">
    <div class="flex items-start gap-3">
        <span class="flex items-center justify-center w-9 h-9 rounded-lg shrink-0
                     {{ $isExam ? 'bg-amber-100 text-amber-600' : 'bg-rose-100 text-rose-600' }}">
            <i class="bx {{ $isExam ? 'bx-clipboard' : 'bx-lock-alt' }} text-lg leading-none"></i>
        </span>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-bold {{ $isExam ? 'text-amber-800' : 'text-rose-800' }} mb-0.5">
                {{ $lockLabel }}
            </p>
            <p class="text-xs {{ $isExam ? 'text-amber-700' : 'text-rose-700' }} leading-relaxed">
                This week contains a
                <strong>{{ $isExam ? 'scheduled exam' : 'non-teaching class' }}</strong>
                in the academic calendar. Coverage details cannot be entered.
            </p>

            @if (count($lockingEvents) > 0)
                <ul class="mt-2.5 space-y-1.5">
                    @foreach ($lockingEvents as $ev)
                        <li class="flex items-center gap-2 text-xs {{ $isExam ? 'text-amber-800' : 'text-rose-800' }}">
                            <span class="w-1.5 h-1.5 rounded-full shrink-0
                                         {{ $isExam ? 'bg-amber-500' : 'bg-rose-500' }}"></span>
                            <span class="font-medium">{{ $ev['name'] }}</span>
                            <span class="opacity-60">— {{ $ev['date_display'] }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>

{{-- Other events this week (holidays, breaks, etc.) --}}
@if (count($otherEvents) > 0)
    <div class="rounded-xl border border-emerald-200 bg-emerald-50/60 p-4">
        <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-700 mb-2.5 flex items-center gap-1.5">
            <i class="bx bx-calendar-event text-emerald-500"></i> Other events this week
        </p>
        <ul class="space-y-1.5">
            @foreach ($otherEvents as $ev)
                @php
                    $dotColor = match ($ev['type']) {
                        'holiday' => 'bg-emerald-500',
                        'break'   => 'bg-blue-500',
                        default   => 'bg-amber-500',
                    };
                    $textColor = match ($ev['type']) {
                        'holiday' => 'text-emerald-800',
                        'break'   => 'text-blue-800',
                        default   => 'text-amber-800',
                    };
                @endphp
                <li class="flex items-center gap-2 text-xs {{ $textColor }}">
                    <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $dotColor }}"></span>
                    <span class="font-medium">{{ $ev['name'] }}</span>
                    <span class="opacity-60">— {{ $ev['date_display'] }}</span>
                    <span class="ml-auto px-1.5 py-0.5 rounded-full text-[10px] font-semibold
                                 {{ $ev['type'] === 'holiday' ? 'bg-emerald-100 text-emerald-700' : ($ev['type'] === 'break' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') }}">
                        {{ str_replace('_', ' ', ucfirst($ev['type'])) }}
                    </span>
                </li>
            @endforeach
        </ul>
    </div>
@endif
