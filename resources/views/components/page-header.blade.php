@props([
    'icon'  => null,
    'title',
    'desc'  => null,
    'class' => '',
])

<div {{ $attributes->merge(['class' => 'w-full ' . $class]) }}
     style="background: linear-gradient(135deg, #002a0c 0%, #004d16 60%, #006622 100%); border-bottom: 3px solid #ffd700;">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-5">

        {{-- ── Left: icon + text ── --}}
        <div class="flex items-center gap-4 min-w-0">

            @if ($icon)
                <span class="shrink-0 flex items-center justify-center
                            w-11 h-11 rounded-xl border border-white/20
                            shadow-lg"
                      style="background: rgba(255,255,255,0.12);">
                    <i class="bx {{ $icon }} text-xl leading-none text-white"></i>
                </span>
            @endif

            <div class="min-w-0">
                <h1 class="text-base font-bold text-white leading-tight truncate">
                    {{ $title }}
                </h1>
                @if ($desc)
                    <p class="text-sm mt-0.5 leading-relaxed truncate" style="color: rgba(255,255,255,0.65);">
                        {{ $desc }}
                    </p>
                @endif
            </div>

        </div>

        {{-- ── Right: actions slot ── --}}
        @if ($slot->isNotEmpty())
            <div class="shrink-0 flex items-center gap-2 sm:justify-end bg-white/95 rounded-lg p-0.5 shadow-inner">
                {{ $slot }}
            </div>
        @endif

    </div>

</div>
