<x-modal.dialog id="deleteProgramModal" maxWidth="max-w-md" width="w-11/12" variant="delete">
    <x-modal.header modalId="deleteProgramModal" variant="delete">
        <span class="text-[#9f1239]">Delete Program</span>
    </x-modal.header>

    <x-modal.body>
        <div class="space-y-4">
            <p class="text-[13px] text-[#475569]">Are you sure you want to delete this program?</p>

            <div class="rounded-xl border border-[#e2e8f0] bg-[#f8fafc] p-4 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">Program</span>
                    <span id="deleteProgramModal_name" class="text-[13px] font-semibold text-[#0f172a] text-right max-w-[60%]"></span>
                </div>
                <div id="deleteProgramModal_borRow" class="hidden flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">BOR Approval Resolution No.</span>
                    <span id="deleteProgramModal_borNo" class="text-[13px] text-[#475569]"></span>
                </div>
                <div id="deleteProgramModal_courseRow" class="hidden flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">Courses</span>
                    <span id="deleteProgramModal_courseCount" class="text-[13px] font-semibold text-rose-600"></span>
                </div>
            </div>

            <x-feedback-status.alert type="error" :showTitle="false">
                This will permanently delete the program and all its courses and syllabi.
            </x-feedback-status.alert>
        </div>
    </x-modal.body>

    <x-modal.footer>
        <x-modal.close-button modalId="deleteProgramModal" text="Cancel" />
        <form id="deleteProgramModal_form" method="POST"
            x-data="{ submitting: false }"
            x-on:submit="submitting = true">
            @csrf
            @method('DELETE')
            <x-ui.button type="submit" variant="danger"
                submitting="submitting" loadingText="Deleting…"
                ::disabled="submitting">
                <i class="bx bx-trash"></i> Delete Program
            </x-ui.button>
        </form>
    </x-modal.footer>
</x-modal.dialog>

<script>
function openDeleteProgramModal(id, name, borNo, courseCount, routeBase) {
    document.getElementById('deleteProgramModal_name').textContent = name;
    document.getElementById('deleteProgramModal_form').action = routeBase + '/' + id;

    const borRow = document.getElementById('deleteProgramModal_borRow');
    if (borNo) {
        borRow.classList.remove('hidden');
        document.getElementById('deleteProgramModal_borNo').textContent = borNo;
    } else {
        borRow.classList.add('hidden');
    }

    const courseRow = document.getElementById('deleteProgramModal_courseRow');
    if (courseCount > 0) {
        courseRow.classList.remove('hidden');
        document.getElementById('deleteProgramModal_courseCount').textContent = courseCount;
    } else {
        courseRow.classList.add('hidden');
    }

    Alpine.$data(document.getElementById('deleteProgramModal_form')).submitting = false;
    document.getElementById('deleteProgramModal').showModal();
}
</script>
