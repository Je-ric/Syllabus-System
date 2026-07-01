<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @if (!empty($isSnapshot) && !empty($inlinePreviewCss))
        <style>
            {!! $inlinePreviewCss !!}
        </style>
    @else
        @vite(['resources/css/preview.css', 'resources/js/app.js'])
    @endif
    <title>Abridged Course Syllabus - {{ $syllabus->course->course_code }}</title>
</head>

<body>

    <div id="toolbar">
        <div class="t-left">
            <span class="t-title">
                {{ $syllabus->course->course_code }} – {{ $syllabus->course->course_title }}
            </span>
            <span class="t-pages" id="page-count"></span>
        </div>

        @if (empty($isSnapshot))
            <div class="t-center">
                <button type="button"
                    onclick="window.location.href='{{ route('syllabus.preview.complete', $syllabus) }}'">
                    Complete
                </button>
                <button type="button" class="is-active"
                    onclick="window.location.href='{{ route('syllabus.preview.abridged', $syllabus) }}'">
                    Abridged
                </button>
                <button type="button"
                    onclick="window.location.href='{{ route('syllabus.preview.assessment', $syllabus) }}'">
                    Assessment Plan
                </button>
            </div>

            <div class="t-right">
                <button type="button" onclick="openSyllabusVersions()">Versions</button>
                <button type="button"
                    onclick="window.location.href='{{ route('syllabus.wizard', ['syllabusId' => $syllabus->id]) }}'">
                    Edit
                </button>
                <button type="button" onclick="window.print()">Print</button>
            </div>
        @else
            <button type="button" onclick="window.print()">Print</button>
        @endif
    </div>

    <div id="syllabus-content" style="display:none;">

        {{-- ── Letterhead ───────────────────────────────────────────────────── --}}
        <div style="display:grid; grid-template-columns: 80px 1fr 80px; align-items:center; column-gap: 12px;">
            <div style="display:flex; justify-content:flex-start;">
                <img src="{{ !empty($isSnapshot) && !empty($inlineLogoDataUri) ? $inlineLogoDataUri : asset('assets/clsu-logo-green.png') }}"
                    alt="CLSU Logo" style="width:100px; height:auto;" />
            </div>
            <div style="text-align:center;">
                <div class="a4-subtitle">Republic of the Philippines</div>
                <div class="a4-title">CENTRAL LUZON STATE UNIVERSITY</div>
                <div class="a4-subtitle">Science City of Muñoz, Nueva Ecija</div>
                <br>
                <div class="a4-subtitle">OFFICE OF THE VICE PRESIDENT FOR ACADEMIC AFFAIRS</div>
            </div>
            <div aria-hidden="true"></div>
        </div>

        <div class="a4-section a4-title">ABRIDGED COURSE SYLLABUS</div>
        <br>
        <div class="a4-subtitle a4-title">{{ $syllabus->course->course_code }} - {{ $syllabus->course->course_title }}
        </div>

        <div class="a4-section"
            style="font-style:italic; font-size:9pt; color:#444; line-height:1.5; border:1px solid #ccc; padding:8px 12px; margin-top:10px;">
            This resource is designed for students' use and serves as a supplementary guide. However, it is not
            intended to replace or substitute the official OBTL (Outcome-Based Teaching and Learning) syllabus
            format provided by the university. Students should refer to the official OBTL syllabus for
            comprehensive and accurate information regarding their courses and academic requirements.
        </div>

        {{-- ═══════════════════════════════════════════════════════════════════
             SECTION I — COURSE AND INSTRUCTOR INFORMATION
        ════════════════════════════════════════════════════════════════════ --}}
        <h3 class="a4-section title-lettered">I. Course and Instructor Information</h3>

        @php
            $hasLab = (bool) $syllabus->course?->has_lec_lab;

            $lecLabValue = function ($lecValue, $labValue) use ($hasLab) {
                if (!$hasLab) {
                    return !blank($lecValue) ? e($lecValue) : 'N/A';
                }
                $lines = [];
                if (!blank($lecValue)) {
                    $lines[] = 'LEC: ' . e($lecValue);
                }
                if (!blank($labValue)) {
                    $lines[] = 'LAB: ' . e($labValue);
                }
                return count($lines) ? implode('<br>', $lines) : 'N/A';
            };
        @endphp

        <div class="a4-section">
            <div class="table-indent">
                <table class="abridged-table">
                    <tbody>
                        <tr>
                            <td>Course Code</td>
                            <td>{{ $syllabus->course->course_code }}</td>
                        </tr>
                        <tr>
                            <td>Course Title</td>
                            <td>{{ $syllabus->course->course_title ?? '' }}</td>
                        </tr>
                        <tr>
                            <td>Course Description</td>
                            <td>{{ $syllabus->course->course_description ?? '' }}</td>
                        </tr>
                        <tr>
                            <td>Prerequisite</td>
                            <td>{{ $syllabus->course->prerequisite ?? 'None' }}</td>
                        </tr>
                        <tr>
                            <td>Co-requisite</td>
                            <td>{{ $syllabus->course->corequisite ?? 'None' }}</td>
                        </tr>
                        <tr>
                            <td>Credit Units</td>
                            <td>{{ $syllabus->course->credit_units }}</td>
                        </tr>
                        <tr>
                            <td>Class Hours</td>
                            <td>{!! $lecLabValue($lecComponent?->class_hours, $labComponent?->class_hours) !!}</td>
                        </tr>
                        <tr>
                            <td>Class Schedule</td>
                            <td>
                                {{-- {!! $lecLabValue($lecComponent?->schedule, $labComponent?->schedule) !!} --}}
 @php
                                    $lecSched = $lecComponent?->schedules ?? collect();
                                    $labSched = $labComponent?->schedules ?? collect();
                                @endphp
                                @if (!$hasLab)
                                    @forelse ($lecSched as $s)
                                        {{ $s->day }}: {{ $s->time }}<br>
                                    @empty
                                        N/A
                                    @endforelse
                                @else
                                    @if ($lecSched->isNotEmpty())
                                        LEC: <br>
                                        @foreach ($lecSched as $s){{ $s->day }}: {{ $s->time }}<br>@endforeach
                                    @endif
                                    @if ($labSched->isNotEmpty())
                                        LAB: <br>
                                        @foreach ($labSched as $s){{ $s->day }}: {{ $s->time }}<br>@endforeach
                                    @endif
                                    @if ($lecSched->isEmpty() && $labSched->isEmpty()) @endif
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Name of Instructor</td>
                            <td>{!! $lecLabValue($lecComponent?->instructor_name, $labComponent?->instructor_name) !!}</td>
                        </tr>
                        <tr>
                            <td>Office</td>
                            <td>{!! $lecLabValue($lecComponent?->office, $labComponent?->office) !!}</td>
                        </tr>
                        <tr>
                            <td>Email Address</td>
                            <td>{!! $lecLabValue($lecComponent?->instructor_email, $labComponent?->instructor_email) !!}</td>
                        </tr>
                        <tr>
                            <td>Consultation Hours</td>
                            <td>
                                @php
                                    $lecHours = $preparerConsultationHours ?? collect();
                                    $labInstructor = $labComponent ? \App\Models\User::where('email', $labComponent->instructor_email)->with('consultationHours')->first() : null;
                                    $labHours = $labInstructor?->consultationHours ?? collect();
                                @endphp
                                @if (!$hasLab)
                                    @if ($lecHours->isEmpty())
                                        N/A
                                    @else
                                        @foreach ($lecHours as $ch){{ $ch->day }}: {{ $ch->time }}<br>@endforeach
                                    @endif
                                @else
                                    @if ($lecHours->isNotEmpty())
                                        LEC<br>
                                        @foreach ($lecHours as $ch){{ $ch->day }}: {{ $ch->time }}<br>@endforeach
                                    @endif
                                    @if ($labHours->isNotEmpty())
                                        LAB<br>
                                        @foreach ($labHours as $ch){{ $ch->day }}: {{ $ch->time }}<br>@endforeach
                                    @endif
                                    @if ($lecHours->isEmpty() && $labHours->isEmpty()) @endif
                                @endif
                                {{-- {!! $lecLabValue($lecComponent?->consultation_hours, $labComponent?->consultation_hours) !!} --}}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════════
             SECTION II — COURSE OUTCOMES
        ════════════════════════════════════════════════════════════════════ --}}
        <h3 class="a4-section title-lettered">II. Course Outcomes</h3>

        <div class="a4-section">
            <div class="table-indent">
                <table>
                    <thead>
                        <tr>
                            <th style="text-align:center; width:60px;">CO No.</th>
                            <th style="text-align:center;">Course Outcomes</th>
                            <th style="text-align:center; width:200px;">
                                Program Outcomes Addressed
                                <br>
                                <span style="font-weight:normal; font-style:italic; font-size:8.5pt;">
                                    (use only the letter code)
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($courseOutcomes as $co)
                            <tr>
                                <td style="text-align:center;">
                                    {{ preg_replace('/^[A-Za-z]+/', '', $co->co_code) }}
                                </td>
                                <td>{{ $co->description }}</td>
                                <td style="text-align:center;">{{ $coPoLetterMap[$co->id] ?? '' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align:center;">No course outcomes found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════════
             SECTION III — COURSE CALENDAR
        ════════════════════════════════════════════════════════════════════ --}}
        <h3 class="a4-section title-lettered">III. Course Calendar</h3>

        @php
            $lecRows = $abridgedWeeklyRows['LEC'] ?? [];
            $labRows = $abridgedWeeklyRows['LAB'] ?? [];

            $toMultilineCell = function ($value) {
                $text = trim((string) ($value ?? ''));
                if ($text === '') {
                    return '';
                }
                $text = str_replace(["\r\n", "\r"], "\n", $text);
                $text = str_replace(['•', '●', '▪'], "\n", $text);
                $text = preg_replace('/\s*[;|]\s*/', "\n", $text) ?? $text;
                $text = preg_replace('/\s*,\s*/', "\n", $text) ?? $text;
                $text = preg_replace('/\n{2,}/', "\n", $text) ?? $text;
                return trim($text);
            };

            // Helper to compute CO rowspans for weekly rows
            $computeCoRowSpans = function ($rows) {
                $coRowSpans = [];
                $isCoGroupStart = [];
                $previousCo = null;
                $groupStartIndex = null;

                foreach ($rows as $index => $row) {
                    $currentCo = (string) ($row['co_no'] ?? '');
                    if ($index === 0 || $currentCo !== $previousCo) {
                        if ($groupStartIndex !== null) {
                            $coRowSpans[$groupStartIndex] = $index - $groupStartIndex;
                        }
                        $groupStartIndex = $index;
                        $isCoGroupStart[$index] = true;
                    } else {
                        $isCoGroupStart[$index] = false;
                    }
                    $previousCo = $currentCo;
                }
                if ($groupStartIndex !== null) {
                    $coRowSpans[$groupStartIndex] = count($rows) - $groupStartIndex;
                }

                return compact('coRowSpans', 'isCoGroupStart');
            };

            $lecGrouping = $computeCoRowSpans($lecRows);
            $lecCoRowSpans = $lecGrouping['coRowSpans'];
            $lecIsCoGroupStart = $lecGrouping['isCoGroupStart'];

            $labGrouping = $computeCoRowSpans($labRows);
            $labCoRowSpans = $labGrouping['coRowSpans'];
            $labIsCoGroupStart = $labGrouping['isCoGroupStart'];
        @endphp

        <div class="a4-section">
            @if ($syllabus->course->has_lec_lab)
                <strong class="indent-level-1 title-numbered">Lecture (LEC)</strong>
            @endif
            <table class="weekly-coverage-table">
                <thead>
                    <tr>
                        <th style="text-align:center; width:60px;">CO No.</th>
                        <th style="text-align:center; width:70px;">Wk No.</th>
                        <th style="text-align:center;">Topics</th>
                        <th style="text-align:center;">Learning Activities</th>
                        <th style="text-align:center; width:130px;">Assessment</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($lecRows as $index => $row)
                        @if ($row['is_exam'])
                            <tr>
                                @if ($lecIsCoGroupStart[$index] ?? false)
                                    <td rowspan="{{ $lecCoRowSpans[$index] ?? 1 }}"
                                        style="text-align:center; background-color:#d9d9d9; vertical-align:middle;">
                                        {{ $row['co_no'] ?? ($row['co_label'] ?? '-') }}</td>
                                @endif
                                <td style="text-align:center; background-color:#d9d9d9;">
                                    {{ $row['wk_label'] ?? ($row['week_label'] ?? ($row['week_no'] ?? '')) }}</td>
                                <td colspan="3"
                                    style="text-align:center; font-weight:bold; background-color:#d9d9d9; font-style:italic; vertical-align:middle;">
                                    {{ $row['exam_label'] ?? 'Exam' }}
                                </td>
                            </tr>
                        @else
                            <tr>
                                @if ($lecIsCoGroupStart[$index] ?? false)
                                    <td rowspan="{{ $lecCoRowSpans[$index] ?? 1 }}"
                                        style="text-align:center; vertical-align:middle;">
                                        {{ blank($row['co_no'] ?? null) ? '---' : $row['co_no'] }}</td>
                                @endif
                                <td style="text-align:center; vertical-align:top;">
                                    {{ $row['wk_label'] ?? ($row['week_label'] ?? ($row['week_no'] ?? '')) }}</td>
                                <td style="vertical-align:top;">{!! nl2br(strip_tags(e($toMultilineCell($row['topics'] ?? '')) !== '' ? $toMultilineCell($row['topics'] ?? '') : '---')) !!}</td>
                                <td style="vertical-align:top;">{!! nl2br(strip_tags(e($toMultilineCell($row['tla'] ?? '')) !== '' ? $toMultilineCell($row['tla'] ?? '') : '---')) !!}</td>
                                <td style="vertical-align:top;">{!! nl2br(strip_tags(e($toMultilineCell($row['assessment'] ?? '')) !== '' ? $toMultilineCell($row['assessment'] ?? '') : '---'),) !!}</td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center;">No weekly coverage found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($syllabus->course->has_lec_lab)
                <strong class="indent-level-1 title-numbered" style="display:block; margin-top:10px;">Laboratory
                    (LAB)</strong>
                <table class="weekly-coverage-table">
                    <thead>
                        <tr>
                            <th style="text-align:center; width:60px;">CO No.</th>
                            <th style="text-align:center; width:70px;">Wk No.</th>
                            <th style="text-align:center;">Topics</th>
                            <th style="text-align:center;">Learning Activities</th>
                            <th style="text-align:center; width:130px;">Assessment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($labRows as $index => $row)
                            @if ($row['is_exam'])
                                <tr>
                                    @if ($labIsCoGroupStart[$index] ?? false)
                                        <td rowspan="{{ $labCoRowSpans[$index] ?? 1 }}"
                                            style="text-align:center; background-color:#d9d9d9; vertical-align:middle;">
                                            {{ $row['co_no'] ?? ($row['co_label'] ?? '-') }}</td>
                                    @endif
                                    <td style="text-align:center; background-color:#d9d9d9;">
                                        {{ $row['wk_label'] ?? ($row['week_label'] ?? ($row['week_no'] ?? '')) }}</td>
                                    <td colspan="3"
                                        style="text-align:center; font-weight:bold; background-color:#d9d9d9; font-style:italic; vertical-align:middle;">
                                        {{ $row['exam_label'] ?? 'Exam' }}
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    @if ($labIsCoGroupStart[$index] ?? false)
                                        <td rowspan="{{ $labCoRowSpans[$index] ?? 1 }}"
                                            style="text-align:center; vertical-align:middle;">
                                            {{ blank($row['co_no'] ?? null) ? '---' : $row['co_no'] }}</td>
                                    @endif
                                    <td style="text-align:center; vertical-align:top;">
                                        {{ $row['wk_label'] ?? ($row['week_label'] ?? ($row['week_no'] ?? '')) }}</td>
                                    <td style="vertical-align:top;">{!! nl2br(e($toMultilineCell($row['topics'] ?? '') !== '' ? $toMultilineCell($row['topics'] ?? '') : '---')) !!}</td>
                                    <td style="vertical-align:top;">{!! nl2br(e($toMultilineCell($row['tla'] ?? '') !== '' ? $toMultilineCell($row['tla'] ?? '') : '---')) !!}</td>
                                    <td style="vertical-align:top;">{!! nl2br(
                                        e($toMultilineCell($row['assessment'] ?? '') !== '' ? $toMultilineCell($row['assessment'] ?? '') : '---'),
                                    ) !!}</td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="5" style="text-align:center;">No weekly coverage found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @endif
        </div>

        {{-- ═══════════════════════════════════════════════════════════════════
             SECTION IV — COURSE EVALUATION
        ════════════════════════════════════════════════════════════════════ --}}
        <h3 class="a4-section title-lettered">IV. Course Evaluation</h3>

        <div class="a4-section">
            <p class="indent-level-1"><strong>a. Course Requirements</strong></p>
            <p class="indent-level-2">The student performance in this course will be rated based on the following:</p>

            <div class="table-indent">
                <table class="evaluation-table">
                    <thead>
                        <tr>
                            <th rowspan="2" style="text-align:center; white-space:normal;">Course Outcomes</th>
                            <th colspan="2" style="text-align:center;">
                                {{ $syllabus->course->has_lec_lab ? 'LECTURE (67%)' : 'LECTURE' }}
                            </th>
                            @if ($syllabus->course->has_lec_lab)
                                <th colspan="2" style="text-align:center;">LABORATORY (33%)</th>
                            @endif
                            <th rowspan="2" style="text-align:center;">Performance Standard</th>
                        </tr>
                        <tr>
                            <th style="text-align:center; white-space:nowrap;">Assessment Task</th>
                            <th style="text-align:center; white-space:nowrap;">Weight (%)</th>
                            @if ($syllabus->course->has_lec_lab)
                                <th style="text-align:center; white-space:nowrap;">Assessment Task</th>
                                <th style="text-align:center; white-space:nowrap;">Weight (%)</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $lecPassMark = $lecComponent?->performance_standard
                                ? (int) round(
                                    (float) str_replace('%', '', (string) $lecComponent->performance_standard),
                                )
                                : 60;
                            $labPassMark = $labComponent?->performance_standard
                                ? (int) round(
                                    (float) str_replace('%', '', (string) $labComponent->performance_standard),
                                )
                                : 60;

                            $coRowSpans = [];
                            $isCoGroupStart = [];
                            $previousCo = null;
                            $groupStartIndex = null;

                            foreach ($evaluationRows as $index => $evaluationRow) {
                                $currentCo = (string) ($evaluationRow['co_label'] ?? '');
                                if ($index === 0 || $currentCo !== $previousCo) {
                                    if ($groupStartIndex !== null) {
                                        $coRowSpans[$groupStartIndex] = $index - $groupStartIndex;
                                    }
                                    $groupStartIndex = $index;
                                    $isCoGroupStart[$index] = true;
                                } else {
                                    $isCoGroupStart[$index] = false;
                                }
                                $previousCo = $currentCo;
                            }
                            if ($groupStartIndex !== null) {
                                $coRowSpans[$groupStartIndex] = count($evaluationRows) - $groupStartIndex;
                            }

                            $termRowSpans = [];
                            $isTermStart = [];
                            $termStartIndex = null;

                            foreach ($evaluationRows ?? [] as $index => $evaluationRow) {
                                if ($termStartIndex === null) {
                                    $termStartIndex = $index;
                                    $isTermStart[$index] = true;
                                } else {
                                    $isTermStart[$index] = false;
                                }
                                if (($evaluationRow['is_exam'] ?? false) === true) {
                                    $termRowSpans[$termStartIndex] = $index - $termStartIndex + 1;
                                    $termStartIndex = null;
                                }
                            }
                            if ($termStartIndex !== null) {
                                $termStart = (int) $termStartIndex;
                                $termRowSpans[$termStart] = count($evaluationRows ?? []) - $termStart;
                            }
                        @endphp

                        @forelse ($evaluationRows as $index => $row)
                            <tr>
                                @if ($isCoGroupStart[$index] ?? false)
                                    <td rowspan="{{ $coRowSpans[$index] ?? 1 }}"
                                        style="text-align:center; vertical-align:middle;">
                                        {{ $row['co_label'] ?? '' }}
                                    </td>
                                @endif
                                <td>{{ $row['lec_task'] ?? '' }}</td>
                                <td style="text-align:center;">{{ $row['lec_weight'] ?? '' }}</td>
                                @if ($syllabus->course->has_lec_lab)
                                    <td>{{ $row['lab_task'] ?? '' }}</td>
                                    <td style="text-align:center;">{{ $row['lab_weight'] ?? '' }}</td>
                                @endif
                                @if ($isTermStart[$index] ?? false)
                                    <td rowspan="{{ $termRowSpans[$index] ?? 1 }}"
                                        style="text-align:center; vertical-align:middle;">
                                        <strong>{{ $lecPassMark }}%</strong>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $syllabus->course->has_lec_lab ? 6 : 4 }}"
                                    style="text-align:center;">No evaluation items found.</td>
                            </tr>
                        @endforelse

                        <tr>
                            <td style="text-align:left; font-weight:bold;">Total</td>
                            <td></td>
                            <td style="text-align:center; font-weight:bold;">{{ $evaluationTotals['lec'] ?? '' }}%
                            </td>
                            @if ($syllabus->course->has_lec_lab)
                                <td></td>
                                <td style="text-align:center; font-weight:bold;">{{ $evaluationTotals['lab'] ?? '' }}%
                                </td>
                            @endif
                            <td style="text-align:center; font-weight:bold;">{{ $lecPassMark }}%</td>
                        </tr>
                        <tr>
                            <td colspan="{{ $syllabus->course->has_lec_lab ? 5 : 3 }}">
                                <b>Minimum Average for Satisfactory Performance</b>
                            </td>
                            <td style="text-align:center;"><b>{{ $lecPassMark }}%</b></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="indent-level-1">
                <p style="margin-top:14px;"><strong>b. Computation of Final Course Average Score (FCAS)</strong></p>
                <br>
                @if ($syllabus->course->has_lec_lab)
                    <p><strong>FCAS = (0.67) × LecAve + (0.33) × LabAve + APP</strong></p>
                    <br>
                    <table class="fcas">
                        <tr>
                            <td style="width:120px;">Where:</td>
                            <td>
                                FCAS &nbsp;= Final Course Average Score <br>
                                LecAve = Lecture Average Score <br>
                                LabAve = Laboratory Average Score <br>
                                APP &nbsp;&nbsp;&nbsp;= Additional point incentive for student athletes, performers
                                and student delegates/representatives [CLSU BOR Resolution No. 32-09]
                            </td>
                        </tr>
                    </table>
                @else
                    <p><strong>FCAS = LecAve + APP</strong></p>
                    <br>
                    <table class="fcas">
                        <tr>
                            <td style="width:120px;">Where:</td>
                            <td>
                                FCAS = Final Course Average Score <br>
                                LecAve = Lecture Average Score <br>
                                APP = Additional point incentive for student athletes, performers
                                and student delegates/representatives [CLSU BOR Resolution No. 32-09]
                            </td>
                        </tr>
                    </table>
                @endif

                <p style="margin-top:14px;"><strong>c. Transmutation</strong></p>
                <p class="indent-level-2">The final grades will correspond to the weighted average scores shown below:
                </p>
            </div>

            <div class="table-indent">
                <table>
                    <thead>
                        <tr>
                            <th style="text-align:center;">Average Score</th>
                            <th style="text-align:center;">Grade</th>
                            <th style="text-align:center;">Average Score</th>
                            <th style="text-align:center;">Grade</th>
                            <th style="text-align:center;">Average Score</th>
                            <th style="text-align:center;">Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="text-align:center;">95.60 – 100.00</td>
                            <td style="text-align:center;">1.00</td>
                            <td style="text-align:center;">77.80 – 82.24</td>
                            <td style="text-align:center;">2.00</td>
                            <td style="text-align:center;">60.00 – 64.44</td>
                            <td style="text-align:center;">3.00</td>
                        </tr>
                        <tr>
                            <td style="text-align:center;">91.15 – 95.59</td>
                            <td style="text-align:center;">1.25</td>
                            <td style="text-align:center;">73.35 – 77.79</td>
                            <td style="text-align:center;">2.25</td>
                            <td style="text-align:center;">Below 60</td>
                            <td style="text-align:center;">5.00</td>
                        </tr>
                        <tr>
                            <td style="text-align:center;">86.70 – 91.14</td>
                            <td style="text-align:center;">1.50</td>
                            <td style="text-align:center;">68.90 – 73.34</td>
                            <td style="text-align:center;">2.50</td>
                            <td style="text-align:center;"></td>
                            <td style="text-align:center;"></td>
                        </tr>
                        <tr>
                            <td style="text-align:center;">82.25 – 86.69</td>
                            <td style="text-align:center;">1.75</td>
                            <td style="text-align:center;">64.45 – 68.89</td>
                            <td style="text-align:center;">2.75</td>
                            <td style="text-align:center;"></td>
                            <td style="text-align:center;"></td>
                        </tr>
                    </tbody>
                </table>
                <p style="margin-top:4px;" class="indent-level-1">Passing Mark: {{ $lecPassMark }}%</p>
            </div>
        </div>
        {{-- @php
                /**
                 * Build transmutation display rows for the given passing mark.
                 *
                 * All 9 standard grade bands are anchored to the passing mark.
                 * Bands whose lower bound exceeds 100 are dropped (score can't exceed 100).
                * The remaining valid bands are placed into the 4-row × 3-col-pair table
                * layout left-to-right, top-to-bottom; empty slots get blank cells.
                *
                * The 5.00 / "Below X" row is appended right after the last valid band
                * in column 3 (or wherever it falls in the sequence).
                */
                $buildTransmutation = function (int $pass): array {
                    $fmt = fn(float $n): string => number_format($n, 2);

                    // All 9 bands, highest grade first.
                    $allBands = [
                        ['grade' => '1.00', 'lo' => $pass + 35.6, 'hi' => 100.0],
                        ['grade' => '1.25', 'lo' => $pass + 31.15, 'hi' => $pass + 35.59],
                        ['grade' => '1.50', 'lo' => $pass + 26.7, 'hi' => $pass + 31.14],
                        ['grade' => '1.75', 'lo' => $pass + 22.25, 'hi' => $pass + 26.69],
                        ['grade' => '2.00', 'lo' => $pass + 17.8, 'hi' => $pass + 22.24],
                        ['grade' => '2.25', 'lo' => $pass + 13.35, 'hi' => $pass + 17.79],
                        ['grade' => '2.50', 'lo' => $pass + 8.9, 'hi' => $pass + 13.34],
                        ['grade' => '2.75', 'lo' => $pass + 4.45, 'hi' => $pass + 8.89],
                        ['grade' => '3.00', 'lo' => $pass + 0.0, 'hi' => $pass + 4.44],
                    ];

                    // Keep only bands whose lo ≤ 100; clamp hi to 100.
                    $valid = [];
                    foreach ($allBands as $b) {
                        if ($b['lo'] <= 100) {
                            $valid[] = [
                                'grade' => $b['grade'],
                                'range' => $fmt($b['lo']) . ' – ' . $fmt(min($b['hi'], 100.0)),
                            ];
                        }
                    }

                    // Append the "Below pass / 5.00" entry.
                    $valid[] = ['grade' => '5.00', 'range' => 'Below ' . $pass];

                    // Pad to exactly 12 slots (4 rows × 3 col-pairs).
                    while (count($valid) < 12) {
                        $valid[] = ['grade' => '', 'range' => ''];
                    }

                    // Slice into three columns of 4 rows each.
                    return [
                        array_slice($valid, 0, 4), // col 1
                        array_slice($valid, 4, 4), // col 2
                        array_slice($valid, 8, 4), // col 3
                    ];
                };

                [$tCol1, $tCol2, $tCol3] = $buildTransmutation($lecPassMark);
            @endphp

            <div class="table-indent">
                <table>
                    <thead>
                        <tr>
                            <th style="text-align:center;">Average Score</th>
                            <th style="text-align:center;">Grade</th>
                            <th style="text-align:center;">Average Score</th>
                            <th style="text-align:center;">Grade</th>
                            <th style="text-align:center;">Average Score</th>
                            <th style="text-align:center;">Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 0; $i < 4; $i++)
                            <tr>
                                <td style="text-align:center;">{{ $tCol1[$i]['range'] }}</td>
                                <td style="text-align:center;">{{ $tCol1[$i]['grade'] }}</td>
                                <td style="text-align:center;">{{ $tCol2[$i]['range'] }}</td>
                                <td style="text-align:center;">{{ $tCol2[$i]['grade'] }}</td>
                                <td style="text-align:center;">{{ $tCol3[$i]['range'] }}</td>
                                <td style="text-align:center;">{{ $tCol3[$i]['grade'] }}</td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
                <p style="margin-top: 4px;" class="indent-level-1">Passing Mark: {{ $lecPassMark }}%</p>
            </div> --}}

        {{-- ═══════════════════════════════════════════════════════════════════
             SECTION V — REQUIRED READING MATERIALS
        ════════════════════════════════════════════════════════════════════ --}}
        <h3 class="a4-section title-lettered">V. Required Reading Materials</h3>

        <div class="a4-section">
            <div class="a4-list">
                @if ($allReferences->isNotEmpty())
                    <strong class="indent-level-1-5 title-numbered">Textbooks / eBooks</strong>
                    @foreach ($allReferences as $ref)
                        <div class="indent-level-1-5">{{ $ref }}</div>
                    @endforeach
                @else
                    <div class="indent-level-1-5" style="font-style:italic; color:#666;">No references encoded.</div>
                @endif

                <div class="indent-level-1-5" style="margin-top:8px; font-weight:bold;">
                    <strong>Online materials:</strong>
                </div>

                @if ($onlineMaterialLinks->isNotEmpty())
                    @foreach ($onlineMaterialLinks as $url)
                        @php
                            $link = \Illuminate\Support\Str::startsWith($url, ['http://', 'https://'])
                                ? $url
                                : 'https://' . $url;
                        @endphp
                        <div class="indent-level-1-5">
                            <a href="{{ $link }}" target="_blank"
                                style="text-decoration:underline; font-weight:bold;">{{ $url }}</a>
                        </div>
                    @endforeach
                @else
                    <div class="indent-level-1-5">No online materials encoded.</div>
                @endif
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════════
             PREPARED BY
        ════════════════════════════════════════════════════════════════════ --}}
        <div class="a4-section">
            <p style="margin-top:50px;"><strong>Prepared by:</strong></p>

            @php
                $preparedByName =
                    collect($syllabus->components ?? [])
                        ->pluck('instructor_name')
                        ->filter()
                        ->unique()
                        ->implode(' / ') ?:
                    $lecComponent?->instructor_name ?? 'N/A';
            @endphp

            <p style="display:inline-block; padding-top:4px; min-width:220px;">
                <span style="border-bottom:1px solid #000; display:inline-block; padding-bottom:2px;">
                    {{ $preparedByName }}
                </span>
                <br>
                <span style="font-size:8.5pt; color:#444;">
                    Course Instructor / Professor
                </span>
            </p>
        </div>

    </div>{{-- /#syllabus-content --}}

    <div id="a4-container"></div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const source = document.getElementById("syllabus-content");
            const container = document.getElementById("a4-container");
            const pageCountEl = document.getElementById("page-count");

            let page = makePage();
            container.appendChild(page);

            let _uid = 0;

            function uid(node) {
                if (!node.__sid) node.__sid = ++_uid;
                return node.__sid;
            }

            Array.from(source.children).forEach(el => {
                if (el.tagName === "TABLE") {
                    splitTable(el, () => page);
                } else {
                    flow(el, []);
                }
            });

            source.remove();
            addPageNumbers();

            function flow(node, chain) {
                if (node.nodeType === Node.ELEMENT_NODE && node.tagName === "TABLE") {
                    splitTable(node, () => deepest(chain));
                    return;
                }

                const target = deepest(chain);
                target.appendChild(node);

                if (!overflows()) return;

                target.removeChild(node);

                const kids = (node.nodeType === Node.ELEMENT_NODE) ?
                    Array.from(node.childNodes) : [];

                if (kids.length === 0) {
                    breakPage();
                    deepest(chain).appendChild(node);
                    return;
                }

                uid(node);
                const clone = shallow(node);
                clone.__sid = node.__sid;
                target.appendChild(clone);

                const deeper = [...chain, node];
                kids.forEach(kid => flow(kid, deeper));
            }

            function deepest(chain) {
                let node = page;
                for (let i = 0; i < chain.length; i++) {
                    const orig = chain[i];
                    uid(orig);

                    let found = null;
                    for (let j = node.children.length - 1; j >= 0; j--) {
                        if (node.children[j].__sid === orig.__sid) {
                            found = node.children[j];
                            break;
                        }
                    }

                    if (!found) {
                        for (let k = i; k < chain.length; k++) {
                            const c = shallow(chain[k]);
                            c.__sid = chain[k].__sid;
                            node.appendChild(c);
                            node = c;
                        }
                        return node;
                    }
                    node = found;
                }
                return node;
            }

            function breakPage() {
                page = makePage();
                container.appendChild(page);
            }

            function splitTable(table, getTarget) {
                const thead = table.querySelector(":scope > thead");
                const rows = Array.from(table.querySelectorAll(":scope > tbody > tr"));

                if (!rows.length) {
                    const t = getTarget();
                    t.appendChild(table);
                    if (overflows()) {
                        t.removeChild(table);
                        breakPage();
                        getTarget().appendChild(table);
                    }
                    return;
                }

                const cg = buildColgroup(table, thead);

                let target = getTarget();
                let shell = makeShell(table, thead, true, cg);
                target.appendChild(shell);

                if (overflows()) {
                    target.removeChild(shell);
                    breakPage();
                    target = getTarget();
                    shell = makeShell(table, thead, true, cg);
                    target.appendChild(shell);
                }

                rows.forEach(row => {
                    const tbody = shell.querySelector("tbody");
                    tbody.appendChild(row);

                    if (!overflows()) return;

                    tbody.removeChild(row);

                    if (tbody.children.length === 0) {
                        tbody.appendChild(row);
                        return;
                    }

                    breakPage();
                    target = getTarget();
                    shell = makeShell(table, thead, false, cg);
                    target.appendChild(shell);
                    shell.querySelector("tbody").appendChild(row);
                });
            }

            function buildColgroup(table, thead) {
                const existing = table.querySelector(":scope > colgroup");
                if (existing) return existing.cloneNode(true);
                if (!thead) return null;

                const hRows = Array.from(thead.querySelectorAll("tr"));
                if (!hRows.length) return null;

                const totalCols = Array.from(hRows[0].cells)
                    .reduce((s, c) => s + (parseInt(c.getAttribute("colspan") || "1", 10)), 0);

                const widthMap = new Array(totalCols).fill(null);
                const occupied = new Array(totalCols).fill(0);

                hRows.forEach(row => {
                    let ci = 0;
                    Array.from(row.cells).forEach(cell => {
                        while (ci < totalCols && occupied[ci] > 0) {
                            occupied[ci]--;
                            ci++;
                        }
                        const cs = parseInt(cell.getAttribute("colspan") || "1", 10);
                        const rs = parseInt(cell.getAttribute("rowspan") || "1", 10);
                        const sw = cell.style.width;
                        const aw = cell.getAttribute("width");
                        let w = sw || (aw ? (/^\d+$/.test(aw) ? aw + "px" : aw) : null);
                        if (w && cs > 1) {
                            const m = w.match(/^([\d.]+)(px|%|mm)?$/);
                            if (m) w = (parseFloat(m[1]) / cs).toFixed(1) + (m[2] || "px");
                        }
                        for (let i = 0; i < cs && ci + i < totalCols; i++) {
                            if (widthMap[ci + i] === null && w) widthMap[ci + i] = w;
                            if (rs > 1) occupied[ci + i] = Math.max(occupied[ci + i], rs - 1);
                        }
                        ci += cs;
                    });
                });

                const cg = document.createElement("colgroup");
                widthMap.forEach(w => {
                    const col = document.createElement("col");
                    if (w) col.style.width = w;
                    cg.appendChild(col);
                });
                return cg;
            }

            function makeShell(orig, thead, withHeader, cg) {
                const t = document.createElement("table");
                t.className = orig.className;
                t.style.cssText = orig.style.cssText;
                t.style.width = "100%";
                t.style.borderCollapse = "collapse";
                const b = orig.getAttribute("border");
                if (b) t.setAttribute("border", b);
                if (cg) t.appendChild(cg.cloneNode(true));
                if (withHeader && thead) t.appendChild(thead.cloneNode(true));
                t.appendChild(document.createElement("tbody"));
                return t;
            }

            function overflows() {
                return page.scrollHeight > page.clientHeight;
            }

            function shallow(el) {
                const c = el.cloneNode(false);
                c.removeAttribute("id");
                return c;
            }

            function makePage() {
                const div = document.createElement("div");
                div.className = "a4-page portrait";
                return div;
            }

            function addPageNumbers() {
                const pages = document.querySelectorAll(".a4-page");
                pages.forEach((p, i) => {
                    const footer = document.createElement("div");
                    footer.style.cssText =
                        "position:absolute;bottom:20px;left:60px;right:60px;" +
                        "border-top:1px solid #808080;padding-top:6px;" +
                        "text-align:right;font-size:9pt;color:#808080;" +
                        "font-family:Tahoma,sans-serif;";
                    footer.innerText =
                        "Abridged Course Syllabus: {{ $syllabus->course->course_code }} | Page " +
                        (i + 1) + " of " + pages.length;
                    p.appendChild(footer);
                });
                if (pageCountEl) {
                    pageCountEl.textContent =
                        pages.length + " page" + (pages.length !== 1 ? "s" : "");
                }
            }
        });
    </script>

    @if (empty($isSnapshot))
        @include('Syllabus.preview._versions_drawer', [
            'syllabus' => $syllabus,
            'savedVersions' => $savedVersions ?? collect(),
            'previewMode' => $previewMode ?? 'live',
            'previewVariant' => $previewVariant ?? 'abridged',
            'activeSavedVersion' => $activeSavedVersion ?? null,
            'openButton' => 'none',
        ])
    @endif

</body>

</html>
