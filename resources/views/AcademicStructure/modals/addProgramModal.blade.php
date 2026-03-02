<x-modal.dialog id="addProgramModal" maxWidth="max-w-xl" width="w-11/12">
    <x-modal.header class="bg-emerald-50">
        <h2 class="text-xl font-semibold text-emerald-900 tracking-tight">Add New Program</h2>
        <x-modal.x-button :modalId="'addProgramModal'" />
    </x-modal.header>

    <form method="POST" action="{{ route('program.store') }}">
        @csrf

        <input type="hidden" name="department_id" id="addProgram_department_id" value="">

        <x-modal.body>
            <div class="space-y-3">
                <div>
                    <x-form.label>Program Name</x-form.label>
                    <x-form.input type="text"
                            name="name"
                            placeholder="Program Name"
                            required>
                    </x-form.input>
                </div>

                <div>
                    <x-form.label>BOR Approval No</x-form.label>
                    <x-form.input type="text"
                            name="bor_approval_no"
                            placeholder="BOR Resolution No."
                            required>
                    </x-form.input>
                </div>

                <div>
                    <x-form.label>BOR Approval Date</x-form.label>
                    <x-form.input type="date"
                            name="bor_approval_date"
                            required>
                    </x-form.input>
                </div>

                <p class="text-gray-500 text-sm">This program will be created under the selected department.</p>
            </div>
        </x-modal.body>

        <x-modal.footer class="bg-emerald-50">
            <x-modal.close-button :modalId="'addProgramModal'" text="Cancel" variant="close" />
            <x-button type="submit" variant="save">
                <i class="bx bx-save"></i>
                Create Program
            </x-button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
