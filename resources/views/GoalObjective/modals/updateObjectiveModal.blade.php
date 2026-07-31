<x-modal.dialog id="updateObjectiveModal_{{ $objective->id }}" maxWidth="max-w-lg" width="w-11/12" variant="edit">
    <x-modal.header modalId="updateObjectiveModal_{{ $objective->id }}" variant="edit">
        Edit Objective
    </x-modal.header>

    <form action="{{ route('objective.update', $objective->id) }}" method="POST" class="flex flex-col"
        x-data="{ submitting: false }"
        x-on:submit="submitting = true">
        @csrf
        @method('PUT')
        <x-modal.body>
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">Editing</p>
                    <span class="font-mono text-[11px] font-bold text-[#166534] bg-[#f0fdf4] border border-[#bbf7d0] px-2 py-0.5 rounded-md">
                        {{ $objective->dept_obj_code }}
                    </span>
                </div>
                <div>
                    <x-modal.modal-label for="objective_text_{{ $objective->id }}" isRequired>Objective Description</x-modal.modal-label>
                    <x-form.textarea
                        id="objective_text_{{ $objective->id }}"
                        name="objective_text"
                        rows="5"
                        placeholder="Describe the department objective…"
                        ::readonly="submitting"
                        ::class="submitting ? 'opacity-60 cursor-wait' : ''"
                        required>{{ $objective->objective_text }}</x-form.textarea>
                </div>
            </div>
        </x-modal.body>

        <x-modal.footer>
            <x-modal.close-button :modalId="'updateObjectiveModal_' . $objective->id" text="Cancel" ::disabled="submitting" />
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
