<x-modal.dialog id="updateGoalModal_{{ $goal->id }}" maxWidth="max-w-lg" width="w-11/12">

    <x-modal.header modalId="updateGoalModal_{{ $goal->id }}" class="bg-blue-50">
        <div class="flex items-center gap-2.5">
            <i class="bx bx-edit text-blue-600 text-lg leading-none"></i>
            <h2 class="text-base font-semibold text-blue-900 tracking-tight">Edit Goal</h2>
            <span class="font-mono text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/70 px-2 py-0.5 rounded-md">
                {{ $goal->college_goals_code }}
            </span>
        </div>
    </x-modal.header>

    <form action="{{ route('goal.update', $goal->id) }}" method="POST">
        @csrf
        @method('PUT')

        <x-modal.body>
            <div class="space-y-3">
                <x-form.label for="goal_text_{{ $goal->id }}">Goal Description</x-form.label>
                <x-form.textarea
                    id="goal_text_{{ $goal->id }}"
                    name="goal_text"
                    rows="6"
                    placeholder="Describe the college goal…"
                    required>{{ $goal->goal_text }}</x-form.textarea>
            </div>
        </x-modal.body>

        <x-modal.footer class="bg-blue-50">
            <x-modal.close-button :modalId="'updateGoalModal_' . $goal->id" text="Cancel" variant="cancel" />
            <x-button type="submit" variant="save">
                <i class="bx bx-save"></i> Save Changes
            </x-button>
        </x-modal.footer>
    </form>

</x-modal.dialog>
