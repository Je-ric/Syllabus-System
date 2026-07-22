@props([
    'for'        => null,
    'isRequired' => false,
    'icon'       => null,
])

{{-- Form field label inside modals — Charcoal 500 text, Emerald 700 icon --}}
<label
    @if($for) for="{{ $for }}" @endif
    {{ $attributes->class([
        'flex items-center gap-1.5',
        'text-[11px] font-bold uppercase tracking-[0.12em] text-[#394056]',
        'mb-1.5 leading-none',
    ]) }}
>
    @if($icon)
        <i class="bx {{ $icon }} text-[#00965F] text-sm leading-none shrink-0"></i>
    @endif

    {{ $slot }}

    @if($isRequired)
        <span class="text-[#E52F28] font-bold text-xs leading-none ml-0.5">*</span>
    @endif
</label>
