<x-modal.dialog id="confirmCourseModal" maxWidth="max-w-md" width="w-11/12">
    <x-modal.header modalId="confirmCourseModal">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-[#eff6ff] text-[#1d4ed8] shrink-0">
                <i class="bx bx-check-circle text-base leading-none"></i>
            </span>
            <div>
                <p class="text-[15px] font-bold text-[#0f172a]">Confirm Course Creation</p>
                <p class="text-[13px] text-[#94a3b8]">Review the details before creating.</p>
            </div>
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
        <x-button type="button" variant="save" onclick="confirmCourseSubmit()">
            <i class="bx bx-check"></i> Confirm & Create
        </x-button>
    </x-modal.footer>
</x-modal.dialog>
