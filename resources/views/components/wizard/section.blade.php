@props([
    'title',
    'icon'  => null,
    'color' => 'brand',
])

@php
    $palette = [
        'brand'   => ['bar' => 'bg-[#00965F]',  'icon_bg' => 'bg-[#D5FFF0] text-[#06754E]', 'title' => 'text-[#06754E]',  'header' => 'bg-[#EDFFF8] border-[#00965F]'],
        'emerald' => ['bar' => 'bg-[#00965F]',  'icon_bg' => 'bg-[#D5FFF0] text-[#06754E]', 'title' => 'text-[#06754E]',  'header' => 'bg-[#EDFFF8] border-[#00965F]'],
        'blue'    => ['bar' => 'bg-[#3197D6]',  'icon_bg' => 'bg-[#DAF1FF] text-[#143D57]', 'title' => 'text-[#143D57]',  'header' => 'bg-[#DAF1FF] border-[#3197D6]'],
        'slate'   => ['bar' => 'bg-[#72809E]',  'icon_bg' => 'bg-[#F1F3F5] text-[#394056]', 'title' => 'text-[#394056]',  'header' => 'bg-[#F9FAFA] border-[#E3E8EB]'],
        'amber'   => ['bar' => 'bg-[#F5B126]',  'icon_bg' => 'bg-[#FFF6E2] text-[#875200]', 'title' => 'text-[#875200]',  'header' => 'bg-[#FFF6E2] border-[#F5B126]'],
        'rose'    => ['bar' => 'bg-[#D21B14]',  'icon_bg' => 'bg-[#FFE3E2] text-[#731814]', 'title' => 'text-[#731814]',  'header' => 'bg-[#FFE3E2] border-[#D21B14]'],
    ];
    $p = $palette[$color] ?? $palette['brand'];
@endphp

<div {{ $attributes->class(['rounded-[12px] border border-[#E3E8EB] bg-white overflow-hidden mb-5']) }}
     style="box-shadow: 0 1px 2px rgba(16,24,40,0.04), 0 1px 3px rgba(16,24,40,0.06);">

    {{-- Colored top bar --}}
    <div class="h-[3px] w-full {{ $p['bar'] }}"></div>

    {{-- Header --}}
    <div class="flex items-center justify-between gap-3 px-5 py-3 border-b {{ $p['header'] }}">
        <div class="flex items-center gap-2.5 min-w-0">
            @if ($icon)
                <span aria-hidden="true"
                    class="shrink-0 flex items-center justify-center w-7 h-7 rounded-[8px] {{ $p['icon_bg'] }}">
                    <i class="bx bx-{{ $icon }} text-sm leading-none"></i>
                </span>
            @endif
            <h4 class="text-[13px] font-semibold {{ $p['title'] }} truncate">{{ $title }}</h4>
        </div>

        @if (isset($action) && $action->isNotEmpty())
            <div class="shrink-0 flex items-center gap-2">{{ $action }}</div>
        @endif
    </div>

    {{-- Body --}}
    <div class="p-5">{{ $slot }}</div>

</div>
