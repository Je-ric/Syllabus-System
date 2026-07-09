<x-modal.dialog id="addProgramModal" maxWidth="max-w-md" width="w-11/12">
    <x-modal.header modalId="addProgramModal" variant="add">
        <div>
            <p class="text-[15px] font-bold text-[#0f172a]">Add New Program</p>
            <p class="text-[13px] text-[#94a3b8]">Will be created under the selected department.</p>
        </div>
    </x-modal.header>

    <form method="POST" action="{{ route('program.store') }}" class="flex flex-col">
        @csrf
        <input type="hidden" name="department_id" id="addProgram_department_id" value="">
        <x-modal.body>
            <div class="space-y-4">
                <div>
                    <x-modal.modal-label isRequired>Program Name</x-modal.modal-label>
                    <x-form.input type="text" name="name" placeholder="e.g. Bachelor of Science in Computer Science" required />
                </div>
                <div>
                    <x-modal.modal-label isRequired>BOR Approval Resolution No.</x-modal.modal-label>
                    <x-form.input type="text" name="bor_approval_no" placeholder="e.g. BOR Approval Resolution No. 123" required />
                </div>
                <div>
                    <x-modal.modal-label isRequired>BOR Approval Date</x-modal.modal-label>
                    <x-form.input type="date" name="bor_approval_date" required />
                </div>
            </div>
        </x-modal.body>

        <x-modal.footer>
            <x-modal.close-button modalId="addProgramModal" text="Cancel" />
            <x-ui.button type="submit" variant="add-button">
                <i class="bx bx-save"></i> Create Program
            </x-ui.button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
