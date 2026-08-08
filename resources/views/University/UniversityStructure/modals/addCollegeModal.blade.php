<x-modal.dialog id="addCollegeModal" maxWidth="max-w-md" width="w-11/12" variant="add">
    <x-modal.header modalId="addCollegeModal" variant="add">
        <div>
            <p class="text-[15px] font-bold text-[#0f172a]">Add New College</p>
            <p class="text-[13px] text-[#94a3b8]">Provide the official name of the college.</p>
        </div>
    </x-modal.header>

    <form method="POST" action="{{ route('university.structure.college.store') }}" class="flex flex-col"
        x-data="{ submitting: false, name: '' }"
        x-on:submit="submitting = true">
        @csrf
        <x-modal.body>
            <div>
                <x-modal.modal-label isRequired>College Name</x-modal.modal-label>
                <x-form.input
                    type="text"
                    name="name"
                    placeholder="e.g. College of Engineering"
                    x-model="name"
                    ::readonly="submitting"
                    ::class="submitting ? 'opacity-60 cursor-not-allowed' : ''"
                    required />
            </div>
        </x-modal.body>

        <x-modal.footer>
            <x-modal.close-button modalId="addCollegeModal" text="Cancel" ::disabled="submitting" />
            <x-ui.button type="submit" variant="add-button"
                submitting="submitting" loadingText="Creating…"
                ::disabled="submitting || !name.trim()">
                <i class="bx bx-save"></i> Create College
            </x-ui.button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
