@php $hasErrors = $errors->hasAny(['name']) && session('_modal') === 'updateCollege_' . $college->id; @endphp

<x-modal.dialog id="updateCollegeModal_{{ $college->id }}" maxWidth="max-w-md" width="w-11/12" variant="edit">
    <x-modal.header modalId="updateCollegeModal_{{ $college->id }}" variant="edit">
        <div>
            <p class="text-[15px] font-bold text-[#0f172a]">Edit College</p>
            <p class="text-[13px] text-[#94a3b8] truncate">{{ $college->name }}</p>
        </div>
    </x-modal.header>

    <form method="POST" action="{{ route('university.structure.college.update', $college) }}" class="flex flex-col"
        x-data="{
            submitting: false,
            original: @js($college->name),
            name: @js($college->name)
        }"
        x-on:submit="submitting = true"
        x-init="
            const modal = document.getElementById('updateCollegeModal_{{ $college->id }}');
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
        <input type="hidden" name="_modal" value="updateCollege_{{ $college->id }}">
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
                    <x-modal.modal-label for="editCollegeName_{{ $college->id }}" isRequired>College Name</x-modal.modal-label>
                    <x-form.input
                        id="editCollegeName_{{ $college->id }}"
                        type="text"
                        name="name"
                        value="{{ $college->name }}"
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
            <x-modal.close-button :modalId="'updateCollegeModal_' . $college->id" text="Cancel" ::disabled="submitting" />
            <x-ui.button type="submit" variant="save"
                submitting="submitting" loadingText="Saving…"
                ::disabled="submitting || !name.trim() || name.trim() === original">
                <i class="bx bx-save"></i> Save Changes
            </x-ui.button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
