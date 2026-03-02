<x-modal.dialog id="addCollegeModal" maxWidth="max-w-xl" width="w-11/12">
    <x-modal.header class="bg-emerald-50">
        <h2 class="text-xl font-semibold text-emerald-900 tracking-tight">Add New College</h2>
        <x-modal.x-button :modalId="'addCollegeModal'" />
    </x-modal.header>

    <form method="POST" action="{{ route('college.store') }}">
        @csrf

        <x-modal.body>
            <div class="space-y-3">
                <div>
                    <x-form.label>College Name</x-form.label>
                    <x-form.input
                        type="text"
                        name="name"
                        placeholder="College Name"
                        required>
                    </x-form.input>
                </div>
                <p class="text-gray-500 text-sm">Provide the official name of the college.</p>
            </div>
        </x-modal.body>

        <x-modal.footer class="bg-emerald-50">
            <x-modal.close-button :modalId="'addCollegeModal'" text="Cancel" variant="close"/>
            <x-button type="submit" variant="save">
                <i class="bx bx-save"></i>
                Create College
            </x-button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
