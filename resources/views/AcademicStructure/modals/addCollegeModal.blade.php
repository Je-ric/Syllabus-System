<x-modal.dialog id="addCollegeModal" maxWidth="max-w-xl" width="w-11/12">
    <x-modal.header>
        Add New College
        <x-modal.x-button :modalId="'addCollegeModal'" />
    </x-modal.header>

    <form method="POST" action="{{ route('college.store') }}">
        @csrf

        <x-modal.body>
            <div class="space-y-3">
                <div>
                    <label class="block font-medium text-sm text-gray-700">College Name</label>
                    <input
                        type="text"
                        name="name"
                        placeholder="College Name"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-green-500"
                        required>
                </div>
                <p class="text-gray-500 text-sm">Provide the official name of the college.</p>
            </div>
        </x-modal.body>

        <x-modal.footer>
            <x-modal.close-button :modalId="'addCollegeModal'" text="Cancel" variant="close"/>
            <x-button type="submit" variant="save">
                <i class="bx bx-save"></i>
                Create College
            </x-button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
