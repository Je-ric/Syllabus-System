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
            'container' => 'border-[#00965F] bg-[#F4FFFA] text-[#06754E]',
            'iconWrap'  => 'bg-[#D5FFF0] text-[#06754E] ring-1 ring-inset ring-[#AEFFE2]',
            'icon'      => 'bx-check-circle',
            'title'     => 'Success',
        ],
        'error' => [
            'container' => 'border-[#E52F28] bg-[#FFF8F8] text-[#731814]',
            'iconWrap'  => 'bg-[#FFE3E2] text-[#D21B14] ring-1 ring-inset ring-[#FFA2A2]',
            'icon'      => 'bx-error-circle',
            'title'     => 'Error',
        ],
        'warning' => [
            'container' => 'border-[#F5B126] bg-[#FFFCF5] text-[#875200]',
            'iconWrap'  => 'bg-[#FFF6E2] text-[#B37100] ring-1 ring-inset ring-[#FFE9B5]',
            'icon'      => 'bx-error',
            'title'     => 'Warning',
        ],
        'info' => [
            'container' => 'border-[#3197D6] bg-[#F5FBFF] text-[#143D57]',
            'iconWrap'  => 'bg-[#DAF1FF] text-[#194C6E] ring-1 ring-inset ring-[#AEDFFF]',
            'icon'      => 'bx-info-circle',
            'title'     => 'Information',
        ],
        'default' => [
            'container' => 'border-[#D6DDE3] bg-[#F9FAFA] text-[#394056]',
            'iconWrap'  => 'bg-[#F1F3F5] text-[#72809E] ring-1 ring-inset ring-[#E3E8EB]',
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

        <div class="min-w-0 flex-1 pt-0.5">
            @if ($resolvedTitle)
                <p class="text-[13px] font-semibold leading-5">{{ $resolvedTitle }}</p>
            @endif

            @if ($message || $slot->isNotEmpty())
                <div class="{{ $resolvedTitle ? 'mt-1' : '' }} text-[13px] leading-relaxed opacity-90">
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
                class="shrink-0 mt-0.5 p-1 rounded-[6px] opacity-50
                    hover:opacity-100 hover:bg-black/10 transition-all duration-150 focus:outline-none"
                aria-label="Dismiss alert">
                <i class="bx bx-x text-base" aria-hidden="true"></i>
            </button>
        @endif
    </div>
</div>
