{{--
    course-evaluation-partials/notes.blade.php
    ────────────────────────────────────────────
    Variables expected:
      $courseHasLab      — bool
      $lecPerformanceStd — string|null   e.g. '67%' or '100%'
      $labPerformanceStd — string|null   e.g. '33%'

    Displays the expected weight split based on what was saved in Course Components.
    If the standard is not yet set (null) it shows a sensible fallback message.
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

    {{-- ── Performance standard vs weight total --}}
    @if ($courseHasLab)
        @php
            $lecLabel = $lecPerformanceStd ?? '67%';
            $labLabel = $labPerformanceStd ?? '33%';
        @endphp
        <p class="mt-2 text-xs text-slate-600">
            Based on your Course Components settings, the expected weight split is:
            <strong class="text-emerald-700">LEC {{ $lecLabel }}</strong>
            +
            <strong class="text-blue-700">LAB {{ $labLabel }}</strong>
            = 100%.
            The totals row below your table will turn
            <span class="text-rose-600 font-medium">red</span> if the entered weights
            do not match these standards, and
            <span class="text-emerald-600 font-medium">green</span> when they match.
        </p>
    @else
        @php
            $lecLabel = $lecPerformanceStd ?? '100%';
        @endphp
        <p class="mt-2 text-xs text-slate-600">
            Based on your Course Components settings, the expected total LEC weight is
            <strong class="text-emerald-700">{{ $lecLabel }}</strong>.
            The totals row will turn
            <span class="text-rose-600 font-medium">red</span> if it does not match, and
            <span class="text-emerald-600 font-medium">green</span> when it matches.
        </p>
    @endif

    {{-- ── Passing standard --}}
    <p class="mt-2 text-xs text-slate-600">
        Minimum passing: <strong>60% per semester</strong> for every assessment task.
    </p>

</x-wizard.info-card>
