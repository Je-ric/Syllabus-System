@props([
    'class'   => '',
    'modalId' => null,
    'variant' => null,
    // {{-- add | edit | delete | assign | confirm | warning | approve | reject | restore | disable | archive | roles --}}
])

@php
    $icons = [
        'add'     => ['icon' => 'bx-plus-circle',  'bg' => 'bg-green-600',  'color' => 'text-green-100'],
        'edit'    => ['icon' => 'bx-edit',          'bg' => 'bg-blue-600',   'color' => 'text-blue-100'],
        'delete'  => ['icon' => 'bx-trash',         'bg' => 'bg-red-600',    'color' => 'text-red-100'],
        'assign'  => ['icon' => 'bx-user-check',    'bg' => 'bg-green-600',   'color' => 'text-green-100'],
        'roles'   => ['icon' => 'bx-shield',        'bg' => 'bg-blue-600',   'color' => 'text-blue-100'],
        'confirm' => ['icon' => 'bx-check-circle',  'bg' => 'bg-green-600',  'color' => 'text-green-100'],
        'warning' => ['icon' => 'bx-error',         'bg' => 'bg-amber-600',  'color' => 'text-amber-100'],
        'archive' => ['icon' => 'bx-archive',       'bg' => 'bg-amber-600',  'color' => 'text-amber-100'],
        'approve' => ['icon' => 'bx-check-shield',  'bg' => 'bg-green-600',  'color' => 'text-green-100'],
        'reject'  => ['icon' => 'bx-block',         'bg' => 'bg-red-600',    'color' => 'text-red-100'],
        'restore' => ['icon' => 'bx-revision',      'bg' => 'bg-blue-600',   'color' => 'text-blue-100'],
        'disable' => ['icon' => 'bx-pause-circle',  'bg' => 'bg-amber-600',  'color' => 'text-amber-100'],
    ];
    $ic = $variant ? ($icons[$variant] ?? null) : null;
@endphp

<header {{ $attributes->merge(['class' => "px-6 py-4 border-b border-[#e2e8f0] bg-white flex-shrink-0 $class"]) }}>
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-3 flex-1 min-w-0">
            @if ($ic)
                <span class="flex items-center justify-center w-8 h-8 rounded-lg shrink-0 {{ $ic['bg'] }} {{ $ic['color'] }}">
                    <i class="bx {{ $ic['icon'] }} text-base leading-none"></i>
                </span>
            @endif
            <div class="flex-1 min-w-0 text-[15px] font-bold text-[#0f172a]">
                {{ $slot }}
            </div>
        </div>

        @if ($modalId)
            <button
                type="button"
                onclick="document.getElementById('{{ $modalId }}').close()"
                class="shrink-0 rounded-lg p-1.5 text-[#94a3b8]
                       hover:bg-[#f8fafc] hover:text-[#475569]
                       transition-colors duration-150"
                aria-label="Close">
                <i class="bx bx-x text-xl leading-none"></i>
            </button>
        @endif
    </div>
</header>
