<x-modal.dialog id="addGoalModal" maxWidth="max-w-md" width="w-11/12">
    <x-modal.header modalId="addGoalModal">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-[#f0fdf4] text-[#16a34a] shrink-0">
                <i class="bx bx-plus-circle text-base leading-none"></i>
            </span>
            <span class="text-[15px] font-bold text-[#0f172a]">Add New Goal</span>
        </div>
    </x-modal.header>

    <form action="{{ route('goal.store') }}" method="POST" class="flex flex-col">
        @csrf
        <input type="hidden" name="college_id" value="{{ $selectedCollegeId }}">
        <x-modal.body>
            <div>
                <x-form.label for="add_goal_text" isRequired>Goal Description</x-form.label>
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
            <x-button type="submit" variant="add-button">
                <i class="bx bx-plus"></i> Add Goal
            </x-button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
