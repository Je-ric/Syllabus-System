<x-modal.dialog id="updateProgramModal_{{ $program->id }}" maxWidth="max-w-md" width="w-11/12">
    <x-modal.header modalId="updateProgramModal_{{ $program->id }}">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-[#eff6ff] text-[#1d4ed8] shrink-0">
                <i class="bx bx-edit text-base leading-none"></i>
            </span>
            <div>
                <p class="text-[15px] font-bold text-[#0f172a]">Edit Program</p>
                <p class="text-[13px] text-[#94a3b8] truncate">{{ $program->name }}</p>
            </div>
        </div>
    </x-modal.header>

    <form method="POST" action="{{ route('program.update', $program) }}" class="flex flex-col">
        @csrf
        @method('PUT')
        <input type="hidden" name="department_id" value="{{ $program->department_id }}">
        <x-modal.body>
            <div class="space-y-4">
                <div>
                    <x-modal.modal-label for="editProgramName_{{ $program->id }}" isRequired>Program Name</x-modal.modal-label>
                    <x-form.input
                        id="editProgramName_{{ $program->id }}"
                        type="text"
                        name="name"
                        value="{{ $program->name }}"
                        required />
                </div>
                <div>
                    <x-modal.modal-label for="editBorNo_{{ $program->id }}" isRequired>BOR Approval No.</x-modal.modal-label>
                    <x-form.input
                        id="editBorNo_{{ $program->id }}"
                        type="text"
                        name="bor_approval_no"
                        value="{{ $program->bor_approval_no }}"
                        required />
                </div>
                <div>
                    <x-modal.modal-label for="editBorDate_{{ $program->id }}" isRequired>BOR Approval Date</x-modal.modal-label>
                    <x-form.input
                        id="editBorDate_{{ $program->id }}"
                        type="date"
                        name="bor_approval_date"
                        value="{{ $program->bor_approval_date }}"
                        required />
                </div>
            </div>
        </x-modal.body>
        <x-modal.footer>
            <x-modal.close-button :modalId="'updateProgramModal_' . $program->id" text="Cancel" />
            <x-button type="submit" variant="save">
                <i class="bx bx-save"></i> Save Changes
            </x-button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
