@php $hasErrors = $errors->hasAny(['goal_text']) && session('_modal') === 'addGoal'; @endphp

<x-modal.dialog id="addGoalModal" maxWidth="max-w-xl" width="w-11/12" variant="add">
    <x-modal.header modalId="addGoalModal" variant="add">
        Add New Goal
    </x-modal.header>

    <form action="{{ route('goal.store') }}" method="POST" class="flex flex-col"
        x-data="{ submitting: false, goalText: @js(old('goal_text', '')) }"
        x-on:submit="submitting = true"
        x-init="@js($hasErrors) && $nextTick(() => document.getElementById('addGoalModal')?.showModal())">
        @csrf
        <input type="hidden" name="college_id" value="{{ $selectedCollegeId }}">
        <input type="hidden" name="_modal" value="addGoal">
        <x-modal.body>
            <div class="space-y-3">
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

                <div>
                    <x-modal.modal-label for="add_goal_text" isRequired>Goal Description</x-modal.modal-label>
                    <x-form.textarea
                        id="add_goal_text"
                        name="goal_text"
                        rows="5"
                        placeholder="Describe the college goal…"
                        x-model="goalText"
                        ::readonly="submitting"
                        ::class="submitting ? 'opacity-60 cursor-not-allowed' : ''"
                        required>{{ old('goal_text') }}</x-form.textarea>
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
            <x-modal.close-button modalId="addGoalModal" text="Cancel" ::disabled="submitting" />
            <x-ui.button type="submit" variant="add-button"
                submitting="submitting" loadingText="Adding…"
                ::disabled="submitting || !goalText.trim()">
                <i class="bx bx-plus"></i> Add Goal
            </x-ui.button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
