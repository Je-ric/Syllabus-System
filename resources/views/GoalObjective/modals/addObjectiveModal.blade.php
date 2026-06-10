<x-modal.dialog id="addObjectiveModal" maxWidth="max-w-md" width="w-11/12">
    <x-modal.header modalId="addObjectiveModal">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-[#f0fdf4] text-[#16a34a] shrink-0">
                <i class="bx bx-plus-circle text-base leading-none"></i>
            </span>
            <span class="text-[15px] font-bold text-[#0f172a]">Add New Objective</span>
        </div>
    </x-modal.header>

    <form action="{{ route('objective.store') }}" method="POST" class="flex flex-col">
        @csrf
        <input type="hidden" name="college_id" value="{{ $selectedCollegeId }}">
        <input type="hidden" name="department_id" value="{{ $selectedDepartmentId }}">
        <x-modal.body>
            <div>
                <x-form.label for="add_objective_text" isRequired>Objective Description</x-form.label>
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
