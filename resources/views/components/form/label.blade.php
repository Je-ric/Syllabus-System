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

{{--
    x-form.label
    ─────────────────────────────────────────────────────────────────────
    Compact uppercase label. Pair with x-form.field for automatic
    label + input + error grouping, or use standalone.

    USAGE:
      <x-form.label for="name">Full Name</x-form.label>
      <x-form.label for="email" :isRequired="true">Email</x-form.label>
      <x-form.label for="date" variant="date">Date</x-form.label>
--}}
<label
    @if($for) for="{{ $for }}" @endif
    {{ $attributes->class([
        'inline-flex items-center gap-1.5',
        'text-xs font-semibold uppercase tracking-[0.12em] text-slate-500',
        'mb-1 leading-none',
    ]) }}
>
    @if($icon)
        <i class="bx {{ $icon }} text-emerald-600 text-sm leading-none shrink-0"></i>
    @endif

    {{ $slot }}

    @if($isRequired)
        <span class="text-rose-500 font-bold text-xs leading-none ml-0.5">*</span>
    @endif
</label>
