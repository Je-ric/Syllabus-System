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

<td {{ $attributes->merge(['class' => "border border-slate-200 px-4 py-3 $alignClass $class"]) }}>
    {{ $slot }}
</td>
