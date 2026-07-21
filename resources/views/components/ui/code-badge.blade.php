@props(['variant' => 'emerald'])

@php
    // Light bg + border-matching text color — pill style
    // bg = Emerald 100-200, text = border = darker step (700-800)
    $styles = [
        'emerald' => 'text-[#076042] bg-[#D5FFF0] border-[#076042]',    // text = border = Emerald 900
        'blue'    => 'text-[#143D57] bg-[#DAF1FF] border-[#143D57]',    // text = border = Blue 900
        'amber'   => 'text-[#875200] bg-[#FFF6E2] border-[#875200]',    // text = border = Yellow 900
        'red'     => 'text-[#731814] bg-[#FFE3E2] border-[#731814]',    // text = border = Red 900
        'grey'    => 'text-[#394056] bg-[#F1F3F5] border-[#394056]',    // text = border = Charcoal 500
    ];
    $cls = $styles[$variant] ?? $styles['emerald'];
@endphp

<span {{ $attributes->class([
    'inline-flex items-center font-mono text-[11.5px] font-bold px-2.5 py-[3px] rounded-[6px] border whitespace-nowrap tracking-tight',
    $cls,
]) }}>{{ $slot }}</span>
