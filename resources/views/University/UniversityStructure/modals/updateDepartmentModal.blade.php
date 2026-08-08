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
        x-on:submit="submitting = true">
        @csrf
        @method('PUT')
        <input type="hidden" name="college_id" value="{{ $dept->college_id }}">
        <x-modal.body>
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
