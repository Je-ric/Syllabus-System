@props([
    'icon'  => null,
    'title',
    'desc'  => null,
    'class' => '',
])

<div {{ $attributes->merge(['class' => 'w-full border-b bg-white border-[#e4e4e7] ' . $class]) }}
    {{-- style="box-shadow: 0 1px 4px rgba(0,0,0,0.04);" --}}
    >

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-4">

        {{-- Left: icon + text --}}
        <div class="flex items-center gap-3.5 min-w-0">

            @if ($icon)
                <span class="shrink-0 flex items-center justify-center
                            w-10 h-10 rounded-[14px]
                            bg-[#f0fdf4] border border-[#d1fae5]">
                    <i class="bx {{ $icon }} text-lg leading-none text-[#16a34a]"></i>
                </span>
            @endif

            <div class="min-w-0">
                <h1 class="text-base font-bold text-[#09090b] leading-tight truncate">
                    {{ $title }}
                </h1>

                @if ($desc)
                    <p class="text-[13.5px] mt-0.5 leading-snug text-[#52525b]">
                        {{ $desc }}
                    </p>
                @endif
            </div>

        </div>

        {{-- Right: actions slot --}}
        @if ($slot->isNotEmpty())
            <div class="shrink-0 flex items-center gap-2 sm:justify-end">
                {{ $slot }}
            </div>
        @endif

    </div>

</div>
