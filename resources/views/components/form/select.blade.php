{{-- x-form.select --}}
@props(['name' => null])

<div class="relative">
    <select
        @if($name) name="{{ $name }}" @endif
        {{ $attributes->merge([
            'class' => '
                w-full appearance-none rounded-[14px] bg-white
                border border-[#d4d4d8]
                px-3.5 py-2.5 pr-9 text-[14px] text-[#09090b]
                hover:border-[#a1a1aa]
                focus:border-[#16a34a] focus:outline-none focus:ring-2 focus:ring-[#16a34a]/15
                disabled:bg-[#f4f4f5] disabled:text-[#a1a1aa] disabled:cursor-not-allowed disabled:border-[#e4e4e7]
                transition-colors duration-150
            '
        ]) }}
    >
        {{ $slot }}
    </select>

    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-[#a1a1aa]">
        <i class="bx bx-chevron-down text-base leading-none"></i>
    </span>
</div>
