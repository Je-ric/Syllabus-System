@php
    $editErrors = $errors->hasAny(['name']) && str_starts_with(session('_modal', ''), 'updateDepartment_');
    $editModalId = session('_modal', '');
@endphp

<x-modal.dialog id="updateDepartmentModal" maxWidth="max-w-md" width="w-11/12" variant="edit">
    <x-modal.header modalId="updateDepartmentModal" variant="edit">
        <div>
            <p class="text-[15px] font-bold text-[#0f172a]">Edit Department</p>
            <p id="updateDepartmentModal_subtitle" class="text-[13px] text-[#94a3b8] truncate"></p>
        </div>
    </x-modal.header>

    <form id="updateDepartmentModal_form" method="POST" class="flex flex-col"
        x-data="{
            submitting: false,
            original: '',
            name: ''
        }"
        x-on:submit="submitting = true">
        @csrf
        @method('PUT')
        <input type="hidden" name="college_id" id="updateDepartmentModal_collegeId" value="">
        <input type="hidden" name="_modal" id="updateDepartmentModal_modalKey" value="">
        <x-modal.body>
            <div class="space-y-3">
                @if ($editErrors)
                    <x-feedback-status.alert type="error" :showTitle="false">
                        Please fix the highlighted fields below before submitting.
                    </x-feedback-status.alert>
                @endif
                <div>
                    <x-modal.modal-label for="updateDepartmentModal_name" isRequired>Department Name</x-modal.modal-label>
                    <x-form.input
                        id="updateDepartmentModal_name"
                        type="text"
                        name="name"
                        x-model="name"
                        ::readonly="submitting"
                        ::class="submitting ? 'opacity-60 cursor-not-allowed' : ''"
                        required />
                    @if ($editErrors)
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
            <x-modal.close-button modalId="updateDepartmentModal" text="Cancel" ::disabled="submitting" />
            <x-ui.button type="submit" variant="save"
                submitting="submitting" loadingText="Saving…"
                ::disabled="submitting || !name.trim() || name.trim() === original">
                <i class="bx bx-save"></i> Save Changes
            </x-ui.button>
        </x-modal.footer>
    </form>
</x-modal.dialog>

<script>
function openEditDepartmentModal(id, name, collegeId, routeBase) {
    const modal = document.getElementById('updateDepartmentModal');
    const form  = document.getElementById('updateDepartmentModal_form');
    form.action = routeBase + '/' + id;
    document.getElementById('updateDepartmentModal_subtitle').textContent = name;
    document.getElementById('updateDepartmentModal_collegeId').value = collegeId;
    document.getElementById('updateDepartmentModal_modalKey').value = 'updateDepartment_' + id;
    const data = Alpine.$data(form);
    data.name     = name;
    data.original = name;
    data.submitting = false;
    modal.showModal();
}

@if ($editErrors)
    document.addEventListener('alpine:init', () => {
        openEditDepartmentModal(
            @js(explode('_', $editModalId)[1] ?? ''),
            @js(old('name', '')),
            @js(old('college_id', '')),
            '{{ rtrim(url('/university-structure/department'), '/') }}'
        );
    });
@endif
</script>
