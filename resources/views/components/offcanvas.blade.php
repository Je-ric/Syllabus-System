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
                px-5 py-4 border-b"
         style="background: linear-gradient(135deg, #002a0c 0%, #004d16 100%); border-color: rgba(255,215,0,0.25);">

        <div class="flex items-center gap-3 min-w-0">

            @if ($icon)
                <span class="shrink-0 flex items-center justify-center
                             w-8 h-8 rounded-xl border border-white/20"
                      style="background: rgba(255,255,255,0.15);">
                    <i class="bx {{ $icon }} text-base leading-none text-white"></i>
                </span>
            @endif

            <div class="min-w-0">
                @if ($title)
                    <p class="text-sm font-semibold text-white truncate leading-tight">
                        {{ $title }}
                    </p>
                @endif
                @if ($subtitle)
                    <p class="text-xs mt-0.5 truncate" style="color: rgba(255,255,255,0.6);">
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
                   hover:bg-white/20 transition-colors duration-150
                   text-white/70 hover:text-white"
            aria-label="Close panel">
            <i class="bx bx-x text-lg leading-none"></i>
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