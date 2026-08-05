@props([
    'title',
    'description' => null,
    'icon'        => null,
    'eyebrow'     => null,
    'step'        => null,
    'total'       => 6,
])

@php
    $stepNum = $step !== null ? (int) $step : null;
@endphp

<div class="mb-6 relative overflow-hidden rounded-[14px] border border-[#e4e4e7]"
     style="background: linear-gradient(115deg, color-mix(in srgb, #00965F 7%, #fff) 0%, #ffffff 55%);">

    {{-- Brand accent bar — Emerald ramp --}}
    <span class="absolute inset-y-0 left-0 w-[3px]"
          style="background: linear-gradient(180deg,#00C075 0%,#00965F 100%);"></span>

    <div class="flex items-start justify-between gap-4 pl-5 pr-4 py-4">
        <div class="flex items-start gap-3.5 flex-1 min-w-0">

            @if ($icon)
                <span class="shrink-0 w-10 h-10 rounded-[12px] flex items-center justify-center text-white text-[20px]"
                      style="background: linear-gradient(180deg,#00C075 0%,#00965F 100%);
                             box-shadow: 0 3px 10px rgba(0,150,95,0.28);">
                    <i class="bx {{ $icon }}"></i>
                </span>
            @endif

            <div class="min-w-0">

                {{-- Eyebrow: step counter + progress ticks --}}
                @if ($stepNum || $eyebrow)
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-[#00965F]">
                            {{ $eyebrow ?? "Step {$stepNum} of {$total}" }}
                        </span>
                        @if ($stepNum)
                            <span class="flex items-center gap-[3px]">
                                @for ($i = 1; $i <= $total; $i++)
                                    <span class="h-[3px] rounded-full transition-all duration-300
                                                 {{ $i === $stepNum ? 'w-4 bg-[#00965F]' : ($i < $stepNum ? 'w-2 bg-[#86efac]' : 'w-2 bg-[#e4e4e7]') }}"></span>
                                @endfor
                            </span>
                        @endif
                    </div>
                @endif

                <h2 class="text-xl font-bold tracking-tight text-[#394056] leading-snug">
                    {{ $title }}
                </h2>

                @if ($description)
                    <p class="mt-1 text-[13px] leading-relaxed text-[#72809E] max-w-2xl">
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
