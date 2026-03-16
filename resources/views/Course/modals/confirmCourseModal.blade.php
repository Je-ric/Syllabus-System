<x-modal.dialog id="confirmCourseModal" maxWidth="max-w-lg" width="w-11/12">

    <x-modal.header modalId="confirmCourseModal">
        <h2 class="text-xl font-semibold text-emerald-900 tracking-tight">Confirm Course Creation</h2>
    </x-modal.header>

    <x-modal.body>
        <div class="space-y-4">
            <p class="rounded-xl text-md border border-slate-200 bg-slate-50 px-4 py-3 space-y-1.5">
                Please review all course details and Program Outcomes IED levels before creating this course.
                Once created, the course will be added to the program.
            </p>
            <x-feedback-status.alert type="info">
                Make sure all field values and IED level selections are correct before proceeding.
            </x-feedback-status.alert>
        </div>
    </x-modal.body>

    <x-modal.footer>
        <x-modal.close-button modalId="confirmCourseModal" text="Review Again" variant="cancel" />
        <x-button type="button" variant="save" onclick="confirmCourseSubmit()">
            <i class="bx bx-check"></i> Confirm &amp; Create
        </x-button>
    </x-modal.footer>

</x-modal.dialog>
