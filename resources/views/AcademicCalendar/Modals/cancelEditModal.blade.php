<x-modal.dialog id="cancelEditModal" maxWidth="max-w-md" width="w-11/12">
    <x-modal.header>
        Discard Changes?
        <x-modal.x-button modalId="cancelEditModal" />
    </x-modal.header>

    <x-modal.body>
        <div class="space-y-3">
            <p class="text-gray-700">You have unsaved changes. Are you sure you want to leave this page?</p>
            <p class="text-amber-600 text-sm font-medium"><i class="bx bx-error"></i> All changes will be lost if you proceed.</p>
        </div>
    </x-modal.body>

    <x-modal.footer>
        <div class="w-full flex gap-2 justify-end">
            <x-modal.close-button modalId="cancelEditModal" text="Stay on Page" />

            <x-button
                type="button"
                variant="table-danger"
                onclick="window.location.href='{{ route('academic.calendars.index') }}'">
                <i class="bx bx-x"></i>
                Discard Changes
            </x-button>
        </div>
    </x-modal.footer>
</x-modal.dialog>
