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

            {{-- Brand accent bar --}}
            <div class="w-[3px] self-stretch rounded-full shrink-0 bg-[#16a34a] min-h-[2.5rem]"></div>

            <div class="min-w-0">
                @if ($eyebrow)
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="h-px w-4 bg-[#16a34a]"></span>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-[#16a34a]">
                            {{ $eyebrow }}
                        </span>
                    </div>
                @endif

                <div class="flex items-center gap-2.5 flex-wrap">
                    @if ($step)
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold text-white shrink-0 bg-[#16a34a]"
                              style="box-shadow: 0 2px 6px rgba(22,163,74,0.3);">
                            {{ $step }}
                        </span>
                    @endif
                    <h2 class="text-xl font-bold tracking-tight text-[#09090b] leading-snug">
                        {{ $title }}
                    </h2>
                </div>

                @if ($description)
                    <p class="mt-1.5 text-sm leading-relaxed text-[#71717a] max-w-2xl">
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
