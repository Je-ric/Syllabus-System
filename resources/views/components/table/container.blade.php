@props([
    'class' => '',
])

<div {{ $attributes->merge(['class' => "overflow-x-auto rounded-2xl border border-slate-200/80 bg-white/90 shadow-sm $class"]) }}>
    {{ $slot }}
</div>
