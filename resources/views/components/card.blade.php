@props([
    'title'   => null,
    'icon'    => null,
    'color'   => 'slate',
    'padding' => true,
    'shadow'  => true,
])

@php
    $palette = [
        'slate'   => ['strip' => 'bg-[#f8fafc] border-[#e2e8f0]',   'icon' => 'bg-[#e2e8f0] text-[#475569]',   'title' => 'text-[#0f172a]'],
        'emerald' => ['strip' => 'bg-[#f0fdf4] border-[#bbf7d0]',   'icon' => 'bg-[#dcfce7] text-[#16a34a]',   'title' => 'text-[#166534]'],
        'blue'    => ['strip' => 'bg-[#eff6ff] border-[#bfdbfe]',   'icon' => 'bg-[#dbeafe] text-[#1d4ed8]',   'title' => 'text-[#1e40af]'],
        'amber'   => ['strip' => 'bg-[#fffbeb] border-[#fcd34d]',   'icon' => 'bg-[#fef3c7] text-[#d97706]',   'title' => 'text-[#92400e]'],
        'rose'    => ['strip' => 'bg-[#fff1f2] border-[#fda4af]',   'icon' => 'bg-[#ffe4e6] text-[#e11d48]',   'title' => 'text-[#9f1239]'],
        'violet'  => ['strip' => 'bg-[#faf5ff] border-[#d8b4fe]',   'icon' => 'bg-[#ede9fe] text-[#7c3aed]',   'title' => 'text-[#581c87]'],
        'navy'    => ['strip' => 'bg-[#1a2235] border-[#1a2235]',   'icon' => 'bg-white/10 text-[#e0a70d]',    'title' => 'text-white'],
        'gold'    => ['strip' => 'bg-[#fffbeb] border-[#fde68a]',   'icon' => 'bg-[#fef3c7] text-[#92400e]',   'title' => 'text-[#78350f]'],
    ];
    $p = $palette[$color] ?? $palette['slate'];
@endphp

<div {{ $attributes->class([
    'rounded-xl border border-[#e2e8f0] bg-white overflow-hidden',
]) }} @if($shadow) style="box-shadow: 0 2px 16px rgba(0,0,0,.07);" @endif>

    @if ($title)
        <div class="flex items-center justify-between gap-3 px-4 py-3 border-b {{ $p['strip'] }}">
            <div class="flex items-center gap-2.5 min-w-0">
                @if ($icon)
                    <span class="shrink-0 flex items-center justify-center w-7 h-7 rounded-lg {{ $p['icon'] }}">
                        <i class="bx bx-{{ $icon }} text-sm leading-none"></i>
                    </span>
                @endif
                <h4 class="text-[13px] font-bold {{ $p['title'] }} truncate">{{ $title }}</h4>
            </div>

            @if (isset($action) && $action->isNotEmpty())
                <div class="shrink-0 flex items-center gap-2">{{ $action }}</div>
            @endif
        </div>
    @endif

    <div class="{{ $padding ? 'p-4' : '' }}">
        {{ $slot }}
    </div>

</div>
