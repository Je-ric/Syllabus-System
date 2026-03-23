<x-modal.dialog id="confirmCourseModal" maxWidth="xl:max-w-xl lg:max-w-lg md:max-w-md sm:max-w-sm max-w-xs" width="w-full" maxHeight="max-h-[90vh]">
    <x-modal.header>
        <h2 class="text-lg sm:text-xl font-bold text-blue-600 flex items-center gap-2">
            <i class="bx bx-check-circle text-2xl"></i>
            Confirm Course Creation
        </h2>
    </x-modal.header>

    <x-modal.body>
        <div class="flex flex-col items-center text-center gap-4">
            <div class="bg-blue-100 rounded-full w-12 h-12 flex items-center justify-center">
                <i class="bx bx-check text-2xl text-blue-500"></i>
            </div>
            <h3 class="text-base sm:text-lg font-semibold text-blue-700">Ready to create this course?</h3>
            <p class="text-sm text-gray-600">Please review all course details and Program Outcomes IED levels before creating. Once created, the course will be added to the program.</p>
            <x-feedback-status.alert type="info" title="Make sure all field values and IED level selections are correct before proceeding." class="w-full" />
        </div>
    </x-modal.body>

    <x-modal.footer>
        <div class="flex gap-2 w-full justify-end flex-col sm:flex-row">
            <x-modal.close-button modalId="confirmCourseModal" text="Review Again" variant="cancel" />
            <x-button type="button" variant="save" onclick="confirmCourseSubmit()" class="w-full sm:w-auto">
                <i class="bx bx-check"></i> Confirm & Create
            </x-button>
        </div>
    </x-modal.footer>
</x-modal.dialog>
