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
                    ::class="submitting ? 'opacity-60 cursor-wait' : ''"
                    required>{{ old('objective_text') }}</x-form.textarea>
            </div>
        </x-modal.body>
        <x-modal.footer>
            <x-modal.close-button modalId="addObjectiveModal" text="Cancel" ::disabled="submitting" />
            <x-ui.button type="submit" variant="add-button" ::disabled="submitting">
                <template x-if="!submitting">
                    <span class="inline-flex items-center gap-1.5"><i class="bx bx-plus"></i> Add Objective</span>
                </template>
                <template x-if="submitting">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="animate-spin h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Adding…
                    </span>
                </template>
            </x-ui.button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
