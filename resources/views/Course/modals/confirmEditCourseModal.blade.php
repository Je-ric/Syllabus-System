<x-modal.dialog id="confirmEditCourseModal" maxWidth="max-w-md" width="w-11/12">
    <x-modal.header modalId="confirmEditCourseModal">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-[#eff6ff] text-[#1d4ed8] shrink-0">
                <i class="bx bx-edit-alt text-base leading-none"></i>
            </span>
            <div>
                <p class="text-[15px] font-bold text-[#0f172a]">Confirm Course Update</p>
                <p class="text-[13px] text-[#94a3b8]">Review the changes before saving.</p>
            </div>
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
        <x-button type="button" variant="save" onclick="confirmCourseSubmit()">
            <i class="bx bx-check"></i> Confirm & Update
        </x-button>
    </x-modal.footer>
</x-modal.dialog>
