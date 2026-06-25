{{-- evaluation-partials/notes.blade.php --}}

<div class="mt-4 rounded-xl border border-[#e2e8f0] bg-white overflow-hidden"
     style="box-shadow: 0 2px 12px rgba(0,0,0,.06);">

    <div class="h-1 w-full bg-gradient-to-r from-[#009639] via-emerald-400 to-[#86efac]"></div>

    <div class="p-5">
        <div class="flex items-center gap-2 mb-3">
            <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-[#dcfce7] text-[#16a34a]">
                <i class="bx bx-info-circle text-sm leading-none"></i>
            </span>
            <p class="text-xs font-bold uppercase tracking-widest text-[#475569]">Notes</p>
        </div>

        <div class="space-y-2 text-sm text-[#475569] leading-relaxed">

            <p>
                Rows are pulled from <strong class="text-[#0f172a]">Weekly Coverage</strong>.
                Only weeks with an assessment task appear.
                Week&nbsp;1 (MVGO) appears only when an assessment task is entered there.
                Greyed columns mean that component has no task for that week.
            </p>

            <p>
                <strong class="text-[#0f172a]">Exam course outcomes</strong> are auto-determined — the CO shown on each exam row
                is taken from the last covered week immediately before that exam.
                They cannot be edited manually.
            </p>

            @if ($courseHasLab)
                <p>
                    <strong class="text-[#0f172a]">Weight split</strong> is fixed:
                    <strong class="text-[#166534]">LEC 67%</strong>
                    +
                    <strong class="text-[#1e40af]">LAB 33%</strong>
                    = 100%.
                    The totals row turns <span class="text-rose-600 font-medium">red</span> if they don't match,
                    and <span class="text-[#16a34a] font-medium">green</span> when they do.
                </p>
            @else
                <p>
                    <strong class="text-[#0f172a]">Weight total</strong> must sum to
                    <strong class="text-[#166534]">100%</strong> for LEC.
                </p>
            @endif

            @php $lecMark = $lecPassingMark ?? 60; $labMark = $labPassingMark ?? 60; @endphp

            @if ($courseHasLab)
                <p>
                    <strong class="text-[#0f172a]">Passing mark</strong>:
                    <strong class="text-[#166534]">LEC {{ $lecMark }}%</strong>
                    /
                    <strong class="text-[#1e40af]">LAB {{ $labMark }}%</strong>
                    (set in Course Components → Performance Standard).
                </p>
            @else
                <p>
                    <strong class="text-[#0f172a]">Passing mark</strong>:
                    <strong class="text-[#166534]">{{ $lecMark }}%</strong>
                    (set in Course Components → Performance Standard).
                </p>
            @endif

        </div>
    </div>
</div>
