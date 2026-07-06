@props([
    'id',
    'maxWidth' => 'max-w-2xl',
    'width'    => 'w-full sm:w-11/12',
    'variant'  => 'default',
    'class'    => '',
])

@php
    $borderClass = match($variant) {
        'delete'  => 'border-red-400',
        'warning' => 'border-amber-400',
        'info'    => 'border-blue-400',
        default   => 'border-green-700',
    };
    $shadowStyle = match($variant) {
        'delete'  => 'box-shadow: 0 8px 40px rgba(225,29,72,0.15);',
        'warning' => 'box-shadow: 0 8px 40px rgba(245,158,11,0.15);',
        'info'    => 'box-shadow: 0 8px 40px rgba(59,130,246,0.15);',
        default   => 'box-shadow: 0 8px 40px rgba(22,163,74,0.18);',
    };
@endphp

<dialog id="{{ $id }}" class="modal" {{ $attributes }}>
    <div class="modal-box {{ $width }} {{ $maxWidth }} max-h-[90vh] p-0 overflow-hidden rounded-xl bg-white flex flex-col
                border-t-4 {{ $borderClass }} {{ $class }}"
        style="{{ $shadowStyle }} min-width: min(540px, 94vw);">
        {{ $slot }}
    </div>
</dialog>
