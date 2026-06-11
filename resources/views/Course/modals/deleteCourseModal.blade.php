<x-modal.dialog id="deleteCourseModal_{{ $course->id }}" maxWidth="max-w-md" width="w-11/12" variant="delete">
    <x-modal.header modalId="deleteCourseModal_{{ $course->id }}" variant="delete">
        <span class="text-[#9f1239]">Delete Course</span>
    </x-modal.header>

    <x-modal.body>
        <div class="space-y-4">
            <p class="text-[13px] text-[#475569]">Are you sure you want to delete this course?</p>

            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-rose-400">Code</span>
                    <span class="font-mono text-[13px] font-bold text-rose-600">{{ $course->course_code }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-rose-400">Title</span>
                    <span class="text-[13px] text-rose-700 text-right max-w-[60%]">{{ $course->course_title }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-rose-400">Program</span>
                    <span class="text-[13px] text-rose-700">{{ $course->program?->name ?? '—' }}</span>
                </div>
                @php $syllabusCount = $course->syllabi()->count(); @endphp
                @if ($syllabusCount > 0)
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-rose-400">Linked Syllabi</span>
                        <span class="text-[13px] font-semibold text-rose-600">{{ $syllabusCount }}</span>
                    </div>
                @endif
            </div>

            <x-feedback-status.alert type="error" :showTitle="false">
                This will permanently delete the course and all its syllabi, components, outcomes, weekly coverage, evaluations, and PO mappings.
            </x-feedback-status.alert>
        </div>
    </x-modal.body>

    <x-modal.footer>
        <x-modal.close-button :modalId="'deleteCourseModal_' . $course->id" text="Cancel" />
        <form action="{{ route('courses.destroy', $course->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <x-button type="submit" variant="danger">
                <i class="bx bx-trash"></i> Delete Course
            </x-button>
        </form>
    </x-modal.footer>
</x-modal.dialog>
