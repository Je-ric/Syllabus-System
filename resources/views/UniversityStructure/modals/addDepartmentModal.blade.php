<x-modal.dialog id="addDepartmentModal" maxWidth="max-w-md" width="w-11/12" variant="add">
    <x-modal.header modalId="addDepartmentModal" variant="add">
        <div>
            <p class="text-[15px] font-bold text-[#0f172a]">Add New Department</p>
            <p class="text-[13px] text-[#94a3b8]">Will be created under the selected college.</p>
        </div>
    </x-modal.header>

    <form method="POST" action="{{ route('university.structure.department.store') }}" class="flex flex-col"
        x-data="{ submitting: false }"
        x-on:submit="submitting = true">
        @csrf
        <input type="hidden" name="college_id" id="addDepartment_college_id" value="">
        <x-modal.body>
            <div>
                <x-modal.modal-label isRequired>Department Name</x-modal.modal-label>
                <x-form.input
                    type="text"
                    name="name"
                    placeholder="e.g. Department of Computer Science"
                    ::readonly="submitting"
                    ::class="submitting ? 'opacity-60 cursor-wait' : ''"
                    required />
            </div>
        </x-modal.body>

        <x-modal.footer>
            <x-modal.close-button modalId="addDepartmentModal" text="Cancel" ::disabled="submitting" />
            <x-ui.button type="submit" variant="add-button"
                submitting="submitting" loadingText="Creating…"
                ::disabled="submitting">
                <i class="bx bx-save"></i> Create Department
            </x-ui.button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
