@props(['class' => ''])

<table {{ $attributes->merge(['class' => "min-w-full text-sm border-collapse $class"]) }}>
    {{ $slot }}
</table>
