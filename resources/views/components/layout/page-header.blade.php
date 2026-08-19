@props([
    'icon'    => null,
    'title',
    'desc'    => null,
    'eyebrow' => null,
    'class'   => '',
])

<div {{ $attributes->merge(['class' => 'relative w-full bg-white ' . $class]) }}>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-6 py-5">

        <div class="flex items-center gap-4 min-w-0">
            @if ($icon)
                <span class="shrink-0 flex items-center justify-center w-11 h-11 rounded-[12px]
                            bg-[linear-gradient(145deg,#00D88B_0%,#06754E_100%)]
                            shadow-[0_4px_14px_rgba(0,150,95,0.32)]">
                    <i class="bx {{ $icon }} text-xl leading-none text-white"></i>
                </span>
            @endif

            <div class="min-w-0">
                @if ($eyebrow)
                    <p class="text-[10.5px] font-bold uppercase tracking-[0.14em] text-[#00965F] mb-0.5">
                        {{ $eyebrow }}
                    </p>
                @endif
                <h1 class="text-[19px] font-extrabold text-[#1D2836] leading-tight tracking-tight truncate">
                    {{ $title }}
                </h1>
                @if ($desc)
                    <p class="text-[13px] mt-1 leading-snug text-[#72809E] max-w-xl">
                        {{ $desc }}
                    </p>
                @endif
            </div>
        </div>

        @if ($slot->isNotEmpty())
            <div class="shrink-0 flex items-center gap-2 sm:justify-end">
                {{ $slot }}
            </div>
        @endif
    </div>

    {{-- Accent rail — same gradient language as x-modal.dialog's top rail --}}
    <div class="h-[2.5px] w-full"
         style="background:linear-gradient(90deg,#00D88B 0%,#00C075 35%,rgba(0,216,139,0) 100%);"></div>
</div>