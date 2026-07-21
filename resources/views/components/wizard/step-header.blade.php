@props([
    'title',
    'description' => null,
    'icon'        => null,
    'eyebrow'     => null,
    'step'        => null,
])

<div class="mb-6">
    <div class="flex items-start justify-between gap-4">
        <div class="flex items-start gap-3 flex-1 min-w-0">

            {{-- Brand accent bar — Emerald 700 --}}
            <div class="w-[3px] self-stretch rounded-full shrink-0 bg-[#00965F] min-h-[2.5rem]"></div>

            <div class="min-w-0">
                @if ($eyebrow)
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="h-px w-4 bg-[#00965F]"></span>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-[#00965F]">
                            {{ $eyebrow }}
                        </span>
                    </div>
                @endif

                <div class="flex items-center gap-2.5 flex-wrap">
                    @if ($step)
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full
                                     text-xs font-bold text-white shrink-0
                                     bg-[linear-gradient(180deg,#00C075_0%,#00965F_100%)]"
                              style="box-shadow: 0 2px 6px rgba(0,150,95,0.30);">
                            {{ $step }}
                        </span>
                    @endif
                    <h2 class="text-xl font-bold tracking-tight text-[#394056] leading-snug">
                        {{ $title }}
                    </h2>
                </div>

                @if ($description)
                    <p class="mt-1.5 text-[13px] leading-relaxed text-[#72809E] max-w-2xl">
                        {{ $description }}
                    </p>
                @endif
            </div>

        </div>

        @if ($slot->isNotEmpty())
            <div class="flex items-center gap-2 shrink-0 mt-0.5">
                {{ $slot }}
            </div>
        @endif
    </div>
</div>
