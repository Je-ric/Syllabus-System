@props([
    'title',
    'icon'  => null,
    'color' => 'brand',
])

@php
    $palette = [
        'brand'   => ['header' => 'bg-[#f0fdf4] border-[#bbf7d0]', 'icon_bg' => 'bg-[#dcfce7] text-[#16a34a]', 'title' => 'text-[#166534]'],
        'emerald' => ['header' => 'bg-[#f0fdf4] border-[#bbf7d0]', 'icon_bg' => 'bg-[#dcfce7] text-[#16a34a]', 'title' => 'text-[#166534]'],
        'blue'    => ['header' => 'bg-[#eff6ff] border-[#bfdbfe]', 'icon_bg' => 'bg-[#dbeafe] text-[#1d4ed8]', 'title' => 'text-[#1e40af]'],
        'slate'   => ['header' => 'bg-[#f8fafc] border-[#e2e8f0]', 'icon_bg' => 'bg-[#e2e8f0] text-[#475569]', 'title' => 'text-[#0f172a]'],
        'amber'   => ['header' => 'bg-[#fffbeb] border-[#fcd34d]', 'icon_bg' => 'bg-[#fef3c7] text-[#d97706]', 'title' => 'text-[#92400e]'],
        'rose'    => ['header' => 'bg-[#fff1f2] border-[#fda4af]', 'icon_bg' => 'bg-[#ffe4e6] text-[#e11d48]', 'title' => 'text-[#9f1239]'],
    ];
    $p = $palette[$color] ?? $palette['brand'];
@endphp

<div {{ $attributes->class(['rounded-xl border border-[#e2e8f0] bg-white overflow-hidden']) }}
     style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">

    {{-- Header strip --}}
    <div class="flex items-center justify-between gap-3 px-5 py-3 border-b {{ $p['header'] }}">
        <div class="flex items-center gap-2.5 min-w-0">
            @if ($icon)
                <span aria-hidden="true"
                    class="shrink-0 flex items-center justify-center w-6 h-6 rounded-lg {{ $p['icon_bg'] }}">
                    <i class="bx bx-{{ $icon }} text-sm leading-none"></i>
                </span>
            @endif
            <h4 class="text-[13px] font-bold {{ $p['title'] }} truncate">{{ $title }}</h4>
        </div>

        @if (isset($action) && $action->isNotEmpty())
            <div class="shrink-0 flex items-center gap-2">{{ $action }}</div>
        @endif
    </div>

    {{-- Body --}}
    <div class="p-5">{{ $slot }}</div>

</div>
