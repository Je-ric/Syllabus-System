{{-- resources/views/components/divider.blade.php --}}
@props(['label' => null])

@if ($label)
    <div {{ $attributes->merge(['class' => 'pt-1']) }}>
        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#8aa19b] mb-2">
            {{ $label }}
        </p>
        <div class="border-t border-[#dce8e5]"></div>
    </div>
@else
    <div {{ $attributes->merge(['class' => 'border-t border-[#dce8e5]']) }}></div>
@endif
