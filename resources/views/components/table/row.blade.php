@props([
    'striped' => false,
    'hover' => false,
    'class' => '',
])

@php
    $stripedClass = $striped ? 'odd:bg-white even:bg-slate-50' : '';
    $hoverClass = $hover ? 'hover:bg-emerald-50/60 transition-colors' : '';
@endphp

<tr {{ $attributes->merge(['class' => "$stripedClass $hoverClass $class"]) }}>
    {{ $slot }}
</tr>
