@props([
    'class' => '',
])

<table {{ $attributes->merge(['class' => "min-w-full border-collapse text-sm $class"]) }}>
    {{ $slot }}
</table>
