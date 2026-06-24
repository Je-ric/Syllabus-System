{{-- outcomes-partials/course-info-drawer.blade.php
     Requires: $courseInfo array from CourseOutcomesStep::render()
     Alpine state in parent: courseInfoOpen
--}}
<x-offcanvas title="Course Info" subtitle="Reference while writing outcomes" icon="bx-book" open="courseInfoOpen">

    
    @if (!empty($courseInfo['program_title']))
        <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400 mb-0.5">Program</p>
            <p class="text-[13px] text-slate-600">{{ $courseInfo['program_title'] }}</p>
        </div>
    @endif
    @if (!empty($courseInfo))
        <div class="space-y-4">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400 mb-0.5">Course Code</p>
                <p class="text-[13px] font-semibold text-slate-800">{{ $courseInfo['course_code'] ?? '—' }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400 mb-0.5">Course Title</p>
                <p class="text-[13px] font-semibold text-slate-800">{{ $courseInfo['course_title'] ?? '—' }}</p>
            </div>
            @if (!empty($courseInfo['description']))
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400 mb-0.5">Description</p>
                    <p class="text-[13px] text-slate-600 leading-relaxed">{{ $courseInfo['description'] }}</p>
                </div>
            @endif
            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5">
                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400 mb-0.5">LEC Hrs</p>
                    <p class="text-[15px] font-bold text-emerald-700">{{ $courseInfo['lec_units'] ?? '—' }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5">
                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400 mb-0.5">LAB Hrs</p>
                    <p class="text-[15px] font-bold text-blue-700">{{ $courseInfo['lab_units'] ?? '—' }}</p>
                </div>
            </div>

        </div>
    @else
        <x-empty-state icon="bx-book" title="No course data" message="Course information could not be loaded." />
    @endif

</x-offcanvas>
