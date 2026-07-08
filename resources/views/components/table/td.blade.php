@props(['align' => 'left', 'class' => ''])

@php
    $alignClass = match ($align) {
        'center' => 'text-center',
        'right'  => 'text-right',
        default  => 'text-left',
    };
@endphp

<td {{ $attributes->merge(['class' => "px-4 py-3.5 text-[13.5px] text-[#1a1a1a] $alignClass $class"]) }}>
    {{ $slot }}
</td>
