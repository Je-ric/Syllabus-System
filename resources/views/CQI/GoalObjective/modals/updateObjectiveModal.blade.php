@php $hasErrors = $errors->hasAny(['objective_text']); @endphp

<x-modal.dialog id="updateObjectiveModal_{{ $objective->id }}" maxWidth="max-w-lg" width="w-11/12" variant="edit">
    <x-modal.header modalId="updateObjectiveModal_{{ $objective->id }}" variant="edit">
        Edit Objective
    </x-modal.header>

    <form action="{{ route('objective.update', $objective->id) }}" method="POST" class="flex flex-col"
        x-data="{
            submitting: false,
            original: @js(trim($objective->objective_text)),
            objectiveText: @js(old('objective_text', trim($objective->objective_text)))
        }"
        x-on:submit="submitting = true"
        x-init="@js($hasErrors) && $nextTick(() => document.getElementById('updateObjectiveModal_{{ $objective->id }}')?.showModal())">
        @csrf
        @method('PUT')
        <x-modal.body>
            <div class="space-y-4">
                {{-- Generic / catch-block errors --}}
                @if ($errors->has('error'))
                    <x-feedback-status.alert type="error" :showTitle="false" class="mb-4">
                        <strong>Something went wrong:</strong> {{ $errors->first('error') }}
                    </x-feedback-status.alert>
                @endif

                {{-- Validation summary --}}
                @if ($hasErrors)
                    <x-feedback-status.alert type="error" :showTitle="false">
                        Please fix the highlighted fields below before submitting.
                    </x-feedback-status.alert>
                @endif

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
                        required>{{ old('objective_text', $objective->objective_text) }}</x-form.textarea>
                    @if ($hasErrors)
                        @error('objective_text')
                            <p class="text-xs text-[#E52F28] flex items-center gap-1 mt-1">
                                <i class="bx bx-error-circle"></i> {{ $message }}
                            </p>
                        @enderror
                    @endif
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
