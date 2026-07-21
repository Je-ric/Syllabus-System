@props(['number', 'label'])

<div {{ $attributes->class(['flex items-center gap-2.5 mb-3 mt-5 first:mt-0']) }}>
    <span class="inline-flex items-center justify-center w-[22px] h-[22px] rounded-full
                 bg-[#00965F] text-white text-[11px] font-bold shrink-0
                 shadow-[0_1px_2px_rgba(0,150,95,0.30)]">
        {{ $number }}
    </span>
    <h3 class="text-[11px] font-bold text-[#394056] uppercase tracking-[0.1em]">
        {{ $label }}
    </h3>
    <div class="flex-1 h-px bg-[#E3E8EB]"></div>
</div>
