<x-modal.dialog id="confirmEditCourseModal" maxWidth="max-w-lg" width="w-11/12">

    {{-- modalId prop makes x-modal.header render its own close button — no x-modal.x-button needed in slot --}}
    <x-modal.header modalId="confirmEditCourseModal">
        <h2 class="text-xl font-semibold text-blue-900 tracking-tight">Confirm Course Update</h2>
    </x-modal.header>

    <x-modal.body>
        <div class="space-y-4">
            <p class="rounded-xl text-md border border-slate-200 bg-slate-50 px-4 py-3 space-y-1.5">
                Please review all course details and Program Outcomes IED levels before updating this course.
                This will overwrite the existing course data.
            </p>
            <x-feedback-status.alert type="info">
                Make sure all field values and IED level selections are correct before proceeding.
            </x-feedback-status.alert>
        </div>
    </x-modal.body>

    <x-modal.footer>
        <x-modal.close-button modalId="confirmEditCourseModal" text="Review Again" variant="cancel" />
        <x-button type="button" variant="save" onclick="confirmCourseSubmit()">
            <i class="bx bx-check"></i> Confirm &amp; Update
        </x-button>
    </x-modal.footer>

</x-modal.dialog>
