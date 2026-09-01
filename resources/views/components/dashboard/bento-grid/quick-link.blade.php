@props([
    'href',
    'icon'    => 'bx-link',
    'primary' => false,
    'gold'    => false,
])

@php
    if ($primary) {
        $classes = 'bg-[#003a10] text-white hover:bg-[#1a5f30] border-transparent';
        $iconClasses = 'text-white/70';
    } elseif ($gold) {
        $classes = 'bg-[#e0a70d] text-white hover:bg-[#c8940b] border-transparent';
        $iconClasses = 'text-white/70';
    } else {
        $classes = 'bg-[#f2f6f5] text-[#003a10] hover:bg-[#e4ede9] border-[#c8ddd4]';
        $iconClasses = 'text-[#1a5f30]';
    }
@endphp

<a href="{{ $href }}"
   class="flex items-center gap-3 w-full px-4 py-3 rounded-xl border font-semibold text-sm
          transition-colors min-h-[44px] {{ $classes }}">
    <i class="bx {{ $icon }} text-base leading-none shrink-0 {{ $iconClasses }}"></i>
    <span>{{ $slot }}</span>
</a>
