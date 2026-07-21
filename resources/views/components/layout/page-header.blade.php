@props([
    'icon'  => null,
    'title',
    'desc'  => null,
    'class' => '',
])

<div {{ $attributes->merge(['class' => 'w-full border-b bg-white border-[#E3E8EB] ' . $class]) }}>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-4">

        {{-- Left: icon + text --}}
        <div class="flex items-center gap-3.5 min-w-0">

            @if ($icon)
                <span class="shrink-0 flex items-center justify-center
                            w-10 h-10 rounded-[10px]
                            bg-[#D5FFF0] border border-[#00C075]">
                    <i class="bx {{ $icon }} text-lg leading-none text-[#06754E]"></i>
                </span>
            @endif

            <div class="min-w-0">
                <h1 class="text-base font-bold text-[#394056] leading-tight truncate">
                    {{ $title }}
                </h1>

                @if ($desc)
                    <p class="text-[13px] mt-0.5 leading-snug text-[#72809E]">
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
