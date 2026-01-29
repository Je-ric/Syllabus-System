<x-modal.dialog id="confirmAYModal" maxWidth="max-w-lg" width="w-11/12">
    <x-modal.header>
        Confirm Academic Year Creation
        <x-modal.x-button modalId="confirmAYModal" />
    </x-modal.header>

    <x-modal.body>
        <div class="space-y-4">
            <p class="text-gray-700 font-medium">Please review the academic year details before creating:</p>
            <div class="bg-gray-50 p-4 rounded border border-gray-200 space-y-3">
                <div>
                    <p class="font-semibold text-sm text-gray-600">Academic Year:</p>
                    <p class="text-base" id="confirm-ay-year">-</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="font-semibold text-sm text-gray-600">1st Semester:</p>
                        <p class="text-sm" id="confirm-sem1-dates">-</p>
                    </div>
                    <div>
                        <p class="font-semibold text-sm text-gray-600">2nd Semester:</p>
                        <p class="text-sm" id="confirm-sem2-dates">-</p>
                    </div>
                </div>
            </div>
            <p class="text-blue-600 text-sm">✓ Make sure all dates are correct before proceeding.</p>
        </div>
    </x-modal.body>

    <x-modal.footer>
        <div class="w-full flex gap-2 justify-end">
            <x-modal.close-button modalId="confirmAYModal" text="Review Again" />

            <x-button type="button" variant="save" onclick="document.getElementById('academicCalendarForm').submit()">
                <i class="bx bx-check"></i>
                Confirm & Create
            </x-button>
        </div>
    </x-modal.footer>
</x-modal.dialog>

<script>
function showConfirmModal() {
    // Get form values
    const academicYear = document.querySelector('input[name="academic_year"]').value;
    const startDate1 = document.querySelector('input[name="start_date_1"]').value;
    const endDate1 = document.querySelector('input[name="end_date_1"]').value;
    const startDate2 = document.querySelector('input[name="start_date_2"]').value;
    const endDate2 = document.querySelector('input[name="end_date_2"]').value;

    // Validate all fields are filled
    if (!academicYear || !startDate1 || !endDate1 || !startDate2 || !endDate2) {
        alert('Please fill in all required fields');
        return false;
    }

    // Format dates
    const formatDate = (dateStr) => {
        if (!dateStr) return '-';
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    };

    // Update modal content
    document.getElementById('confirm-ay-year').textContent = academicYear;
    document.getElementById('confirm-sem1-dates').textContent = `${formatDate(startDate1)} - ${formatDate(endDate1)}`;
    document.getElementById('confirm-sem2-dates').textContent = `${formatDate(startDate2)} - ${formatDate(endDate2)}`;

    // Show modal
    document.getElementById('confirmAYModal').showModal();
    return false;
}
</script>
