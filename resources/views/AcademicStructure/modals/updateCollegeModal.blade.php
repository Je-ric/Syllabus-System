<x-modal.dialog id="updateCollegeModal_{{ $college->id }}" maxWidth="max-w-md" width="w-11/12">
    <x-modal.header modalId="updateCollegeModal_{{ $college->id }}" variant="edit">
        <div>
            <p class="text-[15px] font-bold text-[#0f172a]">Edit College</p>
            <p class="text-[13px] text-[#94a3b8] truncate">{{ $college->name }}</p>
        </div>
    </x-modal.header>

    <form method="POST" action="{{ route('college.update', $college) }}" class="flex flex-col">
        @csrf
        @method('PUT')
        <x-modal.body>
            <div>
                <x-modal.modal-label for="editCollegeName_{{ $college->id }}" isRequired>College Name</x-modal.modal-label>
                <x-form.input
                    id="editCollegeName_{{ $college->id }}"
                    type="text"
                    name="name"
                    value="{{ $college->name }}"
                    required />
            </div>
        </x-modal.body>
        <x-modal.footer>
            <x-modal.close-button :modalId="'updateCollegeModal_' . $college->id" text="Cancel" />
            <x-button type="submit" variant="save">
                <i class="bx bx-save"></i> Save Changes
            </x-button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
