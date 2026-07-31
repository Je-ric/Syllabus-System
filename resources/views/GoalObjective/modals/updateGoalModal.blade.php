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
                        ::class="submitting ? 'opacity-60 cursor-wait' : ''"
                        required>{{ $goal->goal_text }}</x-form.textarea>
                </div>
            </div>
        </x-modal.body>

        <x-modal.footer>
            <x-modal.close-button :modalId="'updateGoalModal_' . $goal->id" text="Cancel" ::disabled="submitting" />
            <x-ui.button type="submit" variant="save" ::disabled="submitting">
                <template x-if="!submitting">
                    <span class="inline-flex items-center gap-1.5"><i class="bx bx-save"></i> Save Changes</span>
                </template>
                <template x-if="submitting">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="animate-spin h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Saving…
                    </span>
                </template>
            </x-ui.button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
