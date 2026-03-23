<x-modal.dialog id="confirmAYModal" maxWidth="xl:max-w-xl lg:max-w-lg md:max-w-md sm:max-w-sm max-w-xs" width="w-full" maxHeight="max-h-[90vh]">
    <x-modal.header>
        <h2 class="text-lg sm:text-xl font-bold text-blue-600 flex items-center gap-2">
            <i class="bx bx-calendar-check text-2xl"></i>
            Confirm Academic Year
        </h2>
    </x-modal.header>

    <x-modal.body>
        <div class="flex flex-col items-center text-center gap-4">
            <div class="bg-blue-100 rounded-full w-12 h-12 flex items-center justify-center">
                <i class="bx bx-calendar-check text-2xl text-blue-500"></i>
            </div>
            <h3 class="text-base sm:text-lg font-semibold text-blue-700">Please review the details before creating.</h3>

            <div class="bg-gray-50 rounded-lg p-4 w-full text-left">
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700 text-sm">Academic Year:</span>
                        <span class="text-sm font-semibold text-gray-800" id="confirm-ay-year">—</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700 text-sm">1st Semester:</span>
                        <span class="text-sm text-gray-800" id="confirm-sem1-dates">—</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700 text-sm">2nd Semester:</span>
                        <span class="text-sm text-gray-800" id="confirm-sem2-dates">—</span>
                    </div>
                </div>
            </div>

            <x-feedback-status.alert type="info" title="Make sure all dates are correct before proceeding." class="w-full" />
        </div>
    </x-modal.body>

    <x-modal.footer>
        <div class="flex gap-2 w-full justify-end flex-col sm:flex-row">
            <x-modal.close-button modalId="confirmAYModal" text="Review Again" variant="cancel" />
            <x-button type="button" variant="save" onclick="document.getElementById('academicCalendarForm').submit()">
                <i class="bx bx-check"></i> Confirm & Create
            </x-button>
        </div>
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
