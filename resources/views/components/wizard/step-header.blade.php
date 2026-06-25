@props([
    'title',
    'description' => null,
    'icon'        => null,
    'eyebrow'     => null,
    'step'        => null,
])

<div class="mb-6">

    <div class="flex items-start justify-between gap-4">

        <div class="flex items-start gap-3.5 flex-1 min-w-0">

            {{-- Brand accent bar --}}
            <div class="w-1 self-stretch rounded-full shrink-0"
                 style="background: linear-gradient(180deg, #ffd700 0%, #009639 50%, #86efac 100%); min-height: 2.5rem;"></div>

            <div class="min-w-0">

                @if ($eyebrow)
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="h-px w-5 bg-[#009639]"></span>
                        <span class="text-xs font-semibold uppercase tracking-widest text-[#009639]">
                            {{ $eyebrow }}
                        </span>
                    </div>
                @endif

                <div class="flex items-center gap-2.5 flex-wrap">
                    @if ($step)
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold text-white shrink-0"
                              style="background: linear-gradient(135deg, #009639 0%, #16a34a 100%); box-shadow: 0 2px 6px rgba(0,150,57,0.35);">
                            {{ $step }}
                        </span>
                    @endif
                    <h2 class="text-xl font-bold tracking-tight text-slate-900 leading-snug">
                        {{ $title }}
                    </h2>
                </div>

                @if ($description)
                    <p class="mt-1.5 text-sm leading-relaxed text-slate-500 max-w-2xl">
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
