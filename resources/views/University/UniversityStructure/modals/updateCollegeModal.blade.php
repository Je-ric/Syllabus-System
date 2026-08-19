@php
    $editErrors = $errors->hasAny(['name']) && str_starts_with(session('_modal', ''), 'updateCollege_');
    $editModalId = session('_modal', '');
@endphp

<x-modal.dialog id="updateCollegeModal" maxWidth="max-w-md" width="w-11/12" variant="edit">
    <x-modal.header modalId="updateCollegeModal" variant="edit">
        <div>
            <p class="text-[15px] font-bold text-[#0f172a]">Edit College</p>
            <p id="updateCollegeModal_subtitle" class="text-[13px] text-[#94a3b8] truncate"></p>
        </div>
    </x-modal.header>

    <form id="updateCollegeModal_form" method="POST" class="flex flex-col"
        x-data="{
            submitting: false,
            original: '',
            name: ''
        }"
        x-on:submit="submitting = true">
        @csrf
        @method('PUT')
        <input type="hidden" name="_modal" id="updateCollegeModal_modalKey" value="">
        <x-modal.body>
            <div class="space-y-3">
                @if ($editErrors)
                    <x-feedback-status.alert type="error" :showTitle="false">
                        Please fix the highlighted fields below before submitting.
                    </x-feedback-status.alert>
                @endif
                <div>
                    <x-modal.modal-label for="updateCollegeModal_name" isRequired>College Name</x-modal.modal-label>
                    <x-form.input
                        id="updateCollegeModal_name"
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
            <x-modal.close-button modalId="updateCollegeModal" text="Cancel" ::disabled="submitting" />
            <x-ui.button type="submit" variant="save"
                submitting="submitting" loadingText="Saving…"
                ::disabled="submitting || !name.trim() || name.trim() === original">
                <i class="bx bx-save"></i> Save Changes
            </x-ui.button>
        </x-modal.footer>
    </form>
</x-modal.dialog>

<script>
function openEditCollegeModal(id, name, routeBase) {
    const modal = document.getElementById('updateCollegeModal');
    const form  = document.getElementById('updateCollegeModal_form');
    form.action = routeBase + '/' + id;
    document.getElementById('updateCollegeModal_subtitle').textContent = name;
    document.getElementById('updateCollegeModal_modalKey').value = 'updateCollege_' + id;
    const data = Alpine.$data(form);
    data.name     = name;
    data.original = name;
    data.submitting = false;
    modal.showModal();
}

@if ($editErrors)
    document.addEventListener('alpine:init', () => {
        openEditCollegeModal(
            @js(explode('_', $editModalId)[1] ?? ''),
            @js(old('name', '')),
            '{{ rtrim(url('/university-structure/college'), '/') }}'
        );
    });
@endif
</script>
