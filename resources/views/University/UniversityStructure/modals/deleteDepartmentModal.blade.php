<x-modal.dialog id="deleteDepartmentModal" maxWidth="max-w-md" width="w-11/12" variant="delete">
    <x-modal.header modalId="deleteDepartmentModal" variant="delete">
        <span class="text-[#9f1239]">Delete Department</span>
    </x-modal.header>

    <x-modal.body>
        <div class="space-y-4">
            <p class="text-[13px] text-[#475569]">Are you sure you want to delete this department?</p>

            <div class="rounded-xl border border-[#e2e8f0] bg-[#f8fafc] p-4 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">Department</span>
                    <span id="deleteDepartmentModal_name" class="text-[13px] font-semibold text-[#0f172a]"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">College</span>
                    <span id="deleteDepartmentModal_college" class="text-[13px] text-[#475569]"></span>
                </div>
                <div id="deleteDepartmentModal_programRow" class="hidden flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">Programs</span>
                    <span id="deleteDepartmentModal_programCount" class="text-[13px] font-semibold text-rose-600"></span>
                </div>
            </div>

            <x-feedback-status.alert type="error" :showTitle="false">
                This will permanently delete the department and all its programs and objectives. Cannot delete if courses exist under programs.
            </x-feedback-status.alert>
        </div>
    </x-modal.body>

    <x-modal.footer>
        <x-modal.close-button modalId="deleteDepartmentModal" text="Cancel" />
        <form id="deleteDepartmentModal_form" method="POST"
            x-data="{ submitting: false }"
            x-on:submit="submitting = true">
            @csrf
            @method('DELETE')
            <x-ui.button type="submit" variant="danger"
                submitting="submitting" loadingText="Deleting…"
                ::disabled="submitting">
                <i class="bx bx-trash"></i> Delete Department
            </x-ui.button>
        </form>
    </x-modal.footer>
</x-modal.dialog>

<script>
function openDeleteDepartmentModal(id, name, collegeName, programCount, routeBase) {
    document.getElementById('deleteDepartmentModal_name').textContent = name;
    document.getElementById('deleteDepartmentModal_college').textContent = collegeName;
    document.getElementById('deleteDepartmentModal_form').action = routeBase + '/' + id;
    const programRow = document.getElementById('deleteDepartmentModal_programRow');
    if (programCount > 0) {
        programRow.classList.remove('hidden');
        document.getElementById('deleteDepartmentModal_programCount').textContent = programCount;
    } else {
        programRow.classList.add('hidden');
    }
    Alpine.$data(document.getElementById('deleteDepartmentModal_form')).submitting = false;
    document.getElementById('deleteDepartmentModal').showModal();
}
</script>
