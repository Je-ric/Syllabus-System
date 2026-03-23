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
            w-full rounded-lg border border-slate-300 bg-white
            px-3 py-2 text-sm text-slate-700 shadow-sm
            hover:border-slate-400
            focus:border-emerald-400 focus:ring-1 focus:ring-emerald-300 focus:outline-none
            disabled:bg-slate-50 disabled:text-slate-400 disabled:cursor-not-allowed
            transition-colors duration-150
        ',
    ]) }}
/>
