@props(['number', 'label'])

<div {{ $attributes->class(['flex items-center gap-3 mb-3 mt-5 first:mt-0']) }}>
    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full
                 bg-[#16a34a] text-white text-[12px] font-bold shrink-0">
        {{ $number }}
    </span>
    <h3 class="text-[12px] font-bold text-[#09090b] uppercase tracking-widest">
        {{ $label }}
    </h3>
    <div class="flex-1 h-px bg-[#e4e4e7]"></div>
</div>
