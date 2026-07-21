@props([
    'title'    => null,
    'icon'     => null,
    'count'    => null,
    'subtitle' => null,
    'padded'   => true,
])

<div {{ $attributes->merge([
    'class' => 'rounded-[12px] border border-[#E3E8EB] bg-white overflow-hidden
                shadow-[0_1px_2px_rgba(16,24,40,0.04),0_1px_3px_rgba(16,24,40,0.06)]'
]) }}>

    <div class="px-4 py-3 border-b border-[#E3E8EB] flex items-center gap-2.5">
        @if($icon)
            <i class="bx {{ $icon }} text-[#00965F] text-base leading-none shrink-0"></i>
        @endif
        <div class="flex flex-col gap-0.5 min-w-0">
            <p class="text-[11.5px] font-bold uppercase tracking-[0.08em] text-[#394056] leading-none">
                {{ $title }}
            </p>
            @if ($subtitle)
                <p class="text-[11.5px] text-[#72809E] leading-none truncate">{{ $subtitle }}</p>
            @endif
        </div>

        <div class="ml-auto flex items-center gap-2 shrink-0">
            {{ $actions ?? '' }}

            @if ($count !== null)
                <span class="inline-flex items-center justify-center min-w-[1.375rem] h-[1.375rem] px-1.5
                             rounded-full bg-[#D5FFF0] text-[#06754E] text-[10.5px] font-bold
                             border border-[#00965F]">
                    {{ $count }}
                </span>
            @endif
        </div>
    </div>

    <div class="{{ $padded ? 'p-4' : '' }}">
        {{ $slot }}
    </div>

</div>
