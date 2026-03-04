@props([
    'variant' => 'primary',
    'href'    => null,
    'type'    => 'button',
    'loading' => null,
])

@php
    // ─── Style map ────────────────────────────────────────────────────────────
    $full = 'inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl
             shadow-sm transition-all duration-150 focus:ring-2 focus:outline-none
             active:scale-[.97] disabled:opacity-50 disabled:pointer-events-none ';

    $table = 'inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold rounded-lg
              transition-colors duration-150 disabled:opacity-50 disabled:pointer-events-none ';

    $sm = 'inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg
           transition-colors duration-150 disabled:opacity-50 disabled:pointer-events-none ';

    $styles = [
        // Full-size CLSU-branded
        'primary'    => $full . 'bg-[linear-gradient(90deg,#003a10_0%,#009639_100%)] text-white hover:brightness-110 focus:ring-[#009639]/30',
        'save'       => $full . 'bg-[linear-gradient(90deg,#009639_0%,#92d12c_100%)] text-white hover:brightness-110 focus:ring-[#009639]/30',
        'add-button' => $full . 'bg-[linear-gradient(90deg,#003a10_0%,#009639_100%)] text-white hover:brightness-110 focus:ring-[#009639]/30',
        'secondary'  => $full . 'bg-[linear-gradient(90deg,#ffd700_0%,#e0a70d_100%)] text-[#1a5f30] hover:brightness-105 focus:ring-[#e0a70d]/30',
        'soft'       => $full . 'bg-[linear-gradient(90deg,#92d12c_0%,#cdfb13_100%)] text-[#003a10] hover:brightness-105 focus:ring-[#92d12c]/30',
        'outline'    => $full . 'bg-white text-[#1a5f30] border-2 border-[#1a5f30] hover:bg-[#1a5f30] hover:text-white focus:ring-[#1a5f30]/30',
        'cancel'     => $full . 'bg-white text-slate-700 border border-slate-300 hover:bg-slate-50 hover:border-slate-400 focus:ring-slate-300/40',
        'danger'     => $full . 'bg-rose-600 text-white hover:bg-rose-700 focus:ring-rose-500/30',
        // Full-width dashed
        'add-dashed' => 'flex w-full items-center justify-center gap-2 px-4 py-3
                         border-2 border-dashed border-emerald-300 rounded-2xl
                         text-sm font-semibold text-emerald-700
                         hover:border-emerald-500 hover:bg-emerald-50
                         transition-colors duration-150
                         disabled:opacity-50 disabled:pointer-events-none',
        // Table compact
        'table-confirm'  => $table . 'bg-emerald-600 text-white hover:bg-emerald-700',
        'table-edit'     => $table . 'bg-blue-600 text-white hover:bg-blue-700',
        'table-view'     => $table . 'bg-cyan-600 text-white hover:bg-cyan-700',
        'table-manage'   => $table . 'bg-slate-600 text-white hover:bg-slate-700',
        'table-restore'  => $table . 'bg-indigo-600 text-white hover:bg-indigo-700',
        'table-disable'  => $table . 'bg-slate-500 text-white hover:bg-slate-600',
        'table-danger'   => $table . 'bg-rose-600 text-white hover:bg-rose-700',
        'table-cancel'   => $table . 'bg-white text-slate-700 border border-slate-300 hover:bg-slate-50 hover:border-slate-400',
        // Small inline
        'sm-primary' => $sm . 'bg-[linear-gradient(90deg,#003a10_0%,#009639_100%)] text-white hover:brightness-110',
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

        {{--
            Child spans use the pre-extracted plain string $wireTarget,
            NOT $attributes->only() — that is the fix.
        --}}
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
