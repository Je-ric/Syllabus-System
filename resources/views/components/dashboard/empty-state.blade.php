@props([
    'icon'    => 'bx-inbox',
    'title'   => 'Nothing here yet',
    'message' => '',
])

<div class="flex flex-col items-center justify-center text-center gap-3 py-8 px-4">
    <span class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-slate-100 text-slate-400">
        <i class="bx {{ $icon }} text-xl leading-none"></i>
    </span>
    <div>
        <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-slate-500">{{ $title }}</p>
        @if ($message)
            <p class="text-[11px] mt-1 leading-relaxed text-slate-400 max-w-[180px] mx-auto">{{ $message }}</p>
        @endif
    </div>
    @if ($slot->isNotEmpty())
        <div class="mt-1">{{ $slot }}</div>
    @endif
</div>
