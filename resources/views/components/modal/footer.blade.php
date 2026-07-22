@props(['align' => 'end'])

@php
    $alignClass = match($align) {
        'start'  => 'justify-start',
        'center' => 'justify-center',
        default  => 'justify-end',
    };
@endphp

{{-- Footer: Grey 200 (#F9FAFA) bg, --border-subtle (#F1F3F5) top divider --}}
<footer {{ $attributes->class([
    'border-t border-[#F1F3F5] bg-[#F9FAFA] px-5 py-3.5 flex gap-3 flex-shrink-0',
    $alignClass,
]) }}>
    {{ $slot }}
</footer>
