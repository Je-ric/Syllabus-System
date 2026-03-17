{{--
    course-evaluation-partials/notes.blade.php
    ────────────────────────────────────────────
    Variables expected:
      $courseHasLab      — bool
      $lecPerformanceStd — string|null  (raw DB decimal e.g. "60.00" or "75.00")
      $labPerformanceStd — string|null
      $lecPassingMark    — int   (parsed from lecPerformanceStd, e.g. 60 or 75)
      $labPassingMark    — int

    Clarification on terminology:
      • "Performance Standard" = the PASSING MARK (minimum score to pass).
        Raw scores are always out of 100. This threshold varies by subject.
      • LEC/LAB weight split is structural and fixed: LEC 67% + LAB 33% = 100%
        (or LEC 100% for LEC-only). This is NOT configurable.
--}}

<x-wizard.info-card title="Notes" icon="info-circle" color="slate" class="mt-4">

    {{-- ── Source of rows --}}
    <p class="text-xs text-slate-600">
        Rows are pulled from <strong>Weekly Coverage</strong>.
        Only weeks with an assessment task appear.
        Week&nbsp;1 (MVGO) appears only when an assessment task is entered there.
        Greyed columns mean that component has no task for that week.
    </p>

    {{-- ── Exam CO note --}}
    <p class="mt-2 text-xs text-slate-600">
        <strong>Exam course outcomes</strong> are auto-determined — the CO shown on each exam row
        is taken from the last covered week immediately before that exam.
        They cannot be edited manually.
    </p>

    {{-- ── Weight split note (structural, not configurable) --}}
    @if ($courseHasLab)
        <p class="mt-2 text-xs text-slate-600">
            <strong>Weight split</strong> is fixed:
            <strong class="text-emerald-700">LEC 67%</strong>
            +
            <strong class="text-blue-700">LAB 33%</strong>
            = 100%.
            The weight inputs in the table above must sum to these targets.
            The totals row turns <span class="text-rose-600 font-medium">red</span> if they don't match,
            and <span class="text-emerald-600 font-medium">green</span> when they do.
        </p>
    @else
        <p class="mt-2 text-xs text-slate-600">
            <strong>Weight total</strong> must sum to
            <strong class="text-emerald-700">100%</strong> for LEC.
        </p>
    @endif

    {{-- ── Passing mark (from performance_standard — varies by subject) --}}
    @php
        $lecMark = $lecPassingMark ?? 60;
        $labMark = $labPassingMark ?? 60;
    @endphp

    @if ($courseHasLab)
        <p class="mt-2 text-xs text-slate-600">
            <strong>Passing mark</strong> for this course:
            <strong class="text-emerald-700">LEC {{ $lecMark }}%</strong>
            /
            <strong class="text-blue-700">LAB {{ $labMark }}%</strong>
            (set in Course Components → Performance Standard).
            Students must meet or exceed this score to pass the assessment.
        </p>
    @else
        <p class="mt-2 text-xs text-slate-600">
            <strong>Passing mark</strong> for this course:
            <strong class="text-emerald-700">{{ $lecMark }}%</strong>
            (set in Course Components → Performance Standard).
            Students must meet or exceed this score to pass the assessment.
        </p>
    @endif

</x-wizard.info-card>