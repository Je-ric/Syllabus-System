@props(['variant' => 'emerald'])

@php
    $styles = [
        'emerald' => 'text-[#166534] bg-[#dcfce7] border-[#86efac]',
        'blue'    => 'text-[#1e40af] bg-[#dbeafe] border-[#93c5fd]',
        'amber'   => 'text-[#92400e] bg-[#fef3c7] border-[#fcd34d]',
    ];
    $cls = $styles[$variant] ?? $styles['emerald'];
@endphp

<span {{ $attributes->class([
    'inline-flex items-center font-mono text-[12.5px] font-bold px-2.5 py-1 rounded-lg border whitespace-nowrap',
    $cls,
]) }}>{{ $slot }}</span>
