@props([
    'icon'    => 'bx-inbox',
    'title'   => 'Nothing here yet',
    'message' => 'There is nothing to show right now.',
])

<div {{ $attributes->class([
    'rounded-xl border-2 border-dashed border-[#d1d5db] bg-[#f9fafb] p-8 sm:p-10 text-center'
]) }}>
    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-xl bg-[#f0fdf4] text-[#16a34a]">
        <i class="bx {{ $icon }} text-3xl leading-none"></i>
    </div>

    <h3 class="text-[15px] font-bold text-[#0f172a]">{{ $title }}</h3>
    <p class="mt-1.5 text-[13px] text-[#475569] max-w-sm mx-auto leading-relaxed">{{ $message }}</p>

    @if ($slot->isNotEmpty())
        <div class="mt-5 flex justify-center gap-2 flex-wrap">
            {{ $slot }}
        </div>
    @endif
</div>
