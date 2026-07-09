<x-modal.dialog id="addGoalModal" maxWidth="max-w-xl" width="w-11/12">
    <x-modal.header modalId="addGoalModal" variant="add">
        Add New Goal
    </x-modal.header>

    <form action="{{ route('goal.store') }}" method="POST" class="flex flex-col">
        @csrf
        <input type="hidden" name="college_id" value="{{ $selectedCollegeId }}">
        <x-modal.body>
            <div>
                <x-modal.modal-label for="add_goal_text" isRequired>Goal Description</x-modal.modal-label>
                <x-form.textarea
                    id="add_goal_text"
                    name="goal_text"
                    rows="5"
                    placeholder="Describe the college goal…"
                    required>{{ old('goal_text') }}</x-form.textarea>
            </div>
        </x-modal.body>
        <x-modal.footer>
            <x-modal.close-button modalId="addGoalModal" text="Cancel" />
            <x-ui.button type="submit" variant="add-button">
                <i class="bx bx-plus"></i> Add Goal
            </x-ui.button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
