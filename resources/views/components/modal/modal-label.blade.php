@props([
    'for'        => null,
    'isRequired' => false,
    'icon'       => null,
])

<label
    @if($for) for="{{ $for }}" @endif
    {{ $attributes->class([
        'flex items-center gap-1.5',
        'text-[11px] font-bold uppercase tracking-[0.14em] text-[#334155]',
        'mb-2 leading-none',
    ]) }}
>
    @if($icon)
        <i class="bx {{ $icon }} text-[#16a34a] text-sm leading-none shrink-0"></i>
    @endif

    {{ $slot }}

    @if($isRequired)
        <span class="text-rose-500 font-bold text-xs leading-none ml-0.5">*</span>
    @endif
</label>
