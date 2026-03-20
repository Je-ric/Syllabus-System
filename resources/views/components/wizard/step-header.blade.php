@props([
    'title',
    'description' => null,
    'icon'        => null,
])

<div class="mb-5 pb-4 border-b border-slate-100" role="region" aria-label="{{ $title }}">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">

        {{-- Left: icon + text ──────────────────────────────────────────────── --}}
        <div class="flex items-start gap-3 min-w-0 flex-1">

            @if ($icon)
                <div aria-hidden="true"
                    class="shrink-0 mt-0.5 flex items-center justify-center
                           w-9 h-9 rounded-xl
                           bg-gradient-to-br from-emerald-500 to-[#009639]
                           text-white shadow-sm">
                    <i class="bx bx-{{ $icon }} text-lg leading-none"></i>
                </div>
            @endif

            <div class="min-w-0">
                <h3 class="text-lg font-bold tracking-tight text-slate-900 leading-snug">
                    {{ $title }}
                </h3>
                @if ($description)
                    <p class="mt-0.5 text-xs text-slate-500 leading-relaxed max-w-2xl">
                        {{ $description }}
                    </p>
                @endif
            </div>
        </div>

        {{-- Right: action slot — only rendered when caller provides content ── --}}
        @if ($slot->isNotEmpty())
            <div class="flex items-center flex-wrap gap-2 shrink-0 sm:pt-0.5 sm:pl-4">
                {{ $slot }}
            </div>
        @endif

    </div>
</div>
