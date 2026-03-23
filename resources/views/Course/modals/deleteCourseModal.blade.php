<x-modal.dialog id="deleteCourseModal_{{ $course->id }}" maxWidth="xl:max-w-xl lg:max-w-lg md:max-w-md sm:max-w-sm max-w-xs" width="w-full" maxHeight="max-h-[90vh]">
    <x-modal.header>
        <h2 class="text-lg sm:text-xl font-bold text-red-600 flex items-center gap-2">
            <i class="bx bx-trash text-2xl"></i>
            Delete Course
            <span class="font-mono text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/70 px-2 py-0.5 rounded-md">
                {{ $course->course_code }}
            </span>
        </h2>
    </x-modal.header>

    <x-modal.body>
        <div class="flex flex-col items-center text-center gap-4">
            <div class="bg-red-100 rounded-full w-12 h-12 flex items-center justify-center">
                <i class="bx bx-trash text-2xl text-red-500"></i>
            </div>
            <h3 class="text-base sm:text-lg font-semibold text-red-700">Are you sure you want to delete this course?</h3>

            <div class="bg-gray-50 rounded-lg p-4 w-full text-left">
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700 text-sm">Course Code:</span>
                        <span class="text-sm font-mono font-bold text-gray-800">{{ $course->course_code }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700 text-sm">Title:</span>
                        <span class="text-sm text-gray-800">{{ $course->course_title }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700 text-sm">Program:</span>
                        <span class="text-sm text-gray-800">{{ $course->program?->name ?? '—' }}</span>
                    </div>
                    @php $syllabusCount = $course->syllabi()->count(); @endphp
                    @if ($syllabusCount > 0)
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-700 text-sm">Linked Syllabi:</span>
                            <span class="text-sm font-semibold text-red-600">{{ $syllabusCount }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <x-feedback-status.alert type="error"
                title="This will permanently delete the course and all its syllabi, components, outcomes, weekly coverage, evaluations, and PO mappings." class="w-full" />
        </div>
    </x-modal.body>

    <x-modal.footer>
        <div class="flex gap-2 w-full justify-end flex-col sm:flex-row">
            <x-modal.close-button :modalId="'deleteCourseModal_' . $course->id" text="Cancel" variant="cancel" />
            <form action="{{ route('courses.destroy', $course->id) }}" method="POST" class="w-full sm:w-auto">
                @csrf
                @method('DELETE')
                <x-button type="submit" variant="danger" class="w-full sm:w-auto">
                    <i class="bx bx-trash"></i> Delete Course
                </x-button>
            </form>
        </div>
    </x-modal.footer>
</x-modal.dialog>
