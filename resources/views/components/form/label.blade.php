@props([
    'for'        => null,
    'icon'       => null,
    'isRequired' => false,
    'variant'    => null,
])

@php
    $variantIcons = [
        'title'       => 'bx-book',
        'description' => 'bx-align-left',
        'date'        => 'bx-calendar',
        'year'        => 'bx-time-five',
        'email'       => 'bx-envelope',
        'phone'       => 'bx-phone',
        'user'        => 'bx-user',
        'amount'      => 'bx-money',
        'location'    => 'bx-map-pin',
    ];
    if ($variant && isset($variantIcons[$variant])) {
        $icon = $variantIcons[$variant];
    }
@endphp

<label
    @if($for) for="{{ $for }}" @endif
    {{ $attributes->class([
        'inline-flex items-center gap-1.5',
        'text-[12.5px] font-semibold text-[#3f3f46]',
        'mb-1 leading-none',
    ]) }}
>
    @if($icon)
        <i class="bx {{ $icon }} text-[#16a34a] text-sm leading-none shrink-0"></i>
    @endif

    {{ $slot }}

    @if($isRequired)
        <span class="text-[#e11d48] font-bold text-xs leading-none ml-0.5">*</span>
    @endif
</label>
