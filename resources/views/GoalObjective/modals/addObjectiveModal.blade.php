<x-modal.dialog id="addObjectiveModal" maxWidth="max-w-lg" width="w-11/12" variant="add">
    <x-modal.header modalId="addObjectiveModal" variant="add">
        Add New Objective
    </x-modal.header>

    <form action="{{ route('objective.store') }}" method="POST" class="flex flex-col"
        x-data="{ submitting: false }"
        x-on:submit="submitting = true">
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
                    ::readonly="submitting"
                    ::class="submitting ? 'opacity-60 cursor-not-allowed' : ''"
                    required>{{ old('objective_text') }}</x-form.textarea>
            </div>
        </x-modal.body>
        <x-modal.footer>
            <x-modal.close-button modalId="addObjectiveModal" text="Cancel" ::disabled="submitting" />
            <x-ui.button type="submit" variant="add-button"
                submitting="submitting" loadingText="Adding…"
                ::disabled="submitting">
                <i class="bx bx-plus"></i> Add Objective
            </x-ui.button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
