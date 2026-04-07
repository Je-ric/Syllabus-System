@props([
    'class' => '',
])

<table {{ $attributes->merge(['class' => "min-w-full text-sm $class"]) }}>
    {{ $slot }}
</table>
