@props([
    'type'        => 'info',
    'icon'        => null,
    'title'       => null,
    'dismissable' => false,
])

@php
    $types = [
        'info' => [
            'wrap'  => 'bg-blue-50 border-blue-200 text-blue-700',
            'icon'  => $icon ?? 'info-circle',
            'title' => 'text-blue-900',
        ],
        'warning' => [
            'wrap'  => 'bg-amber-50 border-amber-200 text-amber-700',
            'icon'  => $icon ?? 'error-circle',
            'title' => 'text-amber-900',
        ],
        'danger' => [
            'wrap'  => 'bg-rose-50 border-rose-200 text-rose-700',
            'icon'  => $icon ?? 'lock-alt',
            'title' => 'text-rose-900',
        ],
        'success' => [
            'wrap'  => 'bg-emerald-50 border-emerald-200 text-emerald-700',
            'icon'  => $icon ?? 'check-circle',
            'title' => 'text-emerald-900',
        ],
    ];
    $t = $types[$type] ?? $types['info'];
@endphp

<div
    @if ($dismissable) x-data="{ show: true }" x-show="show" @endif
    {{ $attributes->class(["flex items-start gap-3 rounded-xl border px-4 py-3 text-sm {$t['wrap']}"]) }}
    role="alert">

    <i class="bx bx-{{ $t['icon'] }} text-xl shrink-0 mt-0.5" aria-hidden="true"></i>

    <div class="flex-1 min-w-0">
        @if ($title)
            <p class="font-semibold {{ $t['title'] }} mb-0.5 text-sm">{{ $title }}</p>
        @endif
        <div class="text-xs leading-relaxed">
            {{ $slot }}
        </div>
    </div>

    @if ($dismissable)
        <button @click="show = false" type="button"
            class="shrink-0 mt-0.5 p-0.5 rounded-md opacity-60
                   hover:opacity-100 transition-opacity focus:outline-none"
            aria-label="Dismiss alert">
            <i class="bx bx-x text-base" aria-hidden="true"></i>
        </button>
    @endif

</div>
