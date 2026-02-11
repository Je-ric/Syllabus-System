<x-modal.dialog id="addDepartmentModal" maxWidth="max-w-xl" width="w-11/12">
    <x-modal.header>
        Add New Department
        <x-modal.x-button :modalId="'addDepartmentModal'" />
    </x-modal.header>

    <form method="POST" action="{{ route('department.store') }}">
        @csrf

        <input type="hidden" name="college_id" id="addDepartment_college_id" value="">

        <x-modal.body>
            <div class="space-y-3">
                <div>
                    <x-form.label>Department Name</x-form.label>
                    <x-form.input
                        type="text"
                        name="name"
                        placeholder="Department Name"
                        required>
                    </x-form.input>
                </div>
                <p class="text-gray-500 text-sm">This department will be created under the selected college.</p>
            </div>
        </x-modal.body>

        <x-modal.footer>
            <x-modal.close-button :modalId="'addDepartmentModal'" text="Cancel" variant="close"/>
            <x-button type="submit" variant="save">
                <i class="bx bx-save"></i>
                Create Department
            </x-button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
