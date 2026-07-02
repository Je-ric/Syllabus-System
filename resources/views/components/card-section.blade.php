@props([
    'title' => null,
    'icon' => null,
    'count' => null,
    'subtitle' => null,
    'padded' => true,
    'headerRight' => null,
])

<div {{ $attributes->merge([
    'class' => 'rounded-xl border border-green-200 bg-white overflow-hidden shadow-md'
]) }}>

    <div class="px-5 py-3 border-b border-green-200 bg-[#f8fafc] flex items-center gap-2">
        <i class="bx {{ $icon }} text-[#16a34a] text-sm"></i>
        <div class="flex flex-col gap-0.5">
            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#16a34a]">
                {{ $title }}
            </p>
            @if ($subtitle)
                <p class="text-[11px] text-[#94a3b8]">
                    {{ $subtitle }}
                </p>
            @endif
        </div>

        <div class="ml-auto flex items-center gap-3">
            @if($headerRight)
                <span class="text-[11px] text-[#64748b]">
                    {{ $headerRight }}
                </span>
            @endif

            {{ $actions ?? '' }}

            @if ($count !== null)
                <x-feedback-status.status-indicator variant="brand">
                    {{ $count }}
                </x-feedback-status.status-indicator>
            @endif
        </div>

    </div>

    <div class="{{ $padded ? 'p-4' : '' }}">
        {{ $slot }}
    </div>

</div>
