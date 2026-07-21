@props([
    'type'        => 'info',
    'title'       => null,
    'message'     => null,
    'showTitle'   => true,
    'dismissable' => false,
    'class'       => '',
])

@php
    $styles = [
        'success' => [
            'container' => 'border-[#00965F] bg-[#D5FFF0] text-[#06754E]',
            'iconWrap'  => 'bg-[#AEFFE2] text-[#06754E]',
            'icon'      => 'bx-check-circle',
            'title'     => 'Success',
        ],
        'error' => [
            'container' => 'border-[#D21B14] bg-[#FFE3E2] text-[#731814]',
            'iconWrap'  => 'bg-[#FFA2A2] text-[#731814]',
            'icon'      => 'bx-error-circle',
            'title'     => 'Error',
        ],
        'warning' => [
            'container' => 'border-[#F5B126] bg-[#FFF6E2] text-[#875200]',
            'iconWrap'  => 'bg-[#FFE9B5] text-[#875200]',
            'icon'      => 'bx-error',
            'title'     => 'Warning',
        ],
        'info' => [
            'container' => 'border-[#3197D6] bg-[#DAF1FF] text-[#143D57]',
            'iconWrap'  => 'bg-[#AEDFFF] text-[#143D57]',
            'icon'      => 'bx-info-circle',
            'title'     => 'Information',
        ],
        'default' => [
            'container' => 'border-[#AEDFFF] bg-[#F7FCFE] text-[#194C6E]',
            'iconWrap'  => 'bg-[#DAF1FF] text-[#194C6E]',
            'icon'      => 'bx-info-circle',
            'title'     => 'Notice',
        ],
    ];

    $alert         = $styles[$type] ?? $styles['info'];
    $resolvedTitle = $showTitle ? ($title ?: $alert['title']) : null;
@endphp

<div
    @if ($dismissable) x-data="{ show: true }" x-show="show" @endif
    {{ $attributes->class(['rounded-[10px] border p-3.5', $alert['container'], $class]) }}
    role="alert">

    <div class="flex items-start gap-3">
        <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-[8px]
                {{ $alert['iconWrap'] }}">
            <i class="bx {{ $alert['icon'] }} text-base leading-none"></i>
        </span>

        <div class="min-w-0 flex-1">
            @if ($resolvedTitle)
                <p class="text-[13px] font-semibold leading-5">{{ $resolvedTitle }}</p>
            @endif

            @if ($message || $slot->isNotEmpty())
                <div class="{{ $resolvedTitle ? 'mt-0.5' : '' }} text-[13px] leading-relaxed opacity-90">
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
                class="shrink-0 mt-0.5 p-0.5 rounded-[6px] opacity-50
                    hover:opacity-100 hover:bg-black/10 transition-all duration-150 focus:outline-none"
                aria-label="Dismiss alert">
                <i class="bx bx-x text-base" aria-hidden="true"></i>
            </button>
        @endif
    </div>
</div>
