@props(['level'])

@php
    // Light bg + saturated text, ring one step lighter than text (not equal to it) —
    // consistent with status-indicator and alert components.
    // I → Blue    (Introductory)
    // E → Amber   (Enabling)
    // D → Emerald (Demonstrating)
    $colors = [
        'I' => 'bg-[#DAF1FF] text-[#143D57] ring-1 ring-inset ring-[#AEDFFF]',
        'E' => 'bg-[#FFF6E2] text-[#875200] ring-1 ring-inset ring-[#FFE9B5]',
        'D' => 'bg-[#D5FFF0] text-[#06754E] ring-1 ring-inset ring-[#AEFFE2]',
    ];
    $classes = $colors[$level] ?? 'bg-[#F1F3F5] text-[#394056] ring-1 ring-inset ring-[#E3E8EB]';
@endphp

<span class="inline-flex items-center justify-center w-7 px-2 py-[3px]
            text-[0.65rem] font-bold rounded-[7px] tracking-wide {{ $classes }}">
    {{ $level ?? '–' }}
</span>

{{--
x-feedback-status.ied-badge
─────────────────────────────────────────────────────────────────
Displays an IED (Introductory / Enabling / Demonstrating) level badge.
Used in course-outcome mapping tables.

Colors (light bg + saturated text, ring one step lighter than text):
    I → Blue    (Introductory)
    E → Amber   (Enabling)
    D → Emerald (Demonstrating)
    – → Slate   (no mapping / fallback)

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
