<x-modal.dialog id="deleteCourseModal_{{ $course->id }}" maxWidth="max-w-md" width="w-11/12" variant="delete">
    <x-modal.header modalId="deleteCourseModal_{{ $course->id }}" variant="delete">
        <span>Delete Course</span>
    </x-modal.header>

    <x-modal.body>
        <div class="space-y-4">

            {{-- Course identity --}}
            <div class="flex items-start gap-3">
                <div class="min-w-0">
                    <p class="text-[13px] font-semibold text-[#1D2836] leading-snug">{{ $course->course_title }}</p>
                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                        <span class="font-mono text-[11px] font-bold text-[#4F5D6B] bg-[#F1F3F5] border border-[#E3E8EB] px-2 py-0.5 rounded-md">{{ $course->course_code }}</span>
                        @if ($course->program)
                            <span class="text-[12px] text-[#93A1AF]">{{ $course->program->name }}</span>
                        @endif
                    </div>
                    @php $syllabusCount = $course->syllabi()->count(); @endphp
                    @if ($syllabusCount > 0)
                        <p class="mt-1.5 text-[12px] text-[#93A1AF]">
                            <i class="bx bx-link text-[11px]"></i>
                            {{ $syllabusCount }} {{ Str::plural('syllabus', $syllabusCount) }} linked
                        </p>
                    @endif
                </div>
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
            <x-ui.button type="submit" variant="danger">
                <i class="bx bx-trash"></i> Delete Course
            </x-ui.button>
        </form>
    </x-modal.footer>
</x-modal.dialog>
