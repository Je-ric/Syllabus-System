@props([
    'align' => 'left',
    'class' => '',
])

@php
    $alignClass = match ($align) {
        'center' => 'text-center',
        'right' => 'text-right',
        default => 'text-left',
    };
@endphp

<th {{ $attributes->merge(['class' => "border border-slate-200 px-4 py-3 text-xs uppercase tracking-[0.2em] font-semibold $alignClass $class"]) }}>
    {{ $slot }}
</th>
