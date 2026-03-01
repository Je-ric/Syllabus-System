@props(['level'])

@php
    $colors = [
        // Introductory — blue
        'I' => 'bg-blue-50 text-blue-700 ring-1 ring-blue-200',
        // Enabling — amber (was yellow-*, now consistent with app token)
        'E' => 'bg-amber-50 text-amber-700 ring-1 ring-amber-200',
        // Demonstrating — emerald (was green-*, now consistent with app token)
        'D' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
    ];
    // Fallback: slate (was gray-*, now consistent)
    $classes = $colors[$level] ?? 'bg-slate-50 text-slate-400 ring-1 ring-slate-200';
@endphp

<span class="inline-flex items-center justify-center px-2.5 py-0.5
            text-[0.65rem] font-bold rounded-full tracking-wide {{ $classes }}">
    {{ $level ?? '–' }}
</span>

{{--
x-feedback-status.ied-badge
─────────────────────────────────────────────────────────────────
Displays an IED (Introductory / Enabling / Demonstrating) level badge.
Used in course-outcome mapping tables.

Colors:
    I → blue   (Introductory)
    E → amber  (Enabling)
    D → emerald (Demonstrating)
    – → slate  (no mapping / fallback)

USAGE:
Static:
    <x-feedback-status.ied-badge level="I" />
    <x-feedback-status.ied-badge level="E" />
    <x-feedback-status.ied-badge level="D" />

From a pivot / nullable value:
    <x-feedback-status.ied-badge :level="$mapping?->pivot->ied ?? '–'" />

In a table cell:
    <x-table.td align="center">
        <x-feedback-status.ied-badge :level="$ied" />
    </x-table.td>
--}}
