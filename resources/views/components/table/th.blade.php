@props(['align' => 'left', 'class' => ''])

@php
    $alignClass = match ($align) {
        'center' => 'text-center',
        'right'  => 'text-right',
        default  => 'text-left',
    };
@endphp

<th {{ $attributes->merge(['class' => "px-4 py-2.5 text-[11px] font-bold uppercase tracking-[0.08em] text-[#72809E] $alignClass $class"]) }}>
    {{ $slot }}
</th>
