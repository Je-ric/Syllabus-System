@props([
    'class' => '',
])

@php
    $baseClasses = 'px-6 py-4 border-b border-slate-200 flex-shrink-0';
    $finalClasses = $class ? "{$baseClasses} {$class}" : $baseClasses;
@endphp

<header {{ $attributes->merge(['class' => $finalClasses]) }}>
    <div class="flex items-center justify-between gap-4">
        <div class="flex-1">
            {{ $slot }}
        </div>
        <x-modal.x-button />
    </div>
</header>

{{--
Usage: <x-modal.header>Modal Title</x-modal.header>
       <x-modal.header class="bg-blue-50">
            <h3 class="text-lg font-semibold">Edit User</h3>
        </x-modal.header>

--}}
