<x-modal.dialog id="updateGoalModal_{{ $goal->id }}" maxWidth="max-w-lg" width="w-11/12">
    <x-modal.header modalId="updateGoalModal_{{ $goal->id }}">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-[#eff6ff] text-[#1d4ed8] shrink-0">
                <i class="bx bx-edit text-base leading-none"></i>
            </span>
            <div class="flex items-center gap-2 min-w-0">
                <span class="text-[15px] font-bold text-[#0f172a]">Edit Goal</span>
                <span class="font-mono text-[11px] font-bold text-[#166534] bg-[#f0fdf4] border border-[#bbf7d0] px-2 py-0.5 rounded-md shrink-0">
                    {{ $goal->college_goals_code }}
                </span>
            </div>
        </div>
    </x-modal.header>

    <form action="{{ route('goal.update', $goal->id) }}" method="POST" class="flex flex-col">
        @csrf
        @method('PUT')
        <x-modal.body>
            <div>
                <x-modal.modal-label for="goal_text_{{ $goal->id }}" isRequired>Goal Description</x-modal.modal-label>
                <x-form.textarea
                    id="goal_text_{{ $goal->id }}"
                    name="goal_text"
                    rows="5"
                    placeholder="Describe the college goal…"
                    required>{{ $goal->goal_text }}</x-form.textarea>
            </div>
        </x-modal.body>

        <x-modal.footer>
            <x-modal.close-button :modalId="'updateGoalModal_' . $goal->id" text="Cancel" />
            <x-button type="submit" variant="save">
                <i class="bx bx-save"></i> Save Changes
            </x-button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
