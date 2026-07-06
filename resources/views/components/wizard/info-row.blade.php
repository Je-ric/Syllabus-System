@props([
    'label',
    'value' => null,
    'muted' => false,
    'bold'  => false,
])

<div class="flex items-start justify-between gap-4 py-1.5 border-b border-[#e4e4e7]/60 last:border-0">

    <span class="text-[11px] font-medium text-[#71717a] shrink-0">
        {{ $label }}
    </span>

    <span @class([
        'text-[11px] text-right',
        'font-semibold text-[#09090b]' => $bold && $value,
        'font-medium text-[#18181b]'   => ! $bold && $value && ! $muted,
        'text-[#a1a1aa]'               => $muted && $value,
        'text-[#d4d4d8] italic'        => ! $value,
    ])>
        {{ $value ?? '—' }}
    </span>

</div>
