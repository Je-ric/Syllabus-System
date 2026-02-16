<x-modal.dialog id="confirmCourseModal" maxWidth="max-w-lg" width="w-11/12">
    <x-modal.header>
        Confirm Course Creation
        <x-modal.x-button modalId="confirmCourseModal" />
    </x-modal.header>

    <x-modal.body>
        <div class="space-y-4">
            <p class="text-gray-700 font-medium">
                Please review the course details and Program Outcomes IED levels before creating this course.
            </p>
            <p class="text-blue-600 text-sm">
                Make sure all course details are correct before proceeding.
            </p>
        </div>
    </x-modal.body>

    <x-modal.footer>
        <div class="w-full flex gap-2 justify-end">
            <x-modal.close-button modalId="confirmCourseModal" text="Review Again" />

            <x-button type="button" variant="save" onclick="confirmCourseSubmit()">
                <i class="bx bx-check"></i>
                Confirm & Create
            </x-button>
        </div>
    </x-modal.footer>
</x-modal.dialog>

