@props([
    'type'    => 'info',
    'title'   => null,
    'message' => null,
    'showTitle' => true,
    'dismissable' => false,
    'class'   => '',
])

@php
    $styles = [
        'success' => [
            'container' => 'border-emerald-200 bg-emerald-50 text-emerald-900',
            'iconWrap'  => 'bg-emerald-100 text-emerald-600',
            'icon'      => 'bx-check-circle',
            'title'     => 'Success',
        ],
        'error' => [
            'container' => 'border-rose-200 bg-rose-50 text-rose-900',
            'iconWrap'  => 'bg-rose-100 text-rose-600',
            'icon'      => 'bx-error-circle',
            'title'     => 'Error',
        ],
        'warning' => [
            'container' => 'border-amber-200 bg-amber-50 text-amber-900',
            'iconWrap'  => 'bg-amber-100 text-amber-600',
            'icon'      => 'bx-error',
            'title'     => 'Warning',
        ],
        'info' => [
            'container' => 'border-sky-200 bg-sky-50 text-sky-900',
            'iconWrap'  => 'bg-sky-100 text-sky-600',
            'icon'      => 'bx-info-circle',
            'title'     => 'Information',
        ],
    ];

    $alert        = $styles[$type] ?? $styles['info'];
    $resolvedTitle = $showTitle ? ($title ?: $alert['title']) : null;
@endphp

<div
    @if ($dismissable) x-data="{ show: true }" x-show="show" @endif
    {{ $attributes->class(['rounded-xl border p-4', $alert['container'], $class]) }}
    role="alert">

    <div class="flex items-start gap-3">
        <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center
                     rounded-full {{ $alert['iconWrap'] }}">
            <i class="bx {{ $alert['icon'] }} text-lg leading-none"></i>
        </span>

        <div class="min-w-0 flex-1">
            @if ($resolvedTitle)
                <p class="font-semibold leading-5">{{ $resolvedTitle }}</p>
            @endif

            @if ($message)
                {{-- break-words: valid Tailwind utility (wrap-break-words is not) --}}
                <p class="mt-1 text-sm leading-relaxed wrap-break-words">{{ $message }}</p>
            @endif

            {{-- Slot for richer content (lists, links, etc.) --}}
            @if ($slot->isNotEmpty())
                <div class="mt-1 text-sm leading-relaxed wrap-break-words">
                    {{ $slot }}
                </div>
            @endif
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
</div>
