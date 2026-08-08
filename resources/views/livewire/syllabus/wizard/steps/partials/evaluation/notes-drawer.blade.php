{{-- evaluation-partials/notes-drawer.blade.php --}}

<x-layout.offcanvas
    title="Evaluation Notes"
    subtitle="Rules and reference for this step"
    icon="bx-info-circle"
    open="evalNotesOpen"
    width="max-w-sm">

    <div class="rounded-2xl border border-[#E3E8EB] divide-y divide-[#E3E8EB] overflow-hidden">

        {{-- Rows --}}
        <div class="flex gap-3 px-4 py-3.5">
            <span class="shrink-0 flex items-center justify-center w-8 h-8 rounded-[10px] bg-[#F9FAFA] text-[#93A1AF]">
                <i class="bx bx-table text-base leading-none"></i>
            </span>
            <div class="min-w-0">
                <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#93A1AF] mb-1">Rows</p>
                <p class="text-[13px] text-[#4F5D6B] leading-relaxed">
                    Rows are pulled from <strong class="text-[#1D2836]">Weekly Coverage</strong>.
                    Only weeks with an assessment task appear.
                    Week&nbsp;1 (MVGO) appears only when an assessment task is entered there.
                    Greyed columns mean that component has no task for that week.
                </p>
            </div>
        </div>

        {{-- Exam Course Outcomes --}}
        <div class="flex gap-3 px-4 py-3.5">
            <span class="shrink-0 flex items-center justify-center w-8 h-8 rounded-[10px] bg-[#F9FAFA] text-[#93A1AF]">
                <i class="bx bx-target-lock text-base leading-none"></i>
            </span>
            <div class="min-w-0">
                <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#93A1AF] mb-1">Exam Course Outcomes</p>
                <p class="text-[13px] text-[#4F5D6B] leading-relaxed">
                    <strong class="text-[#1D2836]">Exam course outcomes</strong> are auto-determined — the CO shown
                    on each exam row is taken from the last covered week immediately before that exam.
                    They cannot be edited manually.
                </p>
            </div>
        </div>

        {{-- Weight Total --}}
        <div class="flex gap-3 px-4 py-3.5">
            <span class="shrink-0 flex items-center justify-center w-8 h-8 rounded-[10px] bg-[#F9FAFA] text-[#93A1AF]">
                <i class="bx bx-bar-chart-alt-2 text-base leading-none"></i>
            </span>
            <div class="min-w-0">
                <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#93A1AF] mb-1">Weight Total</p>
                @if ($courseHasLab)
                    <p class="text-[13px] text-[#4F5D6B] leading-relaxed">
                        <strong class="text-[#1D2836]">Weight split</strong> is fixed:
                        <strong class="text-[#166534]">LEC {{ $lecStdNum }}%</strong>
                        +
                        <strong class="text-[#1e40af]">LAB {{ $labStdNum }}%</strong>
                        = 100%.
                        The totals row turns <span class="text-rose-600 font-medium">red</span> if they don't match,
                        and <span class="text-[#16a34a] font-medium">green</span> when they do.
                    </p>
                @else
                    <p class="text-[13px] text-[#4F5D6B] leading-relaxed">
                        <strong class="text-[#1D2836]">Weight total</strong> must sum to
                        <strong class="text-[#166534]">{{ $lecStdNum }}%</strong> for LEC.
                    </p>
                @endif
            </div>
        </div>

        {{-- Passing Mark --}}
        <div class="flex gap-3 px-4 py-3.5">
            <span class="shrink-0 flex items-center justify-center w-8 h-8 rounded-[10px] bg-[#F9FAFA] text-[#93A1AF]">
                <i class="bx bx-check-shield text-base leading-none"></i>
            </span>
            <div class="min-w-0">
                <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#93A1AF] mb-1">Passing Mark</p>
                @php $lecMark = $lecPassingMark; $labMark = $labPassingMark; @endphp
                @if ($courseHasLab)
                    <p class="text-[13px] text-[#4F5D6B] leading-relaxed">
                        <strong class="text-[#166534]">LEC {{ $lecMark }}%</strong>
                        /
                        <strong class="text-[#1e40af]">LAB {{ $labMark }}%</strong>
                        — set in Course Components → Performance Standard.
                    </p>
                @else
                    <p class="text-[13px] text-[#4F5D6B] leading-relaxed">
                        <strong class="text-[#166534]">{{ $lecMark }}%</strong>
                        — set in Course Components → Performance Standard.
                    </p>
                @endif
            </div>
        </div>

    </div>

</x-layout.offcanvas>