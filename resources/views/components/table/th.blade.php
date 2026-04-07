@props(['align' => 'left', 'class' => ''])

@php
    $alignClass = match ($align) {
        'center' => 'text-center',
        'right'  => 'text-right',
        default  => 'text-left',
    };
@endphp

<th {{ $attributes->merge(['class' => "px-4 py-3 text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] $alignClass $class"]) }}>
    {{ $slot }}
</th>
