<x-modal.dialog id="addObjectiveModal" maxWidth="max-w-lg" width="w-11/12">
    <x-modal.header modalId="addObjectiveModal" variant="add">
        Add New Objective
    </x-modal.header>

    <form action="{{ route('objective.store') }}" method="POST" class="flex flex-col">
        @csrf
        <input type="hidden" name="college_id" value="{{ $selectedCollegeId }}">
        <input type="hidden" name="department_id" value="{{ $selectedDepartmentId }}">
        <x-modal.body>
            <div>
                <x-modal.modal-label for="add_objective_text" isRequired>Objective Description</x-modal.modal-label>
                <x-form.textarea
                    id="add_objective_text"
                    name="objective_text"
                    rows="5"
                    placeholder="Describe the department objective…"
                    required>{{ old('objective_text') }}</x-form.textarea>
            </div>
        </x-modal.body>
        <x-modal.footer>
            <x-modal.close-button modalId="addObjectiveModal" text="Cancel" />
            <x-button type="submit" variant="add-button">
                <i class="bx bx-plus"></i> Add Objective
            </x-button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
