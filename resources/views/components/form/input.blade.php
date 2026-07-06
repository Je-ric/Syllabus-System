{{-- x-form.input --}}
@props(['type' => 'text'])

<input
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => '
            w-full rounded-[14px] bg-white
            border border-[#d4d4d8]
            px-3 py-2 text-[13px] text-[#09090b]
            placeholder:text-[#a1a1aa]
            hover:border-[#a1a1aa]
            focus:border-[#16a34a] focus:outline-none
            disabled:bg-[#f4f4f5] disabled:text-[#a1a1aa] disabled:cursor-not-allowed disabled:border-[#e4e4e7]
            transition-colors duration-150
        '
    ]) }}
    style="box-shadow: none;"
    onfocus="this.style.boxShadow='0 0 0 3px rgba(22,163,74,0.15)'"
    onblur="this.style.boxShadow='none'"
/>
