@props(['align' => 'end'])

@php
    $alignClass = match($align) {
        'start'  => 'justify-start',
        'center' => 'justify-center',
        default  => 'justify-end',
    };
@endphp

<footer {{ $attributes->class([
    'border-t border-[#e2e8f0] bg-[#f8fafc] px-6 py-4 flex gap-3 flex-shrink-0',
    $alignClass,
]) }}>
    {{ $slot }}
</footer>
