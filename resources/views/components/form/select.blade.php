{{-- x-form.select --}}
@props(['name' => null])

<div class="relative">
    <select
        @if($name) name="{{ $name }}" @endif
        {{ $attributes->merge([
            'class' => '
                w-full appearance-none rounded-lg bg-white
                border border-[#e2e8f0]
                px-3 py-2 pr-9 text-[13px] text-[#0f172a]
                hover:border-[#bbf7d0]
                focus:border-[#16a34a] focus:outline-none
                disabled:bg-[#f8fafc] disabled:text-[#94a3b8] disabled:cursor-not-allowed disabled:border-[#e2e8f0]
                transition-colors duration-150
            '
        ]) }}
        style="box-shadow: none;"
        onfocus="this.style.boxShadow='0 0 0 3px rgba(22,163,74,0.25)'"
        onblur="this.style.boxShadow='none'"
    >
        {{ $slot }}
    </select>

    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-[#94a3b8]">
        <i class="bx bx-chevron-down text-base leading-none"></i>
    </span>
</div>
