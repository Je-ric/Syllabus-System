<x-modal.dialog id="addProgramModal" maxWidth="xl:max-w-xl lg:max-w-lg md:max-w-md sm:max-w-sm max-w-xs" width="w-full" maxHeight="max-h-[90vh]">
    <x-modal.header>
        <div>
            <h3 class="text-lg sm:text-xl font-bold text-slate-800">Add New Program</h3>
            <p class="text-gray-500 text-sm mt-1">This program will be created under the selected department.</p>
        </div>
    </x-modal.header>

    <form method="POST" action="{{ route('program.store') }}" class="flex flex-col">
        @csrf
        <input type="hidden" name="department_id" id="addProgram_department_id" value="">

        <x-modal.body>
            <div class="space-y-4">
                <div>
                    <x-form.label>Program Name</x-form.label>
                    <x-form.input type="text" name="name" placeholder="e.g. Bachelor of Science in Computer Science" required />
                </div>
                <div>
                    <x-form.label>BOR Approval No.</x-form.label>
                    <x-form.input type="text" name="bor_approval_no" placeholder="e.g. BOR Resolution No. 123" required />
                </div>
                <div>
                    <x-form.label>BOR Approval Date</x-form.label>
                    <x-form.input type="date" name="bor_approval_date" required />
                </div>
            </div>
        </x-modal.body>

        <x-modal.footer>
            <div class="flex gap-2 w-full justify-end flex-col sm:flex-row">
                <x-modal.close-button :modalId="'addProgramModal'" text="Cancel" variant="cancel" />
                <x-button type="submit" variant="save" class="w-full sm:w-auto">
                    <i class="bx bx-save"></i> Create Program
                </x-button>
            </div>
        </x-modal.footer>
    </form>
</x-modal.dialog>
