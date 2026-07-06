@props([
    'title'    => '',
    'subtitle' => '',
    'icon'     => null,
    'open'     => 'open',
    'width'    => 'max-w-lg',
    'position' => 'right',
])

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
    class="fixed inset-0 bg-[#09090b]/30 backdrop-blur-[2px] z-40"
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
            'fixed inset-y-0 z-50 flex flex-col bg-white w-full ' .
            $width . ' ' .
            ($position === 'left' ? 'left-0' : 'right-0')
    ]) }}
    style="box-shadow: -4px 0 32px rgba(0,0,0,0.10);">

    {{-- Header --}}
    <div class="shrink-0 flex items-center justify-between gap-4 px-5 py-4 border-b border-[#e4e4e7] bg-white">

        <div class="flex items-center gap-3 min-w-0">
            @if ($icon)
                <span class="shrink-0 flex items-center justify-center w-8 h-8 rounded-[10px] bg-[#f0fdf4] border border-[#d1fae5]">
                    <i class="bx {{ $icon }} text-base leading-none text-[#16a34a]"></i>
                </span>
            @endif

            <div class="min-w-0">
                @if ($title)
                    <p class="text-[13px] font-semibold text-[#09090b] truncate leading-tight">
                        {{ $title }}
                    </p>
                @endif
                @if ($subtitle)
                    <p class="text-[11px] mt-0.5 truncate text-[#71717a]">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>
        </div>

        <button
            type="button"
            x-on:click="{{ $open }} = false"
            class="shrink-0 flex items-center justify-center w-7 h-7 rounded-[8px]
                   text-[#a1a1aa] hover:text-[#09090b] hover:bg-[#f4f4f5]
                   transition-colors duration-150"
            aria-label="Close panel">
            <i class="bx bx-x text-lg leading-none"></i>
        </button>

    </div>

    {{-- Body --}}
    <div class="flex-1 overflow-y-auto overscroll-contain px-5 py-4">
        {{ $slot }}
    </div>

    {{-- Footer (optional) --}}
    @if (isset($footer))
        <div class="shrink-0 px-5 py-3.5 border-t border-[#e4e4e7] bg-[#fafafa]">
            {{ $footer }}
        </div>
    @endif

</div>
