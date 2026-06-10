<x-modal.dialog id="addDepartmentModal" maxWidth="max-w-md" width="w-11/12">
    <x-modal.header modalId="addDepartmentModal">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-[#dcfce7] text-[#16a34a] shrink-0">
                <i class="bx bx-plus text-base leading-none"></i>
            </span>
            <div>
                <p class="text-[15px] font-bold text-[#0f172a]">Add New Department</p>
                <p class="text-[13px] text-[#94a3b8]">Will be created under the selected college.</p>
            </div>
        </div>
    </x-modal.header>

    <form method="POST" action="{{ route('department.store') }}" class="flex flex-col">
        @csrf
        <input type="hidden" name="college_id" id="addDepartment_college_id" value="">
        <x-modal.body>
            <div>
                <x-modal.modal-label isRequired>Department Name</x-modal.modal-label>
                <x-form.input type="text" name="name" placeholder="e.g. Department of Computer Science" required />
            </div>
        </x-modal.body>

        <x-modal.footer>
            <x-modal.close-button modalId="addDepartmentModal" text="Cancel" />
            <x-button type="submit" variant="save">
                <i class="bx bx-save"></i> Create Department
            </x-button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
