<x-modal.dialog id="updateGoalModal_{{ $goal->id }}" maxWidth="max-w-lg" width="w-11/12" variant="edit">
    <x-modal.header modalId="updateGoalModal_{{ $goal->id }}" variant="edit">
        Edit Goal
    </x-modal.header>

    <form action="{{ route('goal.update', $goal->id) }}" method="POST" class="flex flex-col"
        x-data="{ submitting: false }"
        x-on:submit="submitting = true">
        @csrf
        @method('PUT')
        <x-modal.body>
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">Editing</p>
                    <span class="font-mono text-[11px] font-bold text-[#166534] bg-[#f0fdf4] border border-[#bbf7d0] px-2 py-0.5 rounded-md">
                        {{ $goal->college_goals_code }}
                    </span>
                </div>
                <div>
                    <x-modal.modal-label for="goal_text_{{ $goal->id }}" isRequired>Goal Description</x-modal.modal-label>
                    <x-form.textarea
                        id="goal_text_{{ $goal->id }}"
                        name="goal_text"
                        rows="5"
                        placeholder="Describe the college goal…"
                        ::readonly="submitting"
                        ::class="submitting ? 'opacity-60 cursor-not-allowed' : ''"
                        required>{{ $goal->goal_text }}</x-form.textarea>
                </div>
            </div>
        </x-modal.body>

        <x-modal.footer>
            <x-modal.close-button :modalId="'updateGoalModal_' . $goal->id" text="Cancel" ::disabled="submitting" />
            <x-ui.button type="submit" variant="save"
                submitting="submitting" loadingText="Saving…"
                ::disabled="submitting">
                <i class="bx bx-save"></i> Save Changes
            </x-ui.button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
