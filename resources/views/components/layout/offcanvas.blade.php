@props([
    'title'    => '',
    'subtitle' => '',
    'icon'     => null,
    'open'     => 'open',
    'width'    => 'max-w-lg',
    'position' => 'right',
])

<div>
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
    class="fixed inset-0 bg-[#1D2836]/40 backdrop-blur-[3px] z-40"
    aria-hidden="true">
</div>

{{-- Panel --}}
<div
    x-show="{{ $open }}"
    x-cloak
    x-transition:enter="transition ease-out duration-250"
    x-transition:enter-start="opacity-0 {{ $position === 'left' ? '-translate-x-5' : 'translate-x-5' }}"
    x-transition:enter-end="opacity-100 translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-x-0"
    x-transition:leave-end="opacity-0 {{ $position === 'left' ? '-translate-x-5' : 'translate-x-5' }}"
    x-on:keydown.escape.window="{{ $open }} = false"
    role="dialog"
    aria-modal="true"
    {{ $attributes->merge([
        'class' =>
            'inter fixed inset-y-0 z-50 flex flex-col bg-white w-full ' .
            $width . ' ' .
            ($position === 'left' ? 'left-0' : 'right-0')
    ]) }}
    style="box-shadow: {{ $position === 'left' ? '4px' : '-4px' }} 0 40px rgba(16,24,40,0.12);">

    {{-- Header --}}
    <div class="shrink-0 flex items-center justify-between gap-4 px-5 py-4 border-b border-[#E3E8EB] bg-white">

        <div class="flex items-center gap-3 min-w-0">
            @if ($icon)
                <span class="shrink-0 flex items-center justify-center w-9 h-9 rounded-[10px]
                            bg-[#06754E] border border-[#AEFFE2]">
                    <i class="bx {{ $icon }} text-base leading-none text-[#EDFFF8]"></i>
                </span>
            @endif

            <div class="min-w-0">
                @if ($title)
                    <p class="text-[13px] font-semibold text-[#394056] truncate leading-tight">
                        {{ $title }}
                    </p>
                @endif
                @if ($subtitle)
                    <p class="text-[11px] mt-0.5 truncate text-[#72809E]">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>
        </div>

        <button
            type="button"
            x-on:click="{{ $open }} = false"
            class="shrink-0 flex items-center justify-center w-7 h-7 rounded-[7px]
                   text-[#93A1AF] hover:text-[#394056] hover:bg-[#F1F3F5]
                   transition-all duration-150"
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
        <div class="shrink-0 px-5 py-3.5 border-t border-[#E3E8EB] bg-[#F9FAFA]">
            {{ $footer }}
        </div>
    @endif

</div>
</div>
