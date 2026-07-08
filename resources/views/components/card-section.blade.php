@props([
    'title'    => null,
    'icon'     => null,
    'count'    => null,
    'subtitle' => null,
    'padded'   => true,
])

<div {{ $attributes->merge([
    'class' => 'rounded-[16px] border border-[#e4e4e7] bg-white overflow-hidden'
]) }} style="box-shadow: 0 1px 8px rgba(0,0,0,0.05);">

    <div class="px-5 py-3.5 border-b border-[#e4e4e7] flex items-center gap-3">
        @if($icon)
            <i class="bx {{ $icon }} text-[#16a34a] text-base leading-none shrink-0"></i>
        @endif
        <div class="flex flex-col gap-0.5 min-w-0">
            <p class="text-[12px] font-bold uppercase tracking-[0.08em] text-[#3f3f46] leading-none">
                {{ $title }}
            </p>
            @if ($subtitle)
                <p class="text-[12px] text-[#71717a] leading-none truncate">{{ $subtitle }}</p>
            @endif
        </div>

        <div class="ml-auto flex items-center gap-2.5 shrink-0">
            {{ $actions ?? '' }}

            @if ($count !== null)
                <span class="inline-flex items-center justify-center min-w-[1.5rem] h-5 px-1.5
                             rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold
                             border border-[#86efac]">
                    {{ $count }}
                </span>
            @endif
        </div>
    </div>

    <div class="{{ $padded ? 'p-4' : '' }}">
        {{ $slot }}
    </div>

</div>
