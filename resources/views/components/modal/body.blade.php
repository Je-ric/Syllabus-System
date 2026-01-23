@props([
    'class' => '',
    'padded' => true,
])

@php
    $paddingClass = $padded ? 'p-6' : '';
@endphp

<div {{ $attributes->merge([
    'class' => "flex-1 min-h-0 overflow-y-auto space-y-6 max-h-[60vh] sm:max-h-[70vh] custom-scrollbar-gold $paddingClass $class"
]) }}>
    {{ $slot }}
</div>

{{--
Usage:
<x-modal.body>
    ...content...
</x-modal.body>
<x-modal.body :padded="false">
    ...content without padding...
</x-modal.body>

--}}
