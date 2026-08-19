@php $hasErrors = $errors->hasAny(['objective_text']) && session('_modal') === 'addObjective'; @endphp

<x-modal.dialog id="addObjectiveModal" maxWidth="max-w-lg" width="w-11/12" variant="add">
    <x-modal.header modalId="addObjectiveModal" variant="add">
        Add New Objective
    </x-modal.header>

    <form action="{{ route('objective.store') }}" method="POST" class="flex flex-col"
        x-data="{ submitting: false, objectiveText: '' }"
        x-on:submit="submitting = true"
        x-init="
            const modal = document.getElementById('addObjectiveModal');
            if (modal) {
                modal.addEventListener('open', () => {
                    this.objectiveText = '';
                });
            }
            @if($hasErrors)
                $nextTick(() => { 
                    if (modal) {
                        this.objectiveText = @js(old('objective_text'));
                        modal.showModal();
                    }
                });
            @endif
        ">
        @csrf
        <input type="hidden" name="college_id" value="{{ $selectedCollegeId }}">
        <input type="hidden" name="department_id" value="{{ $selectedDepartmentId }}">
        <input type="hidden" name="_modal" value="addObjective">
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
                    <x-modal.modal-label for="add_objective_text" isRequired>Objective Description</x-modal.modal-label>
                    <x-form.textarea
                        id="add_objective_text"
                        name="objective_text"
                        rows="5"
                        placeholder="Describe the department objective…"
                        x-model="objectiveText"
                        ::readonly="submitting"
                        ::class="submitting ? 'opacity-60 cursor-not-allowed' : ''"
                        required></x-form.textarea>
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
            <x-modal.close-button modalId="addObjectiveModal" text="Cancel" ::disabled="submitting" />
            <x-ui.button type="submit" variant="add-button"
                submitting="submitting" loadingText="Adding…"
                ::disabled="submitting || !objectiveText.trim()">
                <i class="bx bx-plus"></i> Add Objective
            </x-ui.button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
