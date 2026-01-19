@props([
    'id' => 'modal', // unique id for the modal
    'title' => 'Modal Title',
    'size' => 'md', // md, lg, xl
])

@php
    $sizeClasses = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
    ];
@endphp

<!-- Overlay -->
<div x-data="{ open: false }" x-show="open" x-transition.opacity
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" id="{{ $id }}"
    style="display: none;">

    <!-- Modal Box -->
    <div x-show="open" x-transition.scale
        class="bg-white rounded-lg shadow-lg w-full {{ $sizeClasses[$size] ?? 'max-w-md' }}">

        <!-- Header -->
        <div class="flex justify-between items-center border-b px-6 py-4">
            <h2 class="text-lg font-semibold">{{ $title }}</h2>
            <button @click="open=false" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>

        <!-- Body -->
        <div class="p-6">
            {{ $slot }}
        </div>

        <!-- Footer (optional) -->
        @isset($footer)
            <div class="flex justify-end border-t px-6 py-4 space-x-2">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>
