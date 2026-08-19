@props([
    'modalId' => null,
    'text'    => 'Close',
    'class'   => '',
    'variant' => 'close',
])

{{-- Close/Cancel button — Design.md § 4.2: --radius-sm (8px), Grey/Charcoal scale --}}
<button
    type="button"
    onclick="document.getElementById('{{ $modalId }}').close()"
    {{ $attributes->merge(['class' => "inline-flex items-center gap-2 px-4 py-2 text-[13px] font-semibold
        rounded-[8px] border border-[#E3E8EB] bg-white text-[#394056]
        hover:bg-[#F1F3F5] hover:border-[#D6DDE3] hover:text-[#253540]
        active:scale-[0.96]
        focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D6DDE3]
        transition-all duration-150 $class"]) }}
>
    {{ $text }}
</button>
