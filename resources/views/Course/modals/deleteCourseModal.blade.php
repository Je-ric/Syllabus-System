<x-modal.dialog id="deleteCourseModal_{{ $course->id }}" maxWidth="max-w-md" width="w-11/12">
    <x-modal.header modalId="deleteCourseModal_{{ $course->id }}">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-[#ffe4e6] text-[#e11d48] shrink-0">
                <i class="bx bx-trash text-base leading-none"></i>
            </span>
            <div class="flex items-center gap-2 min-w-0">
                <span class="text-[#9f1239]">Delete Course</span>
                <span class="font-mono text-[11px] font-bold text-[#166534] bg-[#f0fdf4] border border-[#bbf7d0] px-2 py-0.5 rounded-md shrink-0">
                    {{ $course->course_code }}
                </span>
            </div>
        </div>
    </x-modal.header>

    <x-modal.body>
        <div class="space-y-4">
            <p class="text-[13px] text-[#475569]">Are you sure you want to delete this course?</p>

            <div class="rounded-xl border border-[#e2e8f0] bg-[#f8fafc] p-4 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">Code</span>
                    <span class="font-mono text-[13px] font-bold text-[#0f172a]">{{ $course->course_code }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">Title</span>
                    <span class="text-[13px] text-[#475569] text-right max-w-[60%]">{{ $course->course_title }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">Program</span>
                    <span class="text-[13px] text-[#475569]">{{ $course->program?->name ?? '—' }}</span>
                </div>
                @php $syllabusCount = $course->syllabi()->count(); @endphp
                @if ($syllabusCount > 0)
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">Linked Syllabi</span>
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
