@props([
    'title' => null,
    'icon'  => null,
    'color' => 'slate',
])

@php
    $palette = [
        'slate'   => ['wrap' => 'bg-[#F9FAFA] border-[#E3E8EB]',  'title' => 'text-[#394056]'],
        'emerald' => ['wrap' => 'bg-[#EDFFF8] border-[#00965F]',  'title' => 'text-[#06754E]'],
        'blue'    => ['wrap' => 'bg-[#DAF1FF] border-[#3197D6]',  'title' => 'text-[#143D57]'],
        'amber'   => ['wrap' => 'bg-[#FFF6E2] border-[#F5B126]',  'title' => 'text-[#875200]'],
        'rose'    => ['wrap' => 'bg-[#FFE3E2] border-[#D21B14]',  'title' => 'text-[#731814]'],
    ];
    $p = $palette[$color] ?? $palette['slate'];
@endphp

<div {{ $attributes->class(["rounded-[10px] border p-4 {$p['wrap']}"]) }}>

    @if ($title)
        <div class="flex items-center gap-1.5 text-[11px] font-bold {{ $p['title'] }} mb-3 uppercase tracking-[0.06em]">
            @if ($icon)
                <i class="bx bx-{{ $icon }} text-sm opacity-80" aria-hidden="true"></i>
            @endif
            {{ $title }}
        </div>
    @endif

    {{ $slot }}

</div>
