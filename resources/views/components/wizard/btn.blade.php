@props([
    'variant' => 'primary',
    'href'    => null,
    'type'    => 'button',
    'loading' => null,
])

@php
    // ─── Style map ───────────────────────────────────────────────────────────
    $sm = 'inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-lg
            transition-colors duration-150 disabled:opacity-50 disabled:pointer-events-none ';

    $styles = [
        // Full-size CLSU-branded
        'primary'    => $sm . 'bg-[linear-gradient(90deg,#003a10_0%,#009639_100%)] text-white hover:brightness-110 focus:ring-[#009639]/30',
        // Small inline
        'sm-primary' => $sm . 'bg-emerald-50 text-emerald-700 border border-emerald-300 hover:bg-emerald-50 hover:border-emerald-400',
        'sm-cancel'  => $sm . 'bg-white text-slate-700 border border-slate-300 hover:bg-slate-50 hover:border-slate-400',
        'sm-danger'  => $sm . 'bg-rose-600 text-white hover:bg-rose-700',
        'sm-warning' => $sm . 'bg-amber-50 text-amber-700 border border-amber-300 hover:bg-amber-100',
        'sm-info'    => $sm . 'bg-blue-50 text-blue-700 border border-blue-300 hover:bg-blue-100',
        'sm-success' => $sm . 'bg-emerald-50 text-emerald-700 border border-emerald-300 hover:bg-emerald-100',
        'sm-soft'    => $sm . 'bg-lime-50 text-lime-700 border border-lime-300 hover:bg-lime-100',
    ];

    $class = $styles[$variant] ?? $styles['primary'];

    $wireTarget = $loading ? ($attributes->get('wire:target') ?? '') : '';
@endphp

{{-- ─── Link ────────────────────────────────────────────────────────────── --}}
@if ($href)

    <a href="{{ $href }}" {{ $attributes->class([$class]) }}>{{ $slot }}</a>

{{-- ─── Button with built-in loading/spinner states ───────────────────── --}}
@elseif ($loading)

    <button
        type="{{ $type }}"
        wire:loading.attr="disabled"
        {{ $attributes->class([$class]) }}>

        @if ($wireTarget)
            <span wire:loading.remove wire:target="{{ $wireTarget }}">{{ $slot }}</span>
            <span wire:loading wire:target="{{ $wireTarget }}"
                class="inline-flex items-center gap-1.5">
                <svg class="animate-spin h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10"
                        stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                {{ $loading }}
            </span>
        @else
            {{-- No wire:target — Livewire shows spinner for ANY in-flight request --}}
            <span wire:loading.remove>{{ $slot }}</span>
            <span wire:loading class="inline-flex items-center gap-1.5">
                <svg class="animate-spin h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10"
                        stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                {{ $loading }}
            </span>
        @endif

    </button>

{{-- ─── Plain button ─────────────────────────────────────────────────── --}}
@else

    <button type="{{ $type }}" {{ $attributes->class([$class]) }}>
        {{ $slot }}
    </button>

@endif
