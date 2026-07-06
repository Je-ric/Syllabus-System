@props([
    'title',
    'icon'  => null,
    'color' => 'brand',
])

@php
    $palette = [
        'brand'   => ['bar' => 'bg-[#16a34a]',  'icon_bg' => 'bg-[#dcfce7] text-[#15803d]', 'title' => 'text-[#166534]',  'header' => 'bg-[#f0fdf4] border-[#d1fae5]'],
        'emerald' => ['bar' => 'bg-[#16a34a]',  'icon_bg' => 'bg-[#dcfce7] text-[#15803d]', 'title' => 'text-[#166534]',  'header' => 'bg-[#f0fdf4] border-[#d1fae5]'],
        'blue'    => ['bar' => 'bg-[#2563eb]',  'icon_bg' => 'bg-[#dbeafe] text-[#1d4ed8]', 'title' => 'text-[#1e40af]',  'header' => 'bg-[#eff6ff] border-[#bfdbfe]'],
        'slate'   => ['bar' => 'bg-[#71717a]',  'icon_bg' => 'bg-[#f4f4f5] text-[#52525b]', 'title' => 'text-[#09090b]',  'header' => 'bg-[#fafafa] border-[#e4e4e7]'],
        'amber'   => ['bar' => 'bg-[#d97706]',  'icon_bg' => 'bg-[#fef3c7] text-[#d97706]', 'title' => 'text-[#92400e]',  'header' => 'bg-[#fffbeb] border-[#fde68a]'],
        'rose'    => ['bar' => 'bg-[#e11d48]',  'icon_bg' => 'bg-[#ffe4e6] text-[#e11d48]', 'title' => 'text-[#9f1239]',  'header' => 'bg-[#fff1f2] border-[#fecdd3]'],
    ];
    $p = $palette[$color] ?? $palette['brand'];
@endphp

<div {{ $attributes->class(['rounded-[16px] border border-[#e4e4e7] bg-white overflow-hidden mb-5']) }}
     style="box-shadow: 0 1px 6px rgba(0,0,0,0.04);">

    {{-- Colored top bar --}}
    <div class="h-[3px] w-full {{ $p['bar'] }}"></div>

    {{-- Header --}}
    <div class="flex items-center justify-between gap-3 px-5 py-3 border-b {{ $p['header'] }}">
        <div class="flex items-center gap-2.5 min-w-0">
            @if ($icon)
                <span aria-hidden="true"
                    class="shrink-0 flex items-center justify-center w-7 h-7 rounded-[10px] {{ $p['icon_bg'] }}">
                    <i class="bx bx-{{ $icon }} text-sm leading-none"></i>
                </span>
            @endif
            <h4 class="text-sm font-semibold {{ $p['title'] }} truncate">{{ $title }}</h4>
        </div>

        @if (isset($action) && $action->isNotEmpty())
            <div class="shrink-0 flex items-center gap-2">{{ $action }}</div>
        @endif
    </div>

    {{-- Body --}}
    <div class="p-5">{{ $slot }}</div>

</div>
