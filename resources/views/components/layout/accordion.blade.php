@props([
    'title',
    'icon'         => null,
    'open'         => false,
    'color'        => 'slate',
    'badge'        => null,
    'badgeVariant' => 'slate',
    'noPadding'    => false,
])

@php
    $palette = [
        'slate'   => ['icon_bg' => 'bg-[#F1F3F5] text-[#394056]',   'title' => 'text-[#394056]'],
        'emerald' => ['icon_bg' => 'bg-[#D5FFF0] text-[#06754E]',   'title' => 'text-[#06754E]'],
        'blue'    => ['icon_bg' => 'bg-[#DAF1FF] text-[#143D57]',   'title' => 'text-[#143D57]'],
        'amber'   => ['icon_bg' => 'bg-[#FFF6E2] text-[#875200]',   'title' => 'text-[#875200]'],
        'rose'    => ['icon_bg' => 'bg-[#FFE3E2] text-[#731814]',   'title' => 'text-[#731814]'],
    ];
    $p = $palette[$color] ?? $palette['slate'];
@endphp

<div
    x-data="{ open: {{ $open ? 'true' : 'false' }} }"
    {{ $attributes->class(['rounded-[12px] border border-[#E3E8EB] bg-white overflow-hidden']) }}
    style="box-shadow: 0 1px 2px rgba(16,24,40,0.04), 0 1px 3px rgba(16,24,40,0.06);"
>
    {{-- Header --}}
    <button
        type="button"
        x-on:click="open = !open"
        class="w-full flex items-center justify-between px-4 py-3.5 text-left
               hover:bg-[#F9FAFA] transition-colors duration-150 focus:outline-none"
        :aria-expanded="open"
    >
        <div class="flex items-center gap-3 min-w-0">
            @if ($icon)
                <span class="shrink-0 flex items-center justify-center w-8 h-8 rounded-[8px] {{ $p['icon_bg'] }}">
                    <i class="bx bx-{{ $icon }} text-base leading-none"></i>
                </span>
            @endif

            <div class="min-w-0">
                <p class="text-[13.5px] font-bold {{ $p['title'] }} leading-snug">{{ $title }}</p>
            </div>

            @if ($badge)
                <x-feedback-status.status-indicator :variant="$badgeVariant" class="ml-2 shrink-0">
                    {{ $badge }}
                </x-feedback-status.status-indicator>
            @endif
        </div>

        <div class="flex items-center gap-2 shrink-0 ml-3">
            @if (isset($actions) && $actions->isNotEmpty())
                <div x-on:click.stop class="flex items-center gap-2">
                    {{ $actions }}
                </div>
            @endif

            <i class="bx text-[#C1C8D4] text-lg transition-transform duration-200"
               :class="open ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
        </div>
    </button>

    {{-- Body --}}
    <div x-show="open" x-collapse x-cloak>
        <div class="border-t border-[#E3E8EB] {{ $noPadding ? '' : 'p-4' }}">
            {{ $slot }}
        </div>
    </div>
</div>
