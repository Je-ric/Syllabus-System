@props([
    'type' => 'info',
    'title' => null,
    'message' => null,
])

@php
    $styles = [
        'success' => [
            'container' => 'border-emerald-200 bg-emerald-50 text-emerald-900',
            'iconWrap' => 'bg-emerald-100 text-emerald-700',
            'icon' => 'bx-check-circle',
            'title' => 'Success',
        ],
        'error' => [
            'container' => 'border-rose-200 bg-rose-50 text-rose-900',
            'iconWrap' => 'bg-rose-100 text-rose-700',
            'icon' => 'bx-error-circle',
            'title' => 'Error',
        ],
        'warning' => [
            'container' => 'border-amber-200 bg-amber-50 text-amber-900',
            'iconWrap' => 'bg-amber-100 text-amber-700',
            'icon' => 'bx-error',
            'title' => 'Warning',
        ],
        'info' => [
            'container' => 'border-sky-200 bg-sky-50 text-sky-900',
            'iconWrap' => 'bg-sky-100 text-sky-700',
            'icon' => 'bx-info-circle',
            'title' => 'Information',
        ],
    ];

    $alert = $styles[$type] ?? $styles['info'];
    $resolvedTitle = $title ?: $alert['title'];
@endphp

<div {{ $attributes->class(['rounded-xl border p-4', $alert['container']]) }} role="alert">
    <div class="flex items-start gap-3">
        <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ $alert['iconWrap'] }}">
            <i class="bx {{ $alert['icon'] }} text-lg leading-none"></i>
        </span>

        <div class="min-w-0 flex-1">
            @if($resolvedTitle)
                <p class="font-semibold leading-5">{{ $resolvedTitle }}</p>
            @endif
            @if($message)
                <p class="mt-1 text-sm leading-5 wrap-break-words">{{ $message }}</p>
            @endif
            {{ $slot }}
        </div>
    </div>
</div>
