@props([
    'class'  => '',
    'padded' => true,
])

<div {{ $attributes->merge([
    'class' => "flex-1 min-h-0 overflow-y-auto " . ($padded ? 'p-6' : '') . " $class"
]) }}>
    {{ $slot }}
</div>
