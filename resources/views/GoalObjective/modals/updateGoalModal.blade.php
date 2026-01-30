<x-modal.dialog id="updateGoalModal_{{ $goal->id }}" maxWidth="max-w-lg" width="w-11/12">
    <x-modal.header>
        Edit Goal
        <x-modal.x-button :modalId="'updateGoalModal_' . $goal->id" />
    </x-modal.header>

    <form action="{{ route('goal.update', $goal->id) }}" method="POST">
        @csrf
        @method('PUT')

        <x-modal.body>
            <div class="space-y-4">
                <div>
                    <label class="block font-medium text-sm text-gray-700 mb-2">Goal Code (Auto-generated)</label>
                    <input
                        type="text"
                        value="{{ $goal->college_goals_code }}"
                        class="w-full border rounded px-3 py-2 bg-gray-100 text-gray-500"
                        disabled>
                </div>

                <div>
                    <label class="block font-medium text-sm text-gray-700 mb-2">Goal Description</label>
                    <textarea
                        name="goal_text"
                        rows="6"
                        placeholder="Goal description"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500"
                        required>{{ $goal->goal_text }}</textarea>
                </div>
            </div>
        </x-modal.body>

        <x-modal.footer>
            <x-modal.close-button :modalId="'updateGoalModal_' . $goal->id" text="Cancel" variant="cancel" />

            <x-button type="submit" variant="save">
                <i class="bx bx-save"></i>
                Save Changes
            </x-button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
