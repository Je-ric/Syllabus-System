{{-- evaluation-partials/notes-drawer.blade.php --}}

<x-layout.offcanvas
    title="Evaluation Notes"
    subtitle="Rules and reference for this step"
    icon="bx-info-circle"
    open="evalNotesOpen"
    width="max-w-sm">

    <div class="space-y-4 text-sm text-[#475569] leading-relaxed">

        <div class="space-y-1">
            <p class="text-[11px] font-bold uppercase tracking-widest text-[#a1a1aa]">Rows</p>
            <p>
                Rows are pulled from <strong class="text-[#0f172a]">Weekly Coverage</strong>.
                Only weeks with an assessment task appear.
                Week&nbsp;1 (MVGO) appears only when an assessment task is entered there.
                Greyed columns mean that component has no task for that week.
            </p>
        </div>

        <div class="space-y-1">
            <p class="text-[11px] font-bold uppercase tracking-widest text-[#a1a1aa]">Exam Course Outcomes</p>
            <p>
                <strong class="text-[#0f172a]">Exam course outcomes</strong> are auto-determined — the CO shown
                on each exam row is taken from the last covered week immediately before that exam.
                They cannot be edited manually.
            </p>
        </div>

        <div class="space-y-1">
            <p class="text-[11px] font-bold uppercase tracking-widest text-[#a1a1aa]">Weight Total</p>
            @if ($courseHasLab)
                <p>
                    <strong class="text-[#0f172a]">Weight split</strong> is fixed:
                    <strong class="text-[#166534]">LEC {{ $lecStdNum }}%</strong>
                    +
                    <strong class="text-[#1e40af]">LAB {{ $labStdNum }}%</strong>
                    = 100%.
                    The totals row turns <span class="text-rose-600 font-medium">red</span> if they don't match,
                    and <span class="text-[#16a34a] font-medium">green</span> when they do.
                </p>
            @else
                <p>
                    <strong class="text-[#0f172a]">Weight total</strong> must sum to
                    <strong class="text-[#166534]">{{ $lecStdNum }}%</strong> for LEC.
                </p>
            @endif
        </div>

        <div class="space-y-1">
            <p class="text-[11px] font-bold uppercase tracking-widest text-[#a1a1aa]">Passing Mark</p>
            @php $lecMark = $lecPassingMark; $labMark = $labPassingMark; @endphp
            @if ($courseHasLab)
                <p>
                    <strong class="text-[#166534]">LEC {{ $lecMark }}%</strong>
                    /
                    <strong class="text-[#1e40af]">LAB {{ $labMark }}%</strong>
                    — set in Course Components → Performance Standard.
                </p>
            @else
                <p>
                    <strong class="text-[#166534]">{{ $lecMark }}%</strong>
                    — set in Course Components → Performance Standard.
                </p>
            @endif
        </div>

    </div>

</x-layout.offcanvas>
