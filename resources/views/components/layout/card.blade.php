@props([
    'title'   => null,
    'icon'    => null,
    'color'   => 'slate',
    'padding' => true,
    'shadow'  => true,
])

@php
    // Design-system palette:
    // strip  = header band (bg + bottom-border color)
    // icon   = icon container bg + icon color
    // title  = title text color
    $palette = [
        'slate'   => [
            'strip' => 'bg-[#F1F3F5] border-[#E3E8EB]',
            'icon'  => 'bg-[#E3E8EB] text-[#394056]',
            'title' => 'text-[#394056]',
        ],
        'emerald' => [
            'strip' => 'bg-[#D5FFF0] border-[#00C075]',
            'icon'  => 'bg-[#AEFFE2] text-[#06754E]',
            'title' => 'text-[#06754E]',
        ],
        'blue' => [
            'strip' => 'bg-[#DAF1FF] border-[#3197D6]',
            'icon'  => 'bg-[#AEDFFF] text-[#143D57]',
            'title' => 'text-[#143D57]',
        ],
        'amber' => [
            'strip' => 'bg-[#FFF6E2] border-[#F5B126]',
            'icon'  => 'bg-[#FFE9B5] text-[#875200]',
            'title' => 'text-[#875200]',
        ],
        'rose' => [
            'strip' => 'bg-[#FFE3E2] border-[#E52F28]',
            'icon'  => 'bg-[#FFA2A2] text-[#731814]',
            'title' => 'text-[#731814]',
        ],
        'violet' => [
            'strip' => 'bg-[#F3EEFF] border-[#7C3AED]',
            'icon'  => 'bg-[#E9D5FF] text-[#4C1D95]',
            'title' => 'text-[#4C1D95]',
        ],
        'navy' => [
            'strip' => 'bg-[#1D2836] border-[#253540]',
            'icon'  => 'bg-white/10 text-[#FFC646]',
            'title' => 'text-white',
        ],
        'gold' => [
            'strip' => 'bg-[#FFF6E2] border-[#F5B126]',
            'icon'  => 'bg-[#FFE9B5] text-[#875200]',
            'title' => 'text-[#875200]',
        ],
    ];
    $p = $palette[$color] ?? $palette['slate'];
@endphp

<div {{ $attributes->class(['rounded-[12px] border border-[#E3E8EB] bg-white overflow-hidden']) }}
     @if($shadow) style="box-shadow: 0 1px 2px rgba(16,24,40,0.04), 0 1px 3px rgba(16,24,40,0.06);" @endif>

    @if ($title)
        <div class="flex items-center justify-between gap-3 px-4 py-3 border-b {{ $p['strip'] }}">
            <div class="flex items-center gap-2 min-w-0">
                @if ($icon)
                    <span class="shrink-0 flex items-center justify-center w-7 h-7 rounded-[8px] {{ $p['icon'] }}">
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
