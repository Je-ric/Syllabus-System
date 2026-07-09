<x-modal.dialog id="confirmCourseModal" maxWidth="max-w-md" width="w-11/12">
    <x-modal.header modalId="confirmCourseModal" variant="confirm">
        <div>
            <p class="text-[15px] font-bold text-[#0f172a]">Confirm Course Creation</p>
            <p class="text-[13px] text-[#94a3b8]">Review the details before creating.</p>
        </div>
    </x-modal.header>

    <x-modal.body>
        <div class="space-y-4">
            <p class="text-[13px] text-[#475569]">
                Please review all course details and Program Outcomes IED levels. Once created, the course will be added to the program.
            </p>
            <x-feedback-status.alert type="info" :showTitle="false">
                Make sure all field values and IED level selections are correct before proceeding.
            </x-feedback-status.alert>
        </div>
    </x-modal.body>

    <x-modal.footer>
        <x-modal.close-button modalId="confirmCourseModal" text="Review Again" />
        <x-ui.button type="button" variant="add-button" onclick="confirmCourseSubmit()">
            <i class="bx bx-check"></i> Confirm & Create
        </x-ui.button>
    </x-modal.footer>
</x-modal.dialog>
