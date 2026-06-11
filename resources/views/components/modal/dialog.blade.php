@props([
    'id',
    'maxWidth' => 'max-w-2xl',
    'width'    => 'w-full sm:w-11/12',
    'variant'  => 'default',
    'class'    => '',
])

@php
    $borderClass = $variant === 'delete' ? 'border-red-400' : 'border-green-700';
    $shadowStyle = $variant === 'delete'
        ? 'box-shadow: 0 8px 40px rgba(225,29,72,0.15);'
        : 'box-shadow: 0 8px 40px rgba(22,163,74,0.18);';
@endphp

<dialog id="{{ $id }}" class="modal" {{ $attributes }}>
    <div class="modal-box {{ $width }} {{ $maxWidth }} max-h-[90vh] p-0 overflow-hidden rounded-xl bg-white flex flex-col
                border-t-4 {{ $borderClass }} {{ $class }}"
        style="{{ $shadowStyle }} min-width: min(540px, 94vw);">
        {{ $slot }}
    </div>
</dialog>
