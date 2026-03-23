<x-modal.dialog id="cancelEditModal" maxWidth="xl:max-w-xl lg:max-w-lg md:max-w-md sm:max-w-sm max-w-xs" width="w-full" maxHeight="max-h-[90vh]">
    <x-modal.header>
        <h2 class="text-lg sm:text-xl font-bold text-amber-600 flex items-center gap-2">
            <i class="bx bx-error text-2xl"></i>
            Discard Changes?
        </h2>
    </x-modal.header>

    <x-modal.body>
        <div class="flex flex-col items-center text-center gap-4">
            <div class="bg-amber-100 rounded-full w-12 h-12 flex items-center justify-center">
                <i class="bx bx-error text-2xl text-amber-500"></i>
            </div>
            <h3 class="text-base sm:text-lg font-semibold text-amber-700">Are you sure you want to leave this page?</h3>
            <x-feedback-status.alert type="warning" title="All unsaved changes will be permanently lost." class="w-full" />
        </div>
    </x-modal.body>

    <x-modal.footer>
        <div class="flex gap-2 w-full justify-end flex-col sm:flex-row">
            <x-modal.close-button modalId="cancelEditModal" text="Stay on Page" variant="cancel" />
            <x-button type="button" variant="danger"
                onclick="window.location.href='{{ route('academic.calendars.index') }}'">
                <i class="bx bx-x"></i> Discard Changes
            </x-button>
        </div>
    </x-modal.footer>
</x-modal.dialog>
