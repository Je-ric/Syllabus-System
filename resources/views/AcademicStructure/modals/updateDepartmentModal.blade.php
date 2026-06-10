<x-modal.dialog id="updateDepartmentModal_{{ $dept->id }}" maxWidth="max-w-md" width="w-11/12">
    <x-modal.header modalId="updateDepartmentModal_{{ $dept->id }}">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-[#eff6ff] text-[#1d4ed8] shrink-0">
                <i class="bx bx-edit text-base leading-none"></i>
            </span>
            <div>
                <p class="text-[15px] font-bold text-[#0f172a]">Edit Department</p>
                <p class="text-[13px] text-[#94a3b8] truncate">{{ $dept->name }}</p>
            </div>
        </div>
    </x-modal.header>

    <form method="POST" action="{{ route('department.update', $dept) }}" class="flex flex-col">
        @csrf
        @method('PUT')
        <input type="hidden" name="college_id" value="{{ $dept->college_id }}">
        <x-modal.body>
            <div>
                <x-modal.modal-label for="editDeptName_{{ $dept->id }}" isRequired>Department Name</x-modal.modal-label>
                <x-form.input
                    id="editDeptName_{{ $dept->id }}"
                    type="text"
                    name="name"
                    value="{{ $dept->name }}"
                    required />
            </div>
        </x-modal.body>
        <x-modal.footer>
            <x-modal.close-button :modalId="'updateDepartmentModal_' . $dept->id" text="Cancel" />
            <x-button type="submit" variant="save">
                <i class="bx bx-save"></i> Save Changes
            </x-button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
