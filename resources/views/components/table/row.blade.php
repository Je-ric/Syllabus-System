@props(['striped' => false, 'hover' => false, 'class' => ''])

@php
    $stripedClass = $striped ? 'odd:bg-white even:bg-[#f8fafc]' : '';
    $hoverClass   = $hover   ? 'hover:bg-[#f0fdf4] transition-colors duration-100' : '';
@endphp

<tr {{ $attributes->merge(['class' => "border-b border-[#e2e8f0] last:border-0 $stripedClass $hoverClass $class"]) }}>
    {{ $slot }}
</tr>
