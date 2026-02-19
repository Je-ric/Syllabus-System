@props([
    'min' => null,
    'max' => null,
])

{{-- Compatibility alias. Prefer: <x-form.input type="date" ... /> --}}
<x-form.input
    type="date"
    :min="$min"
    :max="$max"
    {{ $attributes }}
/>
