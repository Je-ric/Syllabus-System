{{-- outcomes-partials/course-info-drawer.blade.php --}}
<x-layout.offcanvas title="Course Info" subtitle="Reference while writing outcomes" icon="bx-book" open="courseInfoOpen">

    @if (!empty($courseInfo))
        <div class="space-y-4">

            {{-- ── Title block ─────────────────────────────────────────────── --}}
            <div>
                <p class="text-[15px] font-bold text-[#1D2836] leading-tight">
                    {{ $courseInfo['course_title'] ?? '—' }}
                </p>
                @if (!empty($courseInfo['program_title']))
                    <p class="text-[12px] text-[#93A1AF] mt-0.5">{{ $courseInfo['program_title'] }}</p>
                @endif
            </div>

            {{-- ── Key/value spec grid — one consistent size for every field ─── --}}
            <div class="rounded-2xl border border-[#E3E8EB] overflow-hidden">
                <div class="grid grid-cols-2 gap-px bg-[#E3E8EB]">

                    <div class="bg-white px-4 py-2.5 flex items-center justify-between gap-3">
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-[#93A1AF]">Course Code</span>
                        <span class="text-[13px] font-bold text-[#253540]">{{ $courseInfo['course_code'] ?? '—' }}</span>
                    </div>

                    <div class="bg-white px-4 py-2.5 flex items-center justify-between gap-3">
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-[#93A1AF]">Units</span>
                        <span class="text-[13px] font-bold text-[#253540]">{{ $courseInfo['credit_units'] ?? '—' }}</span>
                    </div>

                    <div class="bg-white px-4 py-2.5 flex items-center justify-between gap-3">
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-[#93A1AF]">LEC Hours</span>
                        <span class="text-[13px] font-bold text-[#253540]">{{ $courseInfo['lec_class_hours'] ?? '—' }}</span>
                    </div>

                    @if (!empty($courseInfo['has_lec_lab']))
                        <div class="bg-white px-4 py-2.5 flex items-center justify-between gap-3">
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-[#93A1AF]">LAB Hours</span>
                            <span class="text-[13px] font-bold text-[#253540]">{{ $courseInfo['lab_class_hours'] ?? '—' }}</span>
                        </div>
                    @endif

                </div>
            </div>

            {{-- ── Description ─────────────────────────────────────────────── --}}
            @if (!empty($courseInfo['description']))
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-[#93A1AF] mb-1.5">Description</p>
                    <p class="text-[13px] text-[#4F5D6B] leading-relaxed">{{ $courseInfo['description'] }}</p>
                </div>
            @endif

        </div>
    @else
        <x-feedback-status.empty-state icon="bx-book" title="No course data" message="Course information could not be loaded." />
    @endif

</x-layout.offcanvas>