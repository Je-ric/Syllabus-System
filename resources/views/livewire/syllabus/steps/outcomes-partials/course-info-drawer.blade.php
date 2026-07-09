{{-- outcomes-partials/course-info-drawer.blade.php --}}
<x-offcanvas title="Course Info" subtitle="Reference while writing outcomes" icon="bx-book" open="courseInfoOpen">

    @if (!empty($courseInfo))
        <div class="space-y-4">

            @if (!empty($courseInfo['program_title']))
                <div>
                    <x-form.label>Program</x-form.label>
                    <p class="mt-1 text-[13px] text-[#18181b]">{{ $courseInfo['program_title'] }}</p>
                </div>
                <div class="border-t border-[#e4e4e7]"></div>
            @endif

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-form.label>Course Code</x-form.label>
                    <p class="mt-1 text-[14px] font-bold text-[#09090b]">{{ $courseInfo['course_code'] ?? '—' }}</p>
                </div>
                <div>
                    <x-form.label>Credit Units</x-form.label>
                    <p class="mt-1 text-[14px] font-bold text-[#09090b]">{{ $courseInfo['credit_units'] ?? '—' }}</p>
                </div>
            </div>

            <div>
                <x-form.label>Course Title</x-form.label>
                <p class="mt-1 text-[13px] font-semibold text-[#09090b]">{{ $courseInfo['course_title'] ?? '—' }}</p>
            </div>

            @if (!empty($courseInfo['description']))
                <div>
                    <x-form.label>Description</x-form.label>
                    <p class="mt-1 text-[13px] text-[#52525b] leading-relaxed">{{ $courseInfo['description'] }}</p>
                </div>
            @endif

            <div class="border-t border-[#e4e4e7]"></div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-form.label class="text-[#16a34a]">LEC Hours</x-form.label>
                    <p class="mt-1 text-[22px] font-bold text-[#15803d]">{{ $courseInfo['lec_class_hours'] ?? '—' }}</p>
                </div>
                @if (!empty($courseInfo['has_lec_lab']))
                    <div>
                        <x-form.label class="text-[#2563eb]">LAB Hours</x-form.label>
                        <p class="mt-1 text-[22px] font-bold text-[#1d4ed8]">{{ $courseInfo['lab_class_hours'] ?? '—' }}</p>
                    </div>
                @endif
            </div>

        </div>
    @else
        <x-feedback-status.empty-state icon="bx-book" title="No course data" message="Course information could not be loaded." />
    @endif

</x-offcanvas>
