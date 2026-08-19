@props(['variant' => 'emerald'])

@php
    // Light bg + saturated text, border one step lighter than text — pill style,
    // consistent with status-indicator / ied-badge / alert components.
    $styles = [
        'emerald' => 'text-[#076042] bg-[#D5FFF0] border-[#AEFFE2]',
        'blue'    => 'text-[#143D57] bg-[#DAF1FF] border-[#AEDFFF]',
        'amber'   => 'text-[#875200] bg-[#FFF6E2] border-[#FFE9B5]',
        'red'     => 'text-[#731814] bg-[#FFE3E2] border-[#FFA2A2]',
        'grey'    => 'text-[#394056] bg-[#F1F3F5] border-[#E3E8EB]',
    ];
    $cls = $styles[$variant] ?? $styles['emerald'];
@endphp

<span {{ $attributes->class([
    'inline-flex items-center font-mono text-[11.5px] font-bold px-2.5 py-[3px] rounded-[6px] border whitespace-nowrap tracking-tight',
    $cls,
]) }}>{{ $slot }}</span>