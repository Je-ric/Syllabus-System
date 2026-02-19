@props([
    'icon' => 'bx-inbox',
    'title' => 'No results yet',
    'message' => 'There is nothing to show right now.',
])

<div {{ $attributes->class('rounded-2xl border border-dashed border-slate-300 bg-slate-50/90 p-6 sm:p-8 text-center') }}>
    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-white border border-slate-200 text-slate-500">
        <i class="bx {{ $icon }} text-2xl leading-none"></i>
    </div>

    <h3 class="text-base sm:text-lg font-semibold text-slate-800">{{ $title }}</h3>
    <p class="mt-1 text-sm text-slate-500 max-w-xl mx-auto">{{ $message }}</p>

    @if(trim($slot))
        <div class="mt-4">
            {{ $slot }}
        </div>
    @endif
</div>
