<div {{ $attributes->merge([
        'class' => 'bg-white text-sm border border-[#E3E8EB] rounded-[16px] p-3.5
                    shadow-[0_1px_2px_rgba(16,24,40,0.04),0_1px_3px_rgba(16,24,40,0.06)]
                    hover:shadow-[0_4px_8px_rgba(16,24,40,0.08)]
                    hover:border-[#AEFFE2]
                    transition-all duration-200
                    text-[#394056] flex gap-2'
    ]) }}>
    {{ $slot }}
</div>
