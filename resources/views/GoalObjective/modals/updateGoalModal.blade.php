<x-modal.dialog id="updateGoalModal_{{ $goal->id }}" maxWidth="max-w-lg" width="w-11/12">

    <x-modal.header modalId="updateGoalModal_{{ $goal->id }}"
        class="bg-blue-50">
        <h2 class="text-xl font-semibold text-blue-900 tracking-tight">Edit Goal</h2>
    </x-modal.header>

    <form action="{{ route('goal.update', $goal->id) }}" method="POST">
        @csrf
        @method('PUT')

        <x-modal.body>
            <div class="space-y-4">

                {{-- Code (read-only) --}}
                <div>
                    <x-form.label>Goal Code — auto-generated </x-form.label>
                    <x-form.input
                        type="text"
                        value="{{ $goal->college_goals_code }}"
                        disabled />
                </div>

                {{-- Description — slot content sets the existing value --}}
                <div>
                    <x-form.label>Goal Description</x-form.label>
                    <x-form.textarea
                        name="goal_text"
                        rows="6"
                        placeholder="Describe the college goal…"
                        required>{{ $goal->goal_text }}</x-form.textarea>
                </div>

            </div>
        </x-modal.body>

        <x-modal.footer class="bg-blue-50">
            <x-modal.close-button
                :modalId="'updateGoalModal_' . $goal->id"
                text="Cancel"
                variant="cancel" />
            <x-button type="submit" variant="save">
                <i class="bx bx-save"></i> Save Changes
            </x-button>
        </x-modal.footer>
    </form>

</x-modal.dialog>
