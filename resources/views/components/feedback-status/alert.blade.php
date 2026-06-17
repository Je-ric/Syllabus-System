@props([
    'type'       => 'info',
    'title'      => null,
    'message'    => null,
    'showTitle'  => true,
    'dismissable'=> false,
    'class'      => '',
])

@php
    $styles = [
        'success' => [
            'container' => 'border-[#bbf7d0] bg-[#f0fdf4] text-[#166534]',
            'iconWrap'  => 'bg-[#dcfce7] text-[#16a34a]',
            'icon'      => 'bx-check-circle',
            'title'     => 'Success',
        ],
        'error' => [
            'container' => 'border-[#fda4af] bg-[#fff1f2] text-[#9f1239]',
            'iconWrap'  => 'bg-[#ffe4e6] text-[#f43f5e]',
            'icon'      => 'bx-error-circle',
            'title'     => 'Error',
        ],
        'warning' => [
            'container' => 'border-[#fcd34d] bg-[#fffbeb] text-[#92400e]',
            'iconWrap'  => 'bg-[#fef3c7] text-[#f59e0b]',
            'icon'      => 'bx-error',
            'title'     => 'Warning',
        ],
        'info' => [
            'container' => 'border-[#e2e8f0] bg-[#f8fafc] text-[#0f172a]',
            'iconWrap'  => 'bg-[#e2e8f0] text-[#475569]',
            'icon'      => 'bx-info-circle',
            'title'     => 'Information',
        ],
        'default' => [
            'container' => 'border-blue-100 bg-blue-50/60 text-blue-800',
            'iconWrap'  => 'bg-blue-50 text-blue-500',
            'icon'      => 'bx-info-circle',
            'title'     => 'Notice',
        ],
    ];

    $alert         = $styles[$type] ?? $styles['info'];
    $resolvedTitle = $showTitle ? ($title ?: $alert['title']) : null;
@endphp

<div
    @if ($dismissable) x-data="{ show: true }" x-show="show" @endif
    {{ $attributes->class(['rounded-xl border p-4', $alert['container'], $class]) }}
    role="alert">

    <div class="flex items-start gap-3">
        <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-lg
                {{ $alert['iconWrap'] }}">
            <i class="bx {{ $alert['icon'] }} text-base leading-none"></i>
        </span>

        <div class="min-w-0 flex-1">
            @if ($resolvedTitle)
                <p class="text-[13px] font-semibold leading-5">{{ $resolvedTitle }}</p>
            @endif

            @if ($message || $slot->isNotEmpty())
                <div class="{{ $resolvedTitle ? 'mt-0.5' : '' }} text-[13px] leading-relaxed">
                    @if ($message)
                        <p>{{ $message }}</p>
                    @endif

                    @if ($slot->isNotEmpty())
                        {{ $slot }}
                    @endif
                </div>
            @endif
        </div>

        @if ($dismissable)
            <button @click="show = false" type="button"
                class="shrink-0 mt-0.5 p-0.5 rounded-md opacity-50
                    hover:opacity-100 transition-opacity focus:outline-none"
                aria-label="Dismiss alert">
                <i class="bx bx-x text-base" aria-hidden="true"></i>
            </button>
        @endif
    </div>
</div>
