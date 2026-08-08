<x-modal.dialog id="confirmEditCourseModal" maxWidth="max-w-md" width="w-11/12" variant="confirm">
    <x-modal.header modalId="confirmEditCourseModal" variant="confirm">
        <div>
            <p class="text-[15px] font-bold text-[#0f172a]">Confirm Course Update</p>
            <p class="text-[13px] text-[#94a3b8]">Review the changes before saving.</p>
        </div>
    </x-modal.header>

    <x-modal.body>
        <div class="space-y-4">
            <p class="text-[13px] text-[#475569]">
                Please review all course details and Program Outcomes IED levels. This will overwrite the existing course data.
            </p>
            <x-feedback-status.alert type="info" :showTitle="false">
                Make sure all field values and IED level selections are correct before proceeding.
            </x-feedback-status.alert>
        </div>
    </x-modal.body>

    <x-modal.footer>
        <x-modal.close-button modalId="confirmEditCourseModal" text="Review Again" />
        <div x-data="{ submitting: false }">
            <x-ui.button type="button" variant="save"
                submitting="submitting" loadingText="Updating…"
                ::disabled="submitting"
                x-on:click="submitting = true; confirmCourseSubmit()">
                <i class="bx bx-check"></i> Confirm & Update
            </x-ui.button>
        </div>
    </x-modal.footer>
</x-modal.dialog>
