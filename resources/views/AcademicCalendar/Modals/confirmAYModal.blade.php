<x-modal.dialog id="confirmAYModal" maxWidth="max-w-md" width="w-11/12">
    <x-modal.header modalId="confirmAYModal" variant="confirm">
        <span class="text-green-800">Confirm Academic Year</span>
    </x-modal.header>

    <x-modal.body>
        <div class="space-y-4">
            <p class="text-[13px] text-[#475569]">Please review the details before creating.</p>

            <div class="rounded-xl border border-[#e2e8f0] bg-[#f8fafc] p-4 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">Academic Year</span>
                    <span class="text-[13px] font-bold text-[#0f172a]" id="confirm-ay-year">—</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">1st Semester</span>
                    <span class="text-[13px] text-[#475569]" id="confirm-sem1-dates">—</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">2nd Semester</span>
                    <span class="text-[13px] text-[#475569]" id="confirm-sem2-dates">—</span>
                </div>
            </div>

            <x-feedback-status.alert type="info" :showTitle="false">Make sure all dates are correct before proceeding.</x-feedback-status.alert>
        </div>
    </x-modal.body>

    <x-modal.footer>
        <x-modal.close-button modalId="confirmAYModal" text="Review Again" />
        <x-ui.button type="button" variant="add-button" onclick="document.getElementById('academicCalendarForm').submit()">
            <i class="bx bx-check"></i> Confirm &amp; Create
        </x-ui.button>
    </x-modal.footer>
</x-modal.dialog>

<script>
function showConfirmModal() {
    const academicYear = document.querySelector('input[name="academic_year"]').value;
    const startDate1   = document.querySelector('input[name="start_date_1"]').value;
    const endDate1     = document.querySelector('input[name="end_date_1"]').value;
    const startDate2   = document.querySelector('input[name="start_date_2"]').value;
    const endDate2     = document.querySelector('input[name="end_date_2"]').value;

    if (!academicYear || !startDate1 || !endDate1 || !startDate2 || !endDate2) {
        alert('Please fill in all required fields');
        return false;
    }

    const fmt = d => d ? new Date(d).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : '—';

    document.getElementById('confirm-ay-year').textContent    = academicYear;
    document.getElementById('confirm-sem1-dates').textContent = `${fmt(startDate1)} – ${fmt(endDate1)}`;
    document.getElementById('confirm-sem2-dates').textContent = `${fmt(startDate2)} – ${fmt(endDate2)}`;

    document.getElementById('confirmAYModal').showModal();
    return false;
}
</script>
