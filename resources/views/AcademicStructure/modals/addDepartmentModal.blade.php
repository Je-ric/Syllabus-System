<x-modal.dialog id="addDepartmentModal" maxWidth="xl:max-w-xl lg:max-w-lg md:max-w-md sm:max-w-sm max-w-xs" width="w-full" maxHeight="max-h-[90vh]">
    <x-modal.header>
        <div>
            <h3 class="text-lg sm:text-xl font-bold text-slate-800">Add New Department</h3>
            <p class="text-gray-500 text-sm mt-1">This department will be created under the selected college.</p>
        </div>
    </x-modal.header>

    <form method="POST" action="{{ route('department.store') }}" class="flex flex-col">
        @csrf
        <input type="hidden" name="college_id" id="addDepartment_college_id" value="">

        <x-modal.body>
            <div class="space-y-4">
                <div>
                    <x-form.label>Department Name</x-form.label>
                    <x-form.input type="text" name="name" placeholder="e.g. Department of Computer Science" required />
                </div>
            </div>
        </x-modal.body>

        <x-modal.footer>
            <div class="flex gap-2 w-full justify-end flex-col sm:flex-row">
                <x-modal.close-button :modalId="'addDepartmentModal'" text="Cancel" variant="cancel" />
                <x-button type="submit" variant="save" class="w-full sm:w-auto">
                    <i class="bx bx-save"></i> Create Department
                </x-button>
            </div>
        </x-modal.footer>
    </form>
</x-modal.dialog>
