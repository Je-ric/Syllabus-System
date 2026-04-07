@props(['align' => 'left', 'class' => ''])

@php
    $alignClass = match ($align) {
        'center' => 'text-center',
        'right'  => 'text-right',
        default  => 'text-left',
    };
@endphp

<td {{ $attributes->merge(['class' => "px-4 py-3 text-[13px] text-[#0f172a] $alignClass $class"]) }}>
    {{ $slot }}
</td>
