@props([
    'id',
    'maxWidth' => 'max-w-2xl',
    'width'    => 'w-full sm:w-11/12',
    'class'    => '',
])

<dialog id="{{ $id }}" class="modal" {{ $attributes }}>
    <div class="modal-box {{ $width }} {{ $maxWidth }} max-h-[90vh] p-0 overflow-hidden rounded-xl bg-white flex flex-col
                border-t-4 border-green-700 {{ $class }}"
        style="box-shadow: 0 8px 40px rgba(22,163,74,0.18); min-width: min(540px, 94vw);">
        {{ $slot }}
    </div>
</dialog>
