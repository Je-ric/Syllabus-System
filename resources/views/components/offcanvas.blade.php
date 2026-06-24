@props([
    'title'    => '',
    'subtitle' => '',
    'icon'     => null,
    'open'     => 'open',
    'width'    => 'max-w-sm',  // 'max-w-sm' | 'max-w-md' | 'max-w-lg' | 'max-w-[520px]' etc.
    'position' => 'right',     // 'right' | 'left'
])

{{--
    Usage:
    <div x-data="{ myPanel: false }">
        <button x-on:click="myPanel = true">Open</button>
        <x-offcanvas title="My Panel" open="myPanel">
            content here
            <x-slot name="footer">
                <button>Save</button>
            </x-slot>
        </x-offcanvas>
    </div>
--}}

{{-- Backdrop --}}
<div
    x-show="{{ $open }}"
    x-cloak
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    x-on:click="{{ $open }} = false"
    class="fixed inset-0 bg-black/40 backdrop-blur-[2px] z-40"
    aria-hidden="true">
</div>

{{-- Panel --}}
<div
    x-show="{{ $open }}"
    x-cloak
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 {{ $position === 'left' ? '-translate-x-4' : 'translate-x-4' }}"
    x-transition:enter-end="opacity-100 translate-x-0"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 translate-x-0"
    x-transition:leave-end="opacity-0 {{ $position === 'left' ? '-translate-x-4' : 'translate-x-4' }}"
    x-on:keydown.escape.window="{{ $open }} = false"
    role="dialog"
    aria-modal="true"
    {{ $attributes->merge([
        'class' =>
            'fixed inset-y-0 z-50 flex flex-col bg-white shadow-2xl ring-1 ring-black/[0.06] w-full ' .
            $width . ' ' .
            ($position === 'left'
                ? 'left-0'
                : 'right-0')
    ]) }}>

    {{-- ── Header ─────────────────────────────────────────────────── --}}
    <div class="shrink-0 flex items-center justify-between gap-4
                px-5 py-4
                border-b border-slate-100">

        <div class="flex items-center gap-3 min-w-0">

            @if ($icon)
                <span class="shrink-0 flex items-center justify-center
                             w-8 h-8 rounded-xl
                             bg-emerald-50 text-emerald-600
                             ring-1 ring-inset ring-emerald-100">
                    <i class="bx {{ $icon }} text-[16px] leading-none"></i>
                </span>
            @endif

            <div class="min-w-0">
                @if ($title)
                    <p class="text-[13px] font-semibold text-slate-800 truncate leading-tight">
                        {{ $title }}
                    </p>
                @endif
                @if ($subtitle)
                    <p class="text-[11px] text-slate-400 mt-0.5 truncate">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>

        </div>

        <button
            type="button"
            x-on:click="{{ $open }} = false"
            class="shrink-0 flex items-center justify-center
                   w-7 h-7 rounded-lg
                   text-slate-400 hover:text-slate-700 hover:bg-slate-100
                   transition-colors duration-150"
            aria-label="Close panel">
            <i class="bx bx-x text-[18px] leading-none"></i>
        </button>

    </div>

    {{-- ── Body ────────────────────────────────────────────────────── --}}
    <div class="flex-1 overflow-y-auto overscroll-contain px-5 py-4">
        {{ $slot }}
    </div>

    {{-- ── Footer (optional) ──────────────────────────────────────── --}}
    @if (isset($footer))
        <div class="shrink-0 px-5 py-3.5 border-t border-slate-100 bg-slate-50/70">
            {{ $footer }}
        </div>
    @endif

</div>