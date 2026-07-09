<x-modal.dialog id="archiveCourseModal_{{ $course->id }}" maxWidth="max-w-md" width="w-11/12">
    <x-modal.header modalId="archiveCourseModal_{{ $course->id }}" variant="archive">
        <span class="text-amber-800">Archive Course</span>
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
            <x-ui.button type="submit" variant="warning">
                <i class="bx bx-archive"></i> Archive
            </x-ui.button>
        </form>
    </x-modal.footer>
</x-modal.dialog>
