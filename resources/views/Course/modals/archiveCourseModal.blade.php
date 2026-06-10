<x-modal.dialog id="archiveCourseModal_{{ $course->id }}" maxWidth="max-w-md" width="w-11/12">
    <x-modal.header modalId="archiveCourseModal_{{ $course->id }}">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-amber-100 text-amber-600 shrink-0">
                <i class="bx bx-archive text-base leading-none"></i>
            </span>
            <span class="text-amber-800">Archive Course</span>
        </div>
    </x-modal.header>
    <x-modal.body>
        <p class="text-[13px] text-[#475569] mb-3">Archive <strong>{{ $course->course_code }}</strong> — {{ $course->course_title }}?</p>
        <x-feedback-status.alert type="warning" :showTitle="false">
            Archived courses are hidden from active listings but not deleted. You can restore them later.
        </x-feedback-status.alert>
    </x-modal.body>
    <x-modal.footer>
        <x-modal.close-button :modalId="'archiveCourseModal_' . $course->id" text="Cancel" />
        <form action="{{ route('courses.archive', $course->id) }}" method="POST">
            @csrf
            <x-button type="submit" variant="table-warning">
                <i class="bx bx-archive"></i> Archive
            </x-button>
        </form>
    </x-modal.footer>
</x-modal.dialog>
