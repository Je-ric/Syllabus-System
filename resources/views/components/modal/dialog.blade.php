@props([
    'id',
    'maxWidth' => 'max-w-2xl',
    'width' => 'w-11/12',
    'class' => '',
])

<dialog id="{{ $id }}" class="modal backdrop-blur-md" {{ $attributes }}>
    <div class="modal-box {{ $width }} {{ $maxWidth }} p-0 overflow-hidden rounded-xl bg-white shadow-xl flex flex-col {{ $class }}">
        {{ $slot }}
    </div>
</dialog>

{{--
Usage: <x-modal.dialog id="myModal" maxWidth="max-w-lg">
            <x-modal.header>Modal Title</x-modal.header>
            <div class="p-6">Modal content here</div>
            <x-modal.footer>
                <x-modal.close-button :modalId="'myModal'" text="Cancel" />
                <button type="submit">Save</button>
            </x-modal.footer>
        </x-modal.dialog>
--}}
