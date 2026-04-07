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
        'slate'   => ['icon_bg' => 'bg-[#e2e8f0] text-[#475569]',   'title' => 'text-[#0f172a]'],
        'emerald' => ['icon_bg' => 'bg-[#dcfce7] text-[#16a34a]',   'title' => 'text-[#166534]'],
        'blue'    => ['icon_bg' => 'bg-[#dbeafe] text-[#1d4ed8]',   'title' => 'text-[#1e40af]'],
        'amber'   => ['icon_bg' => 'bg-[#fef3c7] text-[#d97706]',   'title' => 'text-[#92400e]'],
        'rose'    => ['icon_bg' => 'bg-[#ffe4e6] text-[#e11d48]',   'title' => 'text-[#9f1239]'],
    ];
    $p = $palette[$color] ?? $palette['slate'];
@endphp

<div
    x-data="{ open: {{ $open ? 'true' : 'false' }} }"
    {{ $attributes->class(['rounded-xl border border-[#e2e8f0] bg-white overflow-hidden']) }}
    style="box-shadow: 0 2px 16px rgba(0,0,0,.07);"
>
    {{-- Header --}}
    <button
        type="button"
        x-on:click="open = !open"
        class="w-full flex items-center justify-between px-5 py-4 text-left
               hover:bg-[#f8fafc] transition-colors duration-100 focus:outline-none"
        :aria-expanded="open"
    >
        <div class="flex items-center gap-3 min-w-0">
            @if ($icon)
                <span class="shrink-0 flex items-center justify-center w-8 h-8 rounded-lg {{ $p['icon_bg'] }}">
                    <i class="bx bx-{{ $icon }} text-base leading-none"></i>
                </span>
            @endif

            <div class="min-w-0">
                <p class="text-[13px] font-bold {{ $p['title'] }} leading-snug">{{ $title }}</p>
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

            <i class="bx text-[#94a3b8] text-lg transition-transform duration-200"
               :class="open ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
        </div>
    </button>

    {{-- Body --}}
    <div x-show="open" x-collapse x-cloak>
        <div class="border-t border-[#e2e8f0] {{ $noPadding ? '' : 'p-5' }}">
            {{ $slot }}
        </div>
    </div>
</div>
