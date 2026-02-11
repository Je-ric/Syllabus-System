<x-modal.dialog id="confirmEditCourseModal" maxWidth="max-w-lg" width="w-11/12">
    <x-modal.header>
        Confirm Course Edit
        <x-modal.x-button modalId="confirmEditCourseModal" />
    </x-modal.header>

    <x-modal.body>
        <div class="space-y-4">
            <p class="text-gray-700 font-medium">Please review the course details and the Program Outcomes IED levels before editing:</p>
            <div class="bg-gray-50 p-4 rounded border border-gray-200 space-y-3">
                <div>
                    <p class="font-semibold text-sm text-gray-600">Course Name:</p>
                    <p class="text-base" id="confirm-course-name">-</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="font-semibold text-sm text-gray-600">Course Code:</p>
                        <p class="text-sm" id="confirm-course-code">-</p>Course
                    </div>
                </div>
            </div>
            <p class="text-blue-600 text-sm">✓ Make sure all course details are correct before proceeding.</p>
        </div>
    </x-modal.body>

    <x-modal.footer>
        <div class="w-full flex gap-2 justify-end">
            <x-modal.close-button modalId="confirmEditCourseModal" text="Review Again" />

            <x-button type="button" variant="save" onclick="document.getElementById('courseForm').submit()">
                <i class="bx bx-check"></i>
                Confirm & Create
            </x-button>
        </div>
    </x-modal.footer>
</x-modal.dialog>

