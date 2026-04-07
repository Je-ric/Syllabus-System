@props([
    'modalId' => null,
    'text'    => 'Close',
    'class'   => '',
    'variant' => 'close',
])

<button
    type="button"
    onclick="document.getElementById('{{ $modalId }}').close()"
    {{ $attributes->merge(['class' => "inline-flex items-center gap-2 px-4 py-2 text-[13px] font-semibold
        rounded-lg border border-[#e2e8f0] bg-white text-[#475569]
        hover:bg-[#f8fafc] hover:border-[#94a3b8] hover:text-[#0f172a]
        transition-colors duration-150 $class"]) }}
>
    {{ $text }}
</button>
