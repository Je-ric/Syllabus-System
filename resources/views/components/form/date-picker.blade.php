@props([
    'min'       => null,
    'max'       => null,
    'value'     => '',
    'wireModel' => null,
])

@php
    $wm = $wireModel
        ?? $attributes->whereStartsWith('wire:model')->first()
        ?? null;

    $passthrough = $attributes->whereDoesntStartWith('wire:model');
@endphp

<input
    type="date"
    @if($wm) wire:model.blur="{{ $wm }}" @endif
    value="{{ $value }}"
    @if($min) min="{{ $min }}" @endif
    @if($max) max="{{ $max }}" @endif
    {{ $passthrough->merge([
        'class' => '
            w-full rounded-[14px] border border-[#d4d4d8] bg-white
            px-3 py-2 text-[13px] text-[#09090b]
            hover:border-[#a1a1aa]
            focus:border-[#16a34a] focus:outline-none
            disabled:bg-[#f4f4f5] disabled:text-[#a1a1aa] disabled:cursor-not-allowed
            transition-colors duration-150
        ',
    ]) }}
    style="box-shadow: none;"
    onfocus="this.style.boxShadow='0 0 0 3px rgba(22,163,74,0.15)'"
    onblur="this.style.boxShadow='none'"
/>
