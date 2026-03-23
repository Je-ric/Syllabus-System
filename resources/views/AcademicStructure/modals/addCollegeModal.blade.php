<x-modal.dialog id="addCollegeModal" maxWidth="xl:max-w-xl lg:max-w-lg md:max-w-md sm:max-w-sm max-w-xs" width="w-full" maxHeight="max-h-[90vh]">
    <x-modal.header>
        <div>
            <h3 class="text-lg sm:text-xl font-bold text-slate-800">Add New College</h3>
            <p class="text-gray-500 text-sm mt-1">Provide the official name of the college.</p>
        </div>
    </x-modal.header>

    <form method="POST" action="{{ route('college.store') }}" class="flex flex-col">
        @csrf
        <x-modal.body>
            <div class="space-y-4">
                <div>
                    <x-form.label>College Name</x-form.label>
                    <x-form.input type="text" name="name" placeholder="e.g. College of Engineering" required />
                </div>
            </div>
        </x-modal.body>

        <x-modal.footer>
            <div class="flex gap-2 w-full justify-end flex-col sm:flex-row">
                <x-modal.close-button :modalId="'addCollegeModal'" text="Cancel" variant="cancel" />
                <x-button type="submit" variant="save" class="w-full sm:w-auto">
                    <i class="bx bx-save"></i> Create College
                </x-button>
            </div>
        </x-modal.footer>
    </form>
</x-modal.dialog>
