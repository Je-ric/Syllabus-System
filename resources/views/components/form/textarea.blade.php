{{-- x-form.textarea --}}
@props(['rows' => 3, 'placeholder' => ''])

<textarea
    rows="{{ $rows }}"
    placeholder="{{ $placeholder }}"
    {{ $attributes->merge([
        'class' => '
            w-full rounded-[14px] bg-white
            border border-[#d4d4d8]
            px-3.5 py-2.5 text-[14px] text-[#09090b]
            placeholder:text-[#a1a1aa]
            hover:border-[#a1a1aa]
            focus:border-[#16a34a] focus:outline-none focus:ring-2 focus:ring-[#16a34a]/15
            disabled:bg-[#f4f4f5] disabled:text-[#a1a1aa] disabled:cursor-not-allowed disabled:border-[#e4e4e7]
            resize-y transition-colors duration-150
        '
    ]) }}
>{{ $slot }}</textarea>
