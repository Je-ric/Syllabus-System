@php $hasErrors = $errors->hasAny(['goal_text']) && session('_modal') === 'updateGoal_' . $goal->id; @endphp

<x-modal.dialog id="updateGoalModal_{{ $goal->id }}" maxWidth="max-w-lg" width="w-11/12" variant="edit">
    <x-modal.header modalId="updateGoalModal_{{ $goal->id }}" variant="edit">
        Edit Goal
    </x-modal.header>

    <form action="{{ route('goal.update', $goal->id) }}" method="POST" class="flex flex-col"
        x-data="{
            submitting: false,
            original: @js(trim($goal->goal_text)),
            goalText: @js(old('goal_text', trim($goal->goal_text)))
        }"
        x-on:submit="submitting = true"
        x-init="@js($hasErrors) && $nextTick(() => document.getElementById('updateGoalModal_{{ $goal->id }}')?.showModal())">
        @csrf
        @method('PUT')
        <input type="hidden" name="_modal" value="updateGoal_{{ $goal->id }}">
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
                        x-model="goalText"
                        ::readonly="submitting"
                        ::class="submitting ? 'opacity-60 cursor-not-allowed' : ''"
                        required>{{ old('goal_text', $goal->goal_text) }}</x-form.textarea>
                    @if ($hasErrors)
                        @error('goal_text')
                            <p class="text-xs text-[#E52F28] flex items-center gap-1 mt-1">
                                <i class="bx bx-error-circle"></i> {{ $message }}
                            </p>
                        @enderror
                    @endif
                </div>
            </div>
        </x-modal.body>

        <x-modal.footer>
            <x-modal.close-button :modalId="'updateGoalModal_' . $goal->id" text="Cancel" ::disabled="submitting" />
            <x-ui.button type="submit" variant="save"
                submitting="submitting" loadingText="Saving…"
                ::disabled="submitting || !goalText.trim() || goalText.trim() === original">
                <i class="bx bx-save"></i> Save Changes
            </x-ui.button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
