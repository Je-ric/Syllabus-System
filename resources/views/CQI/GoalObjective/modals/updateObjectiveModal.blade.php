<x-modal.dialog id="updateObjectiveModal_{{ $objective->id }}" maxWidth="max-w-lg" width="w-11/12" variant="edit">
    <x-modal.header modalId="updateObjectiveModal_{{ $objective->id }}" variant="edit">
        Edit Objective
    </x-modal.header>

    <form action="{{ route('objective.update', $objective->id) }}" method="POST" class="flex flex-col"
        x-data="{
            submitting: false,
            original: @js(trim($objective->objective_text)),
            objectiveText: @js(trim($objective->objective_text))
        }"
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
                        x-model="objectiveText"
                        ::readonly="submitting"
                        ::class="submitting ? 'opacity-60 cursor-not-allowed' : ''"
                        required></x-form.textarea>
                </div>
            </div>
        </x-modal.body>

        <x-modal.footer>
            <x-modal.close-button :modalId="'updateObjectiveModal_' . $objective->id" text="Cancel" ::disabled="submitting" />
            <x-ui.button type="submit" variant="save"
                submitting="submitting" loadingText="Saving…"
                ::disabled="submitting || !objectiveText.trim() || objectiveText.trim() === original">
                <i class="bx bx-save"></i> Save Changes
            </x-ui.button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
