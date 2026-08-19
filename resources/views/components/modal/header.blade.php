@props([
    'class'   => '',
    'modalId' => null,
    'variant' => null,
    // add | edit | delete | assign | confirm | warning | approve | reject | restore | disable | archive | roles
])

@php
    /*
    ┌──────────────────────────────────────────────────────────────────────────┐
    │  modal.header — Design.md compliant icon chips                           │
    │  Emerald scale  : add / assign / confirm / approve                       │
    │  Red scale      : delete / reject           (§ 1.3 Red)                 │
    │  Yellow scale   : warning / archive / disable (§ 1.3 Yellow)            │
    │  Blue scale     : edit / roles / restore    (§ 1.2 Blue)                │
    └──────────────────────────────────────────────────────────────────────────┘
    */
    $icons = [
        'add'     => ['icon' => 'bx-plus-circle',  'bg' => '#06754E', 'ring' => '#AEFFE2', 'fg' => '#EDFFF8'],  // Emerald 800 / 200 / 50
        'edit'    => ['icon' => 'bx-edit',          'bg' => '#194C6E', 'ring' => '#AEDFFF', 'fg' => '#DAF1FF'],  // Blue 800 / 300 / 200
        'delete'  => ['icon' => 'bx-trash',         'bg' => '#D21B14', 'ring' => '#FFA2A2', 'fg' => '#FFE3E2'],  // Red 600 / 300 / 200
        'assign'  => ['icon' => 'bx-user-check',    'bg' => '#06754E', 'ring' => '#AEFFE2', 'fg' => '#EDFFF8'],
        'roles'   => ['icon' => 'bx-shield',        'bg' => '#194C6E', 'ring' => '#AEDFFF', 'fg' => '#DAF1FF'],
        'confirm' => ['icon' => 'bx-check-circle',  'bg' => '#06754E', 'ring' => '#AEFFE2', 'fg' => '#EDFFF8'],
        'warning' => ['icon' => 'bx-error',         'bg' => '#B37100', 'ring' => '#FFE9B5', 'fg' => '#FFF6E2'],  // Yellow 800 / 300 / 200
        'archive' => ['icon' => 'bx-archive',       'bg' => '#B37100', 'ring' => '#FFE9B5', 'fg' => '#FFF6E2'],
        'approve' => ['icon' => 'bx-check-shield',  'bg' => '#06754E', 'ring' => '#AEFFE2', 'fg' => '#EDFFF8'],
        'reject'  => ['icon' => 'bx-block',         'bg' => '#D21B14', 'ring' => '#FFA2A2', 'fg' => '#FFE3E2'],
        'restore' => ['icon' => 'bx-revision',      'bg' => '#194C6E', 'ring' => '#AEDFFF', 'fg' => '#DAF1FF'],
        'disable' => ['icon' => 'bx-pause-circle',  'bg' => '#B37100', 'ring' => '#FFE9B5', 'fg' => '#FFF6E2'],
    ];
    $ic = $variant ? ($icons[$variant] ?? null) : null;
@endphp

<header {{ $attributes->merge(['class' => "px-5 py-4 border-b border-[#E3E8EB] bg-white flex-shrink-0 $class"]) }}>
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-3 flex-1 min-w-0">
            @if ($ic)
                {{-- Icon chip: filled bg + lighter ring border for depth --}}
                <span class="flex items-center justify-center w-9 h-9 rounded-[10px] shrink-0 border"
                      style="background:{{ $ic['bg'] }}; border-color:{{ $ic['ring'] }};">
                    <i class="bx {{ $ic['icon'] }} text-[15px] leading-none" style="color:{{ $ic['fg'] }};"></i>
                </span>
            @endif
            <div class="flex-1 min-w-0 text-[14.5px] font-bold text-[#253540]">
                {{ $slot }}
            </div>
        </div>

        @if ($modalId)
            <button
                type="button"
                onclick="document.getElementById('{{ $modalId }}').close()"
                class="shrink-0 rounded-[8px] p-1.5
                       text-[#A5B2BD] hover:text-[#394056] hover:bg-[#F1F3F5]
                       active:scale-90
                       focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#00C075]/30
                       transition-all duration-150"
                aria-label="Close">
                <i class="bx bx-x text-xl leading-none"></i>
            </button>
        @endif
    </div>
</header>
