@props([
    'title',
    'description' => null,
    'icon'        => null,
])

<div class="mb-6 pb-5 border-b border-slate-100" role="region" aria-label="{{ $title }}">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

        {{-- Left: icon + text block ─────────────────────────────────────────── --}}
        <div class="flex items-start gap-3 min-w-0">

            {{-- Icon bubble — gives instant visual context for the step --}}
            @if ($icon)
                <div aria-hidden="true"
                    class="shrink-0 mt-0.5 flex items-center justify-center
                           w-10 h-10 rounded-xl
                           bg-gradient-to-br from-emerald-500 to-[#009639]
                           text-white shadow-sm">
                    <i class="bx bx-{{ $icon }} text-xl leading-none"></i>
                </div>
            @endif

            <div class="min-w-0">

                {{-- Primary heading — largest element, read first --}}
                <h3 class="text-xl font-bold tracking-tight text-slate-900 leading-snug">
                    {{ $title }}
                </h3>

                {{-- Secondary description — smaller, muted, guides intent --}}
                @if ($description)
                    <p class="mt-1 text-sm text-slate-500 leading-relaxed max-w-2xl">
                        {{ $description }}
                    </p>
                @endif

            </div>
        </div>

        {{-- Right: action slot — tertiary; only visible if caller provides content --}}
        @if ($slot->isNotEmpty())
            <div class="flex items-center flex-wrap gap-2 sm:shrink-0 sm:pt-0.5">
                {{ $slot }}
            </div>
        @endif

    </div>
</div>
