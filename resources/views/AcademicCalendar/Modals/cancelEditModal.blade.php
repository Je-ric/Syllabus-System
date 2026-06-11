<x-modal.dialog id="cancelEditModal" maxWidth="max-w-sm" width="w-11/12">
    <x-modal.header modalId="cancelEditModal" variant="warning">
        <span class="text-amber-800">Discard Changes?</span>
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
