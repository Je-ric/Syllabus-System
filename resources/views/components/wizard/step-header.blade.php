@props([
    'title',
    'description' => null,
    'icon' => null,
    'eyebrow' => null,
])

<div class="mb-8">

    <div class="flex items-start justify-between gap-4">

        <div class="flex items-start gap-4 flex-1 min-w-0">

            <div
                class="w-1 self-stretch rounded-full bg-linear-to-b from-[#009639] via-[#16a34a] to-[#86efac]">
            </div>

            <div class="min-w-0">

                @if($eyebrow)
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="h-px w-6 bg-[#009639]"></span>

                        <span
                            class="text-[11px] font-semibold uppercase
                                   tracking-[0.18em] text-[#009639]">
                            {{ $eyebrow }}
                        </span>
                    </div>
                @endif

                <h2 class="text-[22px] font-bold tracking-tight text-slate-900">
                    {{ $title }}
                </h2>

                @if($description)
                    <p class="mt-1.5 text-[14px] leading-relaxed text-slate-500 max-w-3xl">
                        {{ $description }}
                    </p>
                @endif

            </div>

        </div>

        @if($slot->isNotEmpty())
            <div class="flex items-center gap-2 shrink-0">
                {{ $slot }}
            </div>
        @endif

    </div>

</div>