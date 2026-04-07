@props([
    'title',
    'description' => null,
    'icon'        => null,
])

<div class="mb-6 pb-5 border-b border-[#e2e8f0]" role="region" aria-label="{{ $title }}">

    {{-- Row 1: icon + title + action buttons (all on one line, no wrap) --}}
    <div class="flex items-center justify-between gap-4 min-w-0">

        <div class="flex items-center gap-3 min-w-0 flex-1">
            @if ($icon)
                <div aria-hidden="true"
                    class="shrink-0 flex items-center justify-center w-9 h-9 rounded-xl text-white"
                    style="background: linear-gradient(90deg, #003a10 0%, #009639 100%); box-shadow: 0 2px 8px rgba(22,163,74,0.2);">
                    <i class="bx bx-{{ $icon }} text-lg leading-none"></i>
                </div>
            @endif
            <h3 class="text-[15px] font-bold text-[#0f172a] leading-snug truncate">
                {{ $title }}
            </h3>
        </div>

        @if ($slot->isNotEmpty())
            <div class="flex items-center gap-2 shrink-0">
                {{ $slot }}
            </div>
        @endif

    </div>

    {{-- Row 2: description (only if present) --}}
    @if ($description)
        <p class="mt-2 text-[13px] text-[#475569] leading-relaxed max-w-2xl {{ $icon ? 'pl-12' : '' }}">
            {{ $description }}
        </p>
    @endif

</div>
