<x-modal.dialog id="cancelEditModal" maxWidth="max-w-sm" width="w-11/12">
    <x-modal.header modalId="cancelEditModal">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-[#fef3c7] text-[#d97706] shrink-0">
                <i class="bx bx-error text-base leading-none"></i>
            </span>
            <span class="text-[#92400e]">Discard Changes?</span>
        </div>
    </x-modal.header>

    <x-modal.body>
        <div class="space-y-3">
            <p class="text-[13px] text-[#475569]">Are you sure you want to leave this page?</p>
            <x-feedback-status.alert type="warning" :showTitle="false">All unsaved changes will be permanently lost.</x-feedback-status.alert>
        </div>
    </x-modal.body>

    <x-modal.footer>
        <x-modal.close-button modalId="cancelEditModal" text="Stay on Page" />
        <x-button type="button" variant="danger"
            onclick="window.location.href='{{ route('academic.calendars.index') }}'">
            <i class="bx bx-x"></i> Discard Changes
        </x-button>
    </x-modal.footer>
</x-modal.dialog>
