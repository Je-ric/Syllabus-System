@props([
    'title' => null,
    'icon'  => null,
    'color' => 'slate',
])

@php
    $palette = [
        'slate'   => ['wrap' => 'bg-[#fafafa] border-[#e4e4e7]',   'title' => 'text-[#52525b]'],
        'emerald' => ['wrap' => 'bg-[#f0fdf4] border-[#d1fae5]',   'title' => 'text-[#166534]'],
        'blue'    => ['wrap' => 'bg-[#eff6ff] border-[#bfdbfe]',   'title' => 'text-[#1e40af]'],
        'amber'   => ['wrap' => 'bg-[#fffbeb] border-[#fde68a]',   'title' => 'text-[#92400e]'],
    ];
    $p = $palette[$color] ?? $palette['slate'];
@endphp

<div {{ $attributes->class(["rounded-[14px] border p-4 {$p['wrap']}"]) }}>

    @if ($title)
        <div class="flex items-center gap-1.5 text-[11px] font-semibold {{ $p['title'] }} mb-3">
            @if ($icon)
                <i class="bx bx-{{ $icon }} text-sm opacity-70" aria-hidden="true"></i>
            @endif
            {{ $title }}
        </div>
    @endif

    {{ $slot }}

</div>
