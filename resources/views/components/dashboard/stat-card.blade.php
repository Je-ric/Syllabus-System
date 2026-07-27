@props([
    'label',
    'value',
    'icon'  => 'bx-bar-chart',
    'color' => 'slate',
])

@php
    $palette = [
        'slate'   => ['border' => 'border-[#E3E8EB]', 'bg' => 'bg-[#FAFAFA]', 'icon_bg' => 'bg-[#F1F3F5] text-[#394056]', 'value' => 'text-[#09090b]', 'label' => 'text-[#72809E]'],
        'emerald' => ['border' => 'border-[#AEFFE2]', 'bg' => 'bg-[#F4FFFA]', 'icon_bg' => 'bg-[#D5FFF0] text-[#06754E]', 'value' => 'text-[#06754E]', 'label' => 'text-[#00965F]'],
        'blue'    => ['border' => 'border-[#AEDFFF]', 'bg' => 'bg-[#F5FBFF]', 'icon_bg' => 'bg-[#DAF1FF] text-[#143D57]', 'value' => 'text-[#143D57]', 'label' => 'text-[#3197D6]'],
        'amber'   => ['border' => 'border-[#FFE9B5]', 'bg' => 'bg-[#FFFCF5]', 'icon_bg' => 'bg-[#FFF6E2] text-[#875200]', 'value' => 'text-[#875200]', 'label' => 'text-[#B45309]'],
        'rose'    => ['border' => 'border-[#FFA2A2]', 'bg' => 'bg-[#FFF8F8]', 'icon_bg' => 'bg-[#FFE3E2] text-[#731814]', 'value' => 'text-[#731814]', 'label' => 'text-[#B91C1C]'],
        'violet'  => ['border' => 'border-[#E9D5FF]', 'bg' => 'bg-[#FAF5FF]', 'icon_bg' => 'bg-[#F3EEFF] text-[#4C1D95]', 'value' => 'text-[#4C1D95]', 'label' => 'text-[#6D28D9]'],
    ];
    $p = $palette[$color] ?? $palette['slate'];
@endphp

<div {{ $attributes->class([
    'rounded-[12px] border px-3 py-3 sm:px-4 sm:py-4 text-center min-h-[108px] flex flex-col items-center justify-center',
    $p['border'],
    $p['bg'],
]) }}>
    <span class="inline-flex items-center justify-center w-8 h-8 rounded-[8px] mb-2 {{ $p['icon_bg'] }}">
        <i class="bx {{ $icon }} text-base leading-none"></i>
    </span>
    <p class="text-2xl font-bold leading-none {{ $p['value'] }}">{{ number_format((int) $value) }}</p>
    <p class="text-[10px] font-bold uppercase tracking-widest mt-1.5 {{ $p['label'] }}">{{ $label }}</p>
</div>
