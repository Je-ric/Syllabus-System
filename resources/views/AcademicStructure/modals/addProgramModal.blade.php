<x-modal.dialog id="addProgramModal" maxWidth="max-w-md" width="w-11/12">
    <x-modal.header modalId="addProgramModal">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-[#dcfce7] text-[#16a34a] shrink-0">
                <i class="bx bx-plus text-base leading-none"></i>
            </span>
            <div>
                <p class="text-[15px] font-bold text-[#0f172a]">Add New Program</p>
                <p class="text-[13px] text-[#94a3b8]">Will be created under the selected department.</p>
            </div>
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
                    <x-modal.modal-label isRequired>BOR Approval No.</x-modal.modal-label>
                    <x-form.input type="text" name="bor_approval_no" placeholder="e.g. BOR Resolution No. 123" required />
                </div>
                <div>
                    <x-modal.modal-label isRequired>BOR Approval Date</x-modal.modal-label>
                    <x-form.input type="date" name="bor_approval_date" required />
                </div>
            </div>
        </x-modal.body>

        <x-modal.footer>
            <x-modal.close-button modalId="addProgramModal" text="Cancel" />
            <x-button type="submit" variant="save">
                <i class="bx bx-save"></i> Create Program
            </x-button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
