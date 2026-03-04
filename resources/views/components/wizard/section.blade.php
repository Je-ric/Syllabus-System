@props([
    'title',
    'icon'  => null,
    'color' => 'emerald',
])

@php
    $palette = [
        'emerald' => [
            'header'  => 'bg-emerald-50 border-emerald-100',
            'icon_bg' => 'bg-emerald-100 text-emerald-700',
            'title'   => 'text-emerald-900',
        ],
        'blue' => [
            'header'  => 'bg-blue-50 border-blue-100',
            'icon_bg' => 'bg-blue-100 text-blue-700',
            'title'   => 'text-blue-900',
        ],
        'amber' => [
            'header'  => 'bg-amber-50 border-amber-100',
            'icon_bg' => 'bg-amber-100 text-amber-700',
            'title'   => 'text-amber-900',
        ],
        'rose' => [
            'header'  => 'bg-rose-50 border-rose-100',
            'icon_bg' => 'bg-rose-100 text-rose-700',
            'title'   => 'text-rose-900',
        ],
        'slate' => [
            'header'  => 'bg-slate-50 border-slate-100',
            'icon_bg' => 'bg-slate-200 text-slate-600',
            'title'   => 'text-slate-800',
        ],
    ];
    $p = $palette[$color] ?? $palette['emerald'];
@endphp

<div {{ $attributes->class(['rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden']) }}>

    {{-- Coloured header strip ───────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between gap-3 px-5 py-3.5 border-b {{ $p['header'] }}">

        <div class="flex items-center gap-2.5 min-w-0">
            @if ($icon)
                <span aria-hidden="true"
                    class="shrink-0 flex items-center justify-center
                           w-7 h-7 rounded-full {{ $p['icon_bg'] }}">
                    <i class="bx bx-{{ $icon }} text-base leading-none"></i>
                </span>
            @endif
            <h4 class="text-sm font-semibold {{ $p['title'] }} truncate">
                {{ $title }}
            </h4>
        </div>

        @if (isset($action) && $action->isNotEmpty())
            <div class="shrink-0 flex items-center gap-2">
                {{ $action }}
            </div>
        @endif
    </div>

    {{-- Body ────────────────────────────────────────────────────────────────── --}}
    <div class="p-5">
        {{ $slot }}
    </div>

</div>
