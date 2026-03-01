{{-- @props([
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
</header> --}}

{{--
Usage: <x-modal.header>Modal Title</x-modal.header>
       <x-modal.header class="bg-blue-50">
            <h3 class="text-lg font-semibold">Edit User</h3>
        </x-modal.header>

--}}

@props([
    'class'   => '',
    'modalId' => null,
])

@php
    $baseClasses = 'px-6 py-4 border-b border-slate-200 flex-shrink-0 bg-white';
    $finalClasses = $class ? "{$baseClasses} {$class}" : $baseClasses;
@endphp

<header {{ $attributes->merge(['class' => $finalClasses]) }}>
    <div class="flex items-center justify-between gap-4">
        {{-- Title slot — callers put their title text/markup here --}}
        <div class="flex-1 font-semibold text-slate-800">
            {{ $slot }}
        </div>

        {{-- Close button — rendered by the header so callers don't need to add it --}}
        @if ($modalId)
            <button
                type="button"
                onclick="document.getElementById('{{ $modalId }}').close()"
                class="shrink-0 rounded-lg p-1.5 text-slate-400
                       hover:bg-slate-100 hover:text-slate-600
                       transition-colors duration-150"
                aria-label="Close">
                <i class="bx bx-x text-xl leading-none"></i>
            </button>
        @endif
    </div>
</header>

{{--
USAGE:
<x-modal.header modalId="myModal">Edit Goal</x-modal.header>

Do NOT add <x-modal.x-button> inside the slot — the header already renders it.
--}}
