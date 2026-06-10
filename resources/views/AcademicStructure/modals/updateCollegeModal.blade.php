<x-modal.dialog id="updateCollegeModal_{{ $college->id }}" maxWidth="max-w-md" width="w-11/12">
    <x-modal.header modalId="updateCollegeModal_{{ $college->id }}">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-[#eff6ff] text-[#1d4ed8] shrink-0">
                <i class="bx bx-edit text-base leading-none"></i>
            </span>
            <div>
                <p class="text-[15px] font-bold text-[#0f172a]">Edit College</p>
                <p class="text-[13px] text-[#94a3b8] truncate">{{ $college->name }}</p>
            </div>
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
