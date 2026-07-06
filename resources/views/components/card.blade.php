@props([
    'title'   => null,
    'icon'    => null,
    'color'   => 'slate',
    'padding' => true,
    'shadow'  => true,
])

@php
    $palette = [
        'slate'   => ['strip' => 'bg-[#f4f4f5] border-[#e4e4e7]',   'icon' => 'bg-[#f4f4f5] text-[#52525b]',   'title' => 'text-[#09090b]'],
        'emerald' => ['strip' => 'bg-[#f0fdf4] border-[#d1fae5]',   'icon' => 'bg-[#dcfce7] text-[#16a34a]',   'title' => 'text-[#166534]'],
        'blue'    => ['strip' => 'bg-[#eff6ff] border-[#bfdbfe]',   'icon' => 'bg-[#dbeafe] text-[#2563eb]',   'title' => 'text-[#1e40af]'],
        'amber'   => ['strip' => 'bg-[#fffbeb] border-[#fde68a]',   'icon' => 'bg-[#fef3c7] text-[#d97706]',   'title' => 'text-[#92400e]'],
        'rose'    => ['strip' => 'bg-[#fff1f2] border-[#fecdd3]',   'icon' => 'bg-[#ffe4e6] text-[#e11d48]',   'title' => 'text-[#9f1239]'],
        'violet'  => ['strip' => 'bg-[#faf5ff] border-[#e9d5ff]',   'icon' => 'bg-[#ede9fe] text-[#7c3aed]',   'title' => 'text-[#581c87]'],
        'navy'    => ['strip' => 'bg-[#1a2235] border-[#1a2235]',   'icon' => 'bg-white/10 text-[#ffd700]',    'title' => 'text-white'],
        'gold'    => ['strip' => 'bg-[#fffbeb] border-[#fde68a]',   'icon' => 'bg-[#fef3c7] text-[#92400e]',   'title' => 'text-[#78350f]'],
    ];
    $p = $palette[$color] ?? $palette['slate'];
@endphp

<div {{ $attributes->class(['rounded-[16px] border border-[#e4e4e7] bg-white overflow-hidden']) }}
     @if($shadow) style="box-shadow: 0 1px 8px rgba(0,0,0,0.05);" @endif>

    @if ($title)
        <div class="flex items-center justify-between gap-3 px-4 py-3 border-b {{ $p['strip'] }}">
            <div class="flex items-center gap-2.5 min-w-0">
                @if ($icon)
                    <span class="shrink-0 flex items-center justify-center w-7 h-7 rounded-[10px] {{ $p['icon'] }}">
                        <i class="bx bx-{{ $icon }} text-sm leading-none"></i>
                    </span>
                @endif
                <h4 class="text-[13px] font-semibold {{ $p['title'] }} truncate">{{ $title }}</h4>
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
