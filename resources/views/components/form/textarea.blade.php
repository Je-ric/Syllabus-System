{{-- x-form.textarea --}}
@props(['rows' => 3, 'placeholder' => ''])

<textarea
    rows="{{ $rows }}"
    placeholder="{{ $placeholder }}"
    {{ $attributes->merge([
        'class' => '
            w-full rounded-lg bg-white
            border border-[#e2e8f0]
            px-3 py-2 text-[13px] text-[#0f172a]
            placeholder:text-[#94a3b8]
            hover:border-[#bbf7d0]
            focus:border-[#16a34a] focus:outline-none
            disabled:bg-[#f8fafc] disabled:text-[#94a3b8] disabled:cursor-not-allowed disabled:border-[#e2e8f0]
            resize-y transition-colors duration-150
        '
    ]) }}
    style="box-shadow: none;"
    onfocus="this.style.boxShadow='0 0 0 3px rgba(22,163,74,0.25)'"
    onblur="this.style.boxShadow='none'"
>{{ $slot }}</textarea>
