@props([
    'label',
    'value' => null,
    'muted' => false,
    'bold'  => false,
])

<div class="flex items-start justify-between gap-4 py-1.5 border-b border-[#E3E8EB]/70 last:border-0">

    <span class="text-[11px] font-medium text-[#72809E] shrink-0">
        {{ $label }}
    </span>

    <span @class([
        'text-[11px] text-right',
        'font-bold text-[#394056]'   => $bold && $value,
        'font-medium text-[#394056]' => ! $bold && $value && ! $muted,
        'text-[#93A1AF]'             => $muted && $value,
        'text-[#D6DDE3] italic'      => ! $value,
    ])>
        {{ $value ?? '—' }}
    </span>

</div>
