<x-modal.dialog id="viewCourseModal_{{ $course->id }}" maxWidth="max-w-3xl" width="w-11/12">
    <x-modal.header modalId="viewCourseModal_{{ $course->id }}">
        <div class="flex items-center gap-3 min-w-0">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-[#AEFFE2] text-[#00965F] shrink-0">
                <i class="bx bx-book text-base leading-none"></i>
            </span>
            <div class="min-w-0">
                <p class="text-[15px] font-bold text-[#1D2836] leading-tight truncate">
                    {{ $course->course_title }}
                </p>
                <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                    <span class="font-mono text-[11px] font-bold text-[#076042] bg-[#EDFFF8] border border-[#70FFCC] px-2 py-0.5 rounded-md shrink-0">
                        {{ $course->course_code }}
                    </span>
                    @if ($course->program)
                        <span class="text-[12px] text-[#93A1AF] truncate">{{ $course->program->name }}</span>
                    @endif
                </div>
            </div>
        </div>
    </x-modal.header>

    <x-modal.body>
        <div class="space-y-5">

            {{-- ── Info strip ─────────────────────────────────────────────── --}}
            <div class="rounded-xl border border-[#E3E8EB] overflow-hidden">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-px bg-[#E3E8EB]">

                    <div class="bg-white px-4 py-2.5 flex items-center justify-between gap-3">
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-[#93A1AF]">Units</span>
                        <span class="text-[13px] font-bold text-[#253540]">{{ $course->credit_units }}</span>
                    </div>

                    <div class="bg-white px-4 py-2.5 flex items-center justify-between gap-3">
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-[#93A1AF]">Type</span>
                        @if ($course->has_lec_lab)
                            <span class="text-[11px] font-semibold text-[#194C6E] bg-[#DAF1FF] px-2 py-0.5 rounded-md border border-[#AEDFFF]">LEC + LAB</span>
                        @else
                            <span class="text-[11px] font-semibold text-[#076042] bg-[#EDFFF8] px-2 py-0.5 rounded-md border border-[#AEFFE2]">Lecture</span>
                        @endif
                    </div>

                    @if ($course->year_level)
                        <div class="bg-white px-4 py-2.5 flex items-center justify-between gap-3">
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-[#93A1AF]">Year Level</span>
                            <span class="text-[13px] font-bold text-[#253540]">{{ $course->year_level }}</span>
                        </div>
                    @endif

                    @if ($course->semester)
                        <div class="bg-white px-4 py-2.5 flex items-center justify-between gap-3">
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-[#93A1AF]">Semester</span>
                            <span class="text-[13px] font-bold text-[#253540]">{{ $course->semester }}</span>
                        </div>
                    @endif

                    @if ($course->lec_class_hours)
                        <div class="bg-white px-4 py-2.5 flex items-center justify-between gap-3">
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-[#93A1AF]">LEC Hours</span>
                            <span class="text-[13px] font-semibold text-[#253540]">{{ $course->lec_class_hours }}</span>
                        </div>
                    @endif

                    @if ($course->has_lec_lab && $course->lab_class_hours)
                        <div class="bg-white px-4 py-2.5 flex items-center justify-between gap-3">
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-[#93A1AF]">LAB Hours</span>
                            <span class="text-[13px] font-semibold text-[#253540]">{{ $course->lab_class_hours }}</span>
                        </div>
                    @endif

                    @if ($course->passing_mark)
                        <div class="bg-white px-4 py-2.5 flex items-center justify-between gap-3">
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-[#93A1AF]">Passing Mark</span>
                            <span class="text-[13px] font-semibold text-[#253540]">{{ rtrim(rtrim(number_format($course->passing_mark, 2), '0'), '.') }}%</span>
                        </div>
                    @endif

                </div>
            </div>

            {{-- ── Pre / Co-req + Created by ─────────────────────────────── --}}
            @if ($course->prerequisite || $course->corequisite || $course->creator)
                <div class="flex flex-wrap items-center gap-2 pt-1">
                    @if ($course->prerequisite)
                        <span class="inline-flex items-center gap-1.5 text-[12px] px-2.5 py-1 rounded-lg
                                     bg-[#FFF6E2] border border-[#FFE9B5] text-[#875200]">
                            <i class="bx bx-git-branch text-[#F5B126] text-[11px]"></i>
                            <span class="font-semibold text-[#93A1AF]">Pre:</span>
                            <span class="font-mono font-bold">{{ $course->prerequisite }}</span>
                        </span>
                    @endif
                    @if ($course->corequisite)
                        <span class="inline-flex items-center gap-1.5 text-[12px] px-2.5 py-1 rounded-lg
                                     bg-[#DAF1FF] border border-[#AEDFFF] text-[#194C6E]">
                            <i class="bx bx-git-compare text-[#3197D6] text-[11px]"></i>
                            <span class="font-semibold text-[#93A1AF]">Co-req:</span>
                            <span class="font-mono font-bold">{{ $course->corequisite }}</span>
                        </span>
                    @endif
                    @if ($course->creator)
                        <span class="inline-flex items-center gap-1.5 text-[12px] px-2.5 py-1 rounded-lg
                                     bg-[#F9FAFA] border border-[#E3E8EB] text-[#4F5D6B] ml-auto">
                            <i class="bx bx-user text-[#93A1AF] text-[11px]"></i>
                            <span class="text-[#93A1AF]">by</span>
                            <span class="font-semibold">{{ $course->creator->name }}</span>
                        </span>
                    @endif
                </div>
            @endif

            {{-- ── Description ────────────────────────────────────────────── --}}
            @if ($course->course_description)
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-[#93A1AF] mb-1.5">
                        Description
                    </p>
                    <p class="text-[13px] text-[#4F5D6B] leading-relaxed">
                        {{ $course->course_description }}
                    </p>
                </div>
            @endif

            {{-- ── Program Outcomes Mapping ────────────────────────────────── --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-[#93A1AF]">
                        Program Outcomes Mapping
                    </p>
                    <span class="text-[10px] text-[#B4C0CA]">
                        I — Introductory · E — Enabling · D — Demonstrating
                    </span>
                </div>

                @if ($course->programOutcomes->isEmpty())
                    <div class="rounded-xl border border-dashed border-[#E3E8EB] px-4 py-8 text-center">
                        <i class="bx bx-notepad text-2xl text-[#D6DDE3] block mb-2"></i>
                        <p class="text-[13px] text-[#93A1AF]">No program outcomes mapped yet.</p>
                    </div>
                @else
                    <div class="rounded-xl border border-[#E3E8EB] overflow-hidden">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-[#F1F3F5] border-b border-[#E3E8EB]">
                                    <th class="px-4 py-2 text-left text-[10px] font-bold uppercase tracking-widest text-[#4F5D6B] w-16">PO</th>
                                    <th class="px-4 py-2 text-left text-[10px] font-bold uppercase tracking-widest text-[#4F5D6B]">Program Outcome</th>
                                    <th class="px-4 py-2 text-center text-[10px] font-bold uppercase tracking-widest text-[#4F5D6B] w-16">IED</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F1F3F5]">
                                @foreach ($course->programOutcomes as $outcome)
                                    <tr class="hover:bg-[#F9FAFA] transition-colors">
                                        <td class="px-4 py-2.5 font-mono font-bold text-xs text-[#394056]">
                                            {{ $outcome->po_code }}
                                        </td>
                                        <td class="px-4 py-2.5 text-[#4F5D6B] leading-relaxed text-[13px]">
                                            {{ $outcome->po_text }}
                                        </td>
                                        <td class="px-4 py-2.5 text-center">
                                            <x-feedback-status.ied-badge :level="$outcome->pivot->ied" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </x-modal.body>

    <x-modal.footer>
        <x-modal.close-button
            :modalId="'viewCourseModal_' . $course->id"
            text="Close" />
    </x-modal.footer>

</x-modal.dialog>
