@php $hasErrors = $errors->hasAny(['name']) && session('_modal') === 'addDepartment'; @endphp

<x-modal.dialog id="addDepartmentModal" maxWidth="max-w-md" width="w-11/12" variant="add">
    <x-modal.header modalId="addDepartmentModal" variant="add">
        <div>
            <p class="text-[15px] font-bold text-[#0f172a]">Add New Department</p>
            <p class="text-[13px] text-[#94a3b8]">Will be created under the selected college.</p>
        </div>
    </x-modal.header>

    <form method="POST" action="{{ route('university.structure.department.store') }}" class="flex flex-col"
        x-data="{ submitting: false, name: @js(old('name', '')) }"
        x-on:submit="submitting = true"
        x-init="@js($hasErrors) && $nextTick(() => document.getElementById('addDepartmentModal')?.showModal())">
        @csrf
        <input type="hidden" name="_modal" value="addDepartment">
        <input type="hidden" name="college_id" id="addDepartment_college_id" value="{{ old('college_id', '') }}">
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
                    <x-modal.modal-label isRequired>Department Name</x-modal.modal-label>
                    <x-form.input
                        type="text"
                        name="name"
                        value="{{ old('name', '') }}"
                        placeholder="e.g. Department of Computer Science"
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
            <x-modal.close-button modalId="addDepartmentModal" text="Cancel" ::disabled="submitting" />
            <x-ui.button type="submit" variant="add-button"
                submitting="submitting" loadingText="Creating…"
                ::disabled="submitting || !name.trim()">
                <i class="bx bx-save"></i> Create Department
            </x-ui.button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
