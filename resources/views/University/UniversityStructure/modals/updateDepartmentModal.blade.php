@php $hasErrors = $errors->hasAny(['name']) && session('_modal') === 'updateDepartment_' . $dept->id; @endphp

<x-modal.dialog id="updateDepartmentModal_{{ $dept->id }}" maxWidth="max-w-md" width="w-11/12" variant="edit">
    <x-modal.header modalId="updateDepartmentModal_{{ $dept->id }}" variant="edit">
        <div>
            <p class="text-[15px] font-bold text-[#0f172a]">Edit Department</p>
            <p class="text-[13px] text-[#94a3b8] truncate">{{ $dept->name }}</p>
        </div>
    </x-modal.header>

    <form method="POST" action="{{ route('university.structure.department.update', $dept) }}" class="flex flex-col"
        x-data="{
            submitting: false,
            original: @js($dept->name),
            name: @js($dept->name)
        }"
        x-on:submit="submitting = true"
        x-init="
            const modal = document.getElementById('updateDepartmentModal_{{ $dept->id }}');
            if (modal) {
                modal.addEventListener('open', () => {
                    this.name = this.original;
                });
            }
            @if($hasErrors)
                $nextTick(() => { 
                    if (modal) {
                        this.name = @js(old('name'));
                        modal.showModal();
                    }
                });
            @endif
        ">
        @csrf
        @method('PUT')
        <input type="hidden" name="college_id" value="{{ $dept->college_id }}">
        <input type="hidden" name="_modal" value="updateDepartment_{{ $dept->id }}">
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
                    <x-modal.modal-label for="editDeptName_{{ $dept->id }}" isRequired>Department Name</x-modal.modal-label>
                    <x-form.input
                        id="editDeptName_{{ $dept->id }}"
                        type="text"
                        name="name"
                        value="{{ $dept->name }}"
                        x-model="name"
                        ::readonly="submitting"
                        ::class="submitting ? 'opacity-60 cursor-not-allowed' : ''"
                        required />
                    @if ($hasErrors)
                        @error('name')
                            <p class="text-xs text-[#E52F28] flex items-center gap-1 mt-1">
                                <i class="bx bx-error-circle"></i> {{ $message }}
                            </p>
                        @enderror
                    @endif
                </div>
            </div>
        </x-modal.body>
        <x-modal.footer>
            <x-modal.close-button :modalId="'updateDepartmentModal_' . $dept->id" text="Cancel" ::disabled="submitting" />
            <x-ui.button type="submit" variant="save"
                submitting="submitting" loadingText="Saving…"
                ::disabled="submitting || !name.trim() || name.trim() === original">
                <i class="bx bx-save"></i> Save Changes
            </x-ui.button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
