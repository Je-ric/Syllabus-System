@props([
    'sticky' => false,
    'class' => '',
])

@php
    $base = 'bg-emerald-50 text-emerald-800';
    $stickyClass = $sticky ? 'sticky top-0 z-10' : '';
@endphp

<thead {{ $attributes->merge(['class' => "$base $stickyClass $class"]) }}>
    {{ $slot }}
</thead>
