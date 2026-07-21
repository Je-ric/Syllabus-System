@props(['level'])

@php
    // Light bg + text = border (pill pattern, consistent with design system)
    // I → Blue 200 bg, Blue 900 text+border
    // E → Amber 200 bg, Amber 900 text+border
    // D → Emerald 100 bg, Emerald 800 text+border
    $colors = [
        'I' => 'bg-[#DAF1FF] text-[#143D57] ring-1 ring-inset ring-[#143D57]',
        'E' => 'bg-[#FFF6E2] text-[#875200] ring-1 ring-inset ring-[#875200]',
        'D' => 'bg-[#D5FFF0] text-[#06754E] ring-1 ring-inset ring-[#06754E]',
    ];
    $classes = $colors[$level] ?? 'bg-[#F1F3F5] text-[#394056] ring-1 ring-inset ring-[#394056]';
@endphp

<span class="inline-flex items-center justify-center px-2 py-0.5 w-7
            text-[0.65rem] font-bold rounded-full tracking-wide {{ $classes }}">
    {{ $level ?? '–' }}
</span>

{{--
x-feedback-status.ied-badge
─────────────────────────────────────────────────────────────────
Displays an IED (Introductory / Enabling / Demonstrating) level badge.
Used in course-outcome mapping tables.

Colors (light bg + text = border, pill style):
    I → Blue   (Introductory)
    E → Amber  (Enabling)
    D → Emerald (Demonstrating)
    – → Slate  (no mapping / fallback)

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
