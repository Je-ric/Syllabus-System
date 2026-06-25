@props([
    'title',
    'icon'  => null,
    'color' => 'brand',
])

@php
    $palette = [
        'brand'   => ['bar' => 'bg-[#009639]',  'icon_bg' => 'bg-[#dcfce7] text-[#15803d]', 'title' => 'text-[#166534]',  'header' => 'bg-gradient-to-r from-[#f0fdf4] to-white border-[#bbf7d0]'],
        'emerald' => ['bar' => 'bg-[#16a34a]',  'icon_bg' => 'bg-[#dcfce7] text-[#15803d]', 'title' => 'text-[#166534]',  'header' => 'bg-gradient-to-r from-[#f0fdf4] to-white border-[#bbf7d0]'],
        'blue'    => ['bar' => 'bg-[#2563eb]',  'icon_bg' => 'bg-[#dbeafe] text-[#1d4ed8]', 'title' => 'text-[#1e40af]',  'header' => 'bg-gradient-to-r from-[#eff6ff] to-white border-[#bfdbfe]'],
        'slate'   => ['bar' => 'bg-slate-400',  'icon_bg' => 'bg-[#e2e8f0] text-[#475569]', 'title' => 'text-[#0f172a]',  'header' => 'bg-gradient-to-r from-[#f8fafc] to-white border-[#e2e8f0]'],
        'amber'   => ['bar' => 'bg-amber-400',  'icon_bg' => 'bg-[#fef3c7] text-[#d97706]', 'title' => 'text-[#92400e]',  'header' => 'bg-gradient-to-r from-[#fffbeb] to-white border-[#fcd34d]'],
        'rose'    => ['bar' => 'bg-rose-400',   'icon_bg' => 'bg-[#ffe4e6] text-[#e11d48]', 'title' => 'text-[#9f1239]',  'header' => 'bg-gradient-to-r from-[#fff1f2] to-white border-[#fda4af]'],
    ];
    $p = $palette[$color] ?? $palette['brand'];
@endphp

<div {{ $attributes->class(['rounded-xl border border-[#e2e8f0] 
overflow-hidden mb-5']) }}
     style="box-shadow: 0 2px 12px rgba(0,0,0,.06);">

    {{-- Colored top bar --}}
    <div class="h-1 w-full {{ $p['bar'] }}"></div>

    {{-- Header strip --}}
    <div class="flex items-center justify-between gap-3 px-5 py-3.5 border-b {{ $p['header'] }}">
        <div class="flex items-center gap-2.5 min-w-0">
            @if ($icon)
                <span aria-hidden="true"
                    class="shrink-0 flex items-center justify-center w-7 h-7 rounded-lg {{ $p['icon_bg'] }}">
                    <i class="bx bx-{{ $icon }} text-sm leading-none"></i>
                </span>
            @endif
            <h4 class="text-sm font-bold {{ $p['title'] }} truncate">{{ $title }}</h4>
        </div>

        @if (isset($action) && $action->isNotEmpty())
            <div class="shrink-0 flex items-center gap-2">{{ $action }}</div>
        @endif
    </div>

    {{-- Body --}}
    <div class="p-5">{{ $slot }}</div>

</div>
