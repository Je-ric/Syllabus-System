{{-- evaluation-partials/notes.blade.php --}}

<div class="mt-4 rounded-xl border border-[#e2e8f0] bg-white p-5" style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">
    <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-3">Notes</p>

    <div class="space-y-2 text-[13px] text-[#475569] leading-relaxed">

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
