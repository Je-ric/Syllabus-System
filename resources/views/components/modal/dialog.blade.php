@props([
    'id',
    'maxWidth' => 'max-w-lg',
    'width'    => 'w-11/12',
    'class'    => '',
])

<dialog id="{{ $id }}" class="modal backdrop-blur-sm" {{ $attributes }}>
    <div class="modal-box {{ $width }} {{ $maxWidth }} max-h-[88vh] p-0 overflow-hidden rounded-xl bg-white flex flex-col {{ $class }}"
         style="box-shadow: 0 8px 32px rgba(22,163,74,0.13);">
        {{ $slot }}
    </div>
</dialog>
