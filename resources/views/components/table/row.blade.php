@props(['striped' => false, 'hover' => false, 'class' => ''])

@php
    $stripedClass = $striped ? 'odd:bg-white even:bg-[#F9FAFA]' : '';
    $hoverClass   = $hover   ? 'hover:bg-[#EDFFF8] transition-colors duration-150' : '';
@endphp

<tr {{ $attributes->merge(['class' => "border-b border-[#E3E8EB] last:border-0 $stripedClass $hoverClass $class"]) }}>
    {{ $slot }}
</tr>
