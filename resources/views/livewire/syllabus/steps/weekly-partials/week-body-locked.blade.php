{{--
    Partial: weekly-partials/week-body-locked.blade.php
    ────────────────────────────────────────────────────
    Rendered when a week is locked (exam or non_teaching).
    Shows a type-appropriate alert and the locking event(s).

    Passed by week-accordion.blade.php:
        $week       SyllabusWeek
        $events     array   All calendar events for this week
        $lockType   string  'exam' | 'non_teaching'
        $lockLabel  string  'Exam Week' | 'Non-Teaching Week'
--}}

{{-- Primary lock alert --}}
<x-feedback-status.alert
    type="{{ $lockType === 'exam' ? 'warning' : 'error' }}"
    :title="$lockLabel"
    class="mb-4">

    This week contains a
    <strong>{{ $lockType === 'exam' ? 'scheduled exam' : 'non-teaching class' }}</strong>
    in the academic calendar. Coverage details cannot be entered.

    @php $lockingEvents = array_filter($events, fn ($ev) => in_array($ev['type'], ['exam', 'non_teaching'], true)); @endphp
    @if (count($lockingEvents) > 0)
        <ul class="mt-2 space-y-0.5">
            @foreach ($lockingEvents as $ev)
                <li class="flex items-center gap-1.5">
                    <i class="bx bx-calendar-event text-xs"></i>
                    {{ $ev['name'] }} — {{ $ev['date_display'] }}
                </li>
            @endforeach
        </ul>
    @endif

</x-feedback-status.alert>

{{-- Other (non-locking) events in the same week --}}
@php $otherEvents = array_filter($events, fn ($ev) => ! in_array($ev['type'], ['exam', 'non_teaching'], true)); @endphp
@if (count($otherEvents) > 0)
    <x-feedback-status.alert type="info" title="Other events this week">
        <ul class="space-y-1 mt-1">
            @foreach ($otherEvents as $ev)
                <li class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400 shrink-0"></span>
                    {{ $ev['name'] }}
                    <span class="opacity-60">({{ $ev['date_display'] }})</span>
                </li>
            @endforeach
        </ul>
    </x-feedback-status.alert>
@endif