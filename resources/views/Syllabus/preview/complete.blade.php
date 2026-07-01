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
    {{-- <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Franklin:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tahoma&display=swap" rel="stylesheet"> --}}

    <title>Course Syllabus - {{ $syllabus->course->course_code }}</title>

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
</head>

<body>

    <div id="toolbar">
        <div class="t-left">
            <span class="t-title">
                {{ $syllabus->course->course_code }} - {{ $syllabus->course->course_title }}
            </span>
            <span class="t-pages" id="page-count"></span>
        </div>

        @if (empty($isSnapshot))
            <div class="t-center">
                <button type="button" class="is-active"
                    onclick="window.location.href='{{ route('syllabus.preview.complete', $syllabus) }}'">
                    Complete
                </button>
                <button type="button"
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
        <div class="a4-section a4-title">COURSE SYLLABUS</div>
        <br>
        <div class="a4-subtitle a4-title">{{ $syllabus->course->course_code }} - {{ $syllabus->course->course_title }}
        </div>
        <h3 class="a4-section title-lettered">A. University Information</h3>

        <div class="a4-section">
            <strong class="indent-level-1 title-numbered">1. Vision of the University</strong>
            <p class="indent-level-2">Central Luzon State University (CLSU) as a world-class National University for
                science and technology in agriculture and allied fields.</p>
        </div>

        <div class="a4-section">
            <strong class="indent-level-1 title-numbered">2. Mission of the University</strong>
            <p class="indent-level-2">CLSU shall develop globally competitive, work-ready, socially-responsible and
                empowered human resources who value life-long learning; and to generate, disseminate, and apply
                knowledge and technologies for poverty alleviation, environmental protection, and sustainable
                development.</p>
        </div>

        <div class="a4-section">
            <strong class="indent-level-1 title-numbered">3. Educational Philosophy</strong>
            <p class="indent-level-2">The Central Luzon State University is committed and dedicated to provide a
                holistic transformative education anchored on its mission statement and its institutional core values.
                As stated on its mission, the University shall develop globally competitive, work-ready,
                socially-responsible and empowered human resources who value life-long learning; and shall generate,
                disseminate, and apply knowledge and technologies for poverty alleviation, environmental protection and
                sustainable development.</p>
            <p class="indent-level-2">Along with the curricular programs, the academic journey of learners revolves on
                three value-laden dimensions: creativeness and innovativeness, hard work and integrity, and
                inclusiveness and transformativeness.</p>
            <br>
            <p class="indent-level-1-5-text">With these, the university shall:</p>
            <ul>
                <li>Provide a teaching and learning environment which harness creativity and innovativeness among
                    learners. It advocates the development of individuals to become agents of change, innovators and
                    leaders, imbued with an outward and forward-thinking perspective in their respective fields. It
                    further ensures the vital role of research in promoting quality and excellence. Thus, regular
                    updating of curricular programs, empowerment of human capital, modernizing instructional and
                    pedagogical resources, and equal opportunity for all, are always observed.
                </li>
                <li>Adopt experiential learning on its programs along with the dynamic and continuous engagement
                    between the faculty, the staff, the students and the community. The shared values of hard work
                    and integrity puts forth in the discovery of new knowledge and in its application in real-life
                    contexts. Thus, enabling and preparing the learners to be effective and efficient navigators of
                    the future.
                </li>
                <li>Provide experiences that enable learners to discover the fulfillment of embracing diversity in
                    the form of various academic collaborations at the local, regional and international levels.
                    Students are guided to acknowledge and respect peoples and their cultures for inclusive societal
                    transformation.
                </li>
            </ul>
        </div>

        <div class="a4-section">
            <strong class="indent-level-1 title-numbered">4. Quality Policy Statement</strong>

            <div class="a4-alpha-list indent-level-1-5">
                <div>Excellent service to humanity is our commitment.</div>
                <div>We are committed to develop globally-competent and empowered human resources,
                    and to generate knowledge and technologies for inclusive societal development.
                </div>
                <div>We are dedicated to uphold CLSU's core values and principles, comply with statutory
                    and regulatory standards and continuously improve the effectiveness of our quality management
                    systems.
                </div>
                <div>Mahalaga ang inyong tinig upang higit na mapahusay ang kalidad ng aming paglilingkod.</div>
            </div>
        </div>

        <div class="a4-section">
            <strong class="indent-level-1 title-numbered">5. Goals of the {{ $collegeName }}</strong>
            <div class="indent-level-1-5">
                @forelse ($collegeGoals as $goal)
                    <div class="a4-coded-list">
                        <div class="code">{{ $goal->college_goals_code }}.</div>
                        <div class="text">{{ $goal->goal_text }}</div>
                    </div>
                @empty
                    <div class="indent-level-1-5">No college goals found.</div>
                @endforelse
            </div>
        </div>

        <div class="a4-section">
            <strong class="indent-level-1 title-numbered">6. Objectives of the {{ $departmentName }}</strong>
            <div class="indent-level-1-5">
                @forelse ($departmentObjectives as $objective)
                    <div class="a4-coded-list">
                        <div class="code">{{ $objective->dept_obj_code }}.</div>
                        <div class="text">{{ $objective->objective_text }}</div>
                    </div>
                @empty
                    <div class="indent-level-1-5">No department objectives found.</div>
                @endforelse
            </div>
        </div>

        <h3 class="a4-section title-lettered">B. Program Information</h3>
        <div class="a4-section">
            <div class="table-indent">
                <table class="kv-table">
                    <tbody>
                        <tr>
                            <td>1. Name of Program</td>
                            <td>{{ $program->name }}</td>
                        </tr>
                        <tr>
                            <td>2. BOR Approval</td>
                            <td>{{ $program->bor_approval_no ?? '' }}</td>
                        </tr>
                        <tr>
                            <td>3. Date of Approval</td>
                            <td>{{ $program->bor_approval_date ?? '' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="a4-section">
            <strong class="indent-level-1 title-numbered">7. Program Educational Objectives (PEOs)</strong>
            <div class="table-indent">
                <table>
                    <thead>
                        <tr>
                            <th><b>Program Educational Objectives</b><br>
                                <p>Three to five years after graduation, the BSIT graduates are:</p>
                            </th>
                            <th>Mission</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($peos as $peo)
                            <tr>
                                <td>
                                    <div class="a4-coded-list">
                                        <div class="code">{{ $peo->peo_code }}.</div>
                                        <div class="text">{{ $peo->peo_text }}</div>
                                    </div>
                                </td>
                                <td style="text-align: center;"><i class="bx bx-check">&#10003;</i></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2">No PEOs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="a4-section">
            <strong class="indent-level-1 title-numbered">8. Program Outcomes (POs) and its Relationship to the Program
                Educational
                Objectives (PEOs)
            </strong>
            <div class="table-indent">
                <table>
                    <thead>
                        <tr>
                            <th colspan="2">Program Outcomes</th>
                            <th colspan="{{ max($peos->count(), 1) }}">Program Educational Objectives</th>
                        </tr>
                        <tr>
                            <th colspan="2">
                                <p>By the time of graduation, students of the program have the ability to:</p>
                            </th>
                            @forelse ($peos as $peo)
                                <th>{{ $peo->peo_code }}</th>
                            @empty
                                <th>-</th>
                            @endforelse
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pos as $po)
                            <tr>
                                <td>({{ $po->po_code }})</td>
                                <td>{{ $po->po_text }}</td>
                                @forelse ($peos as $peo)
                                    <td style="text-align: center;">{!! $po->peos->contains('id', $peo->id) ? '&#10003;' : ' ' !!}</td>
                                @empty
                                    <td style="text-align: center;">-</td>
                                @endforelse
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $peos->count() + 2 }}" style="text-align: center;">No POs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <h3 class="a4-section title-lettered">C. Instructor Information</h3>
        <div class="a4-section">
            <div class="table-indent">
                <table class="kv-table">
                    <tbody>
                        <tr>
                            <td>1. Name of Instructor/Professor</td>
                            <td>{!! $lecLabValue($lecComponent?->instructor_name, $labComponent?->instructor_name) !!}</td>
                        </tr>
                        <tr>
                            <td>2. Office</td>
                            <td>{!! $lecLabValue($lecComponent?->office, $labComponent?->office) !!}</td>
                        </tr>
                        <tr>
                            <td>3. Phone No. (Optional)</td>
                            <td>{!! $lecLabValue($lecComponent?->phone, $labComponent?->phone) !!}</td>
                        </tr>
                        <tr>
                            <td>4. Email Address</td>
                            <td>{!! $lecLabValue($lecComponent?->instructor_email, $labComponent?->instructor_email) !!}</td>
                        </tr>
                        <tr>
                            <td>5. Consultation Hours</td>
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
                                {{-- { $labInstructor } --}}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <h3 class="a4-section title-lettered">D. Course Information</h3>
        <div class="a4-section">
            <div class="table-indent">
                <table class="kv-table">
                    <tbody>
                        <tr>
                            <td>1. Course Code</td>
                            <td>{{ $syllabus->course->course_code }}</td>
                        </tr>
                        <tr>
                            <td>2. Course Title</td>
                            <td>{{ $syllabus->course->course_title ?? '' }}</td>
                        </tr>
                        <tr>
                            <td>3. Course Description</td>
                            <td>{{ $syllabus->course->course_description ?? '' }}</td>
                        </tr>
                        <tr>
                            <td>4. Pre-requisite</td>
                            <td>{{ $syllabus->course->prerequisite ?? 'None' }}</td>
                        </tr>
                        <tr>
                            <td>5. Co-requisite</td>
                            <td>{{ $syllabus->course->corequisite ?? 'None' }}</td>
                        </tr>
                        <tr>
                            <td>6. Credit Units</td>
                            <td>{{ $syllabus->course->credit_units }}</td>
                        </tr>
                        <tr>
                            <td>7. Class Hours</td>
                            <td>{!! $lecLabValue($lecComponent?->class_hours, $labComponent?->class_hours) !!}</td>
                        </tr>
                        <tr>
                            <td>8. Class Schedule</td>
                            <td>
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
                    </tbody>
                </table>
            </div>
        </div>

        <div class="landscape">
            <h3 class="a4-section title-numbered">9. Course Outcomes (COs) and Relationship to Program Outcomes</h3>
            <table>
                <thead>
                    <tr>
                        <th rowspan="3" style="background-color:#ddd; text-align:left; vertical-align:top;">
                            Program Outcomes addressed by the course
                        </th>

                        <th colspan="{{ count($pos) }}" style="background-color:#ddd; text-align:center;">
                            PO Code
                        </th>
                    </tr>

                    <tr>
                        @foreach ($pos as $po)
                            <th style="text-align:center; width:30px;">
                                {{ $po->po_code }}
                            </th>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach ($pos as $po)
                            <td style="text-align:center;">
                                @php
                                    $is_mapped = isset($coursePoIedMap[$po->id]);
                                @endphp
                                {{ $is_mapped ? '✓' : '' }}
                            </td>
                        @endforeach
                    </tr>
                </thead>
            </table>

            <table border="1" style="width:100%; border-collapse: collapse;">
                <tbody>
                    <tr>
                        <td rowspan="2" style="background-color:#ddd; font-weight:bold; vertical-align:top;">
                            Program Outcomes addressed by the course
                        </td>

                        <td colspan="{{ count($pos) }}"
                            style="background-color:#ddd; text-align:center; font-weight:bold;">
                            PO Code
                        </td>
                    </tr>

                    <tr>
                        @foreach ($pos as $po)
                            <td style="background-color:#ddd; text-align:center; font-weight:bold;">
                                {{ $po->po_code }}
                            </td>
                        @endforeach
                    </tr>

                    @forelse ($courseOutcomes as $co)
                        <tr>
                            <td>
                                <div class="a4-coded-list">
                                    <div class="code"><strong>{{ $co->co_code }}: </strong></div>
                                    <div class="text">{{ $co->description }}</div>
                                </div>
                            </td>

                            @foreach ($pos as $po)
                                <td style="text-align:center; width:30px;">
                                    {{ $coursePoIedMap[$po->id] ?? '' }}
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($pos) + 1 }}" style="text-align:center;">
                                No course outcomes found.
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
            <p><strong>*Level:</strong> &nbsp; I – Introductory, &nbsp; E – Enabling, &nbsp; D – Demonstrative</p>
        </div>

        <div class="landscape">
            <h3 class="a4-section title-numbered">10. Weekly Coverage</h3>

            <br>
            @if ($syllabus->course->has_lec_lab)
                <p><strong>Lecture (LEC)</strong></p>
            @endif
            <table class="weekly-coverage-table">
                <thead>
                    <tr>
                        <th style="text-align:center; width: 90px;">Week</th>
                        <th style="text-align:center; width: 320px;">Course Outcome</th>
                        <th style="text-align:center;">Topics</th>
                        <th style="text-align:center;">Learning Outcomes</th>
                        <th style="text-align:center;">Teaching and Learning Activities</th>
                        <th style="text-align:center;">Assessment Task</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse (($weeklyCoverageRows['LEC'] ?? []) as $row)
                        <tr>
                            @if (($row['is_exam'] ?? false) === true)
                                <td style="text-align:center; background-color: #d9d9d9;">{{ $row['week_label'] }}
                                </td>
                                <td colspan="5"
                                    style="text-align:center; font-weight:bold; background-color: #d9d9d9; font-style:italic; vertical-align:middle;">
                                    {{ $row['exam_label'] ?? 'Exam' }}
                                </td>
                            @else
                                <td style="text-align:center; font-size: 11pt;">{{ $row['week_label'] }}</td>
                                <td>{{ blank($row['co_description'] ?? null) ? '---' : $row['co_description'] }}</td>
                                <td>{!! blank(strip_tags($row['topics'] ?? '')) ? '---' : $row['topics'] !!}</td>
                                <td>{!! blank(strip_tags($row['learning_outcomes'] ?? '')) ? '---' : $row['learning_outcomes'] !!}</td>
                                <td>{!! blank(strip_tags($row['tla'] ?? '')) ? '---' : $row['tla'] !!}</td>
                                <td style="text-align:center;">{!! blank(strip_tags($row['assessment_task'] ?? '')) ? '---' : $row['assessment_task'] !!}</td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;">No weekly coverage found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            
            <br>
            @if ($syllabus->course->has_lec_lab)
                <p style="margin-top: 10px;"><strong>Laboratory (LAB)</strong></p>
                <table class="weekly-coverage-table" border="1" style="width:100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="text-align:center; width: 90px;">Week</th>
                            <th style="text-align:center; width: 320px;">Course Outcome</th>
                            <th style="text-align:center;">Topics</th>
                            <th style="text-align:center;">Learning Outcomes</th>
                            <th style="text-align:center;">Teaching and Learning Activities</th>
                            <th style="text-align:center;">Assessment Task</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse (($weeklyCoverageRows['LAB'] ?? []) as $row)
                            <tr>
                                @if (($row['is_exam'] ?? false) === true)
                                    <td style="text-align:center; background-color: #d9d9d9;">{{ $row['week_label'] }}
                                    </td>
                                    <td colspan="5"
                                        style="text-align:center; font-weight:bold; background-color: #d9d9d9; font-style:italic; vertical-align:middle;">
                                        {{ $row['exam_label'] ?? 'Exam' }}
                                    </td>
                                @else
                                    <td style="text-align:center;">{{ $row['week_label'] }}</td>
                                    <td>{{ blank($row['co_description'] ?? null) ? '---' : $row['co_description'] }}</td>
                                    <td style="font-size: 11pt;">{!! blank(strip_tags($row['topics'] ?? '')) ? '---' : $row['topics'] !!}</td>
                                    <td style="font-size: 11pt;">{!! blank(strip_tags($row['learning_outcomes'] ?? '')) ? '---' : $row['learning_outcomes'] !!}</td>
                                    <td style="font-size: 11pt;">{!! blank(strip_tags($row['tla'] ?? '')) ? '---' : $row['tla'] !!}</td>
                                    <td style="text-align:center;">{!! blank(strip_tags($row['assessment_task'] ?? '')) ? '---' : $row['assessment_task'] !!}</td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center;">No weekly coverage found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @endif
        </div>

        <div class="portrait">
            <h3 class="a4-section title-numbered">11. Course Evaluation</h3>
            <br>
            <p class="indent-level-1">a. Course Requirements</p>
            <p class="indent-level-2">The student performance in this course will be rated based on the following:</p>

            <div class="table-indent">
                <table class="evaluation-table">
                    <thead>
                        <tr>
                            <th rowspan="2" style="text-align:center; white-space: normal;">Course Outcomes</th>
                            <th colspan="2" style="text-align:center;">
                                {{ $syllabus->course->has_lec_lab ? 'LECTURE (67%)' : 'LECTURE' }}
                            </th>
                            @if ($syllabus->course->has_lec_lab)
                                <th colspan="2" style="text-align:center;">LABORATORY (33%)</th>
                            @endif
                            <th rowspan="2" style="text-align:center;">Performance Standard</th>
                        </tr>
                        <tr>
                            <th style="text-align:center; white-space: nowrap;">Assessment Task</th>
                            <th style="text-align:center; white-space: nowrap;">Weight (%)</th>
                            @if ($syllabus->course->has_lec_lab)
                                <th style="text-align:center; white-space: nowrap;">Assessment Task</th>
                                <th style="text-align:center; white-space: nowrap;">Weight (%)</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // Passing mark = performance_standard from the component (not a fixed 60%).
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

                            // Group Performance Standard by term (term ends at exam row).
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
                        @forelse (($evaluationRows ?? []) as $index => $row)
                            <tr>
                                <td style="text-align:left;">{{ $row['co_label'] ?? '' }}</td>
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
                                    style="text-align:center;">
                                    No evaluation items found.
                                </td>
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
                            <td colspan="5"><b>Minimum Average for Satisfactory Performance</b></td>
                            <td style="text-align:center;"><b>{{ $lecPassMark }}%</b></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="indent-level-1">
                <p style="margin-top: 14px;">b. Computation of Final Course Average Score (FCAS)</p>
                <br>
                @if ($syllabus->course->has_lec_lab)
                    <p><strong>FCAS = (0.67) × LecAve + (0.33) × LabAve + APP</strong></p>
                    <br>
                    <table class="fcas">
                        <tr>
                            <td style="width: 120px;">Where:</td>
                            <td>FCAS &nbsp;= Final Course Average Score <br>
                                LecAve = Lecture Average Score <br>
                                LabAve = Laboratory Average Score <br>
                                APP = Additional point incentive for student athletes, performers and student
                                delegates/representatives [CLSU BOR Resolution No. 32-09]
                            </td>
                        </tr>
                    </table>
                @else
                    <p class="indent-level-2">
                        <strong>FCAS = LecAve + APP</strong>
                    </p>
                    <br>
                    <table class="fcas">
                        <tr>
                            <td style="width: 120px;">Where:</td>
                            <td>FCAS = Final Course Average Score <br>
                                LecAve = Lecture Average Score
                                APP = Additional point incentive for student athletes, performers and student
                                delegates/representatives [CLSU BOR Resolution No. 32-09]
                            </td>
                        </tr>
                    </table>
                @endif
                <p>c. Transmutation</p>
                <p class="indent-level-2">The final grades will correspond to the weighted average scores shown below:
                </p>
            </div>

            @php
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
            </div>

            {{-- <div class="table-indent">
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
                <p style="margin-top: 4px;" class="indent-level-1">Passing Mark: {{ $lecPassMark }}%</p>
            </div> --}}
        </div>

        <div class="portrait">
            <h3 class="a4-section title-numbered">12. References</h3>
            <br>

            <div class="a4-list">
                @forelse (($allReferences ?? []) as $refText)
                    <div class="indent-level-1-5">{{ $refText }}</div>
                @empty
                    <div class="indent-level-1-5">No references encoded.</div>
                @endforelse
                <br>
                <div class="indent-level-1-5" style="margin-top:8px; font-weight: bold;">
                    <strong>Online materials:</strong>
                </div>

                @forelse (($onlineMaterialLinks ?? []) as $url)
                    @php
                        $link = Str::startsWith($url, ['http://', 'https://']) ? $url : 'https://' . $url;
                    @endphp

                    <div class="indent-level-1-5">
                        <a href="{{ $link }}" target="_blank"
                            style="text-decoration: underline; font-weight: bold;">
                            {{ $url }}
                        </a>
                    </div>
                @empty
                    <div class="indent-level-1-5">No online materials encoded.</div>
                @endforelse
            </div>
            <br>

            <h3 class="a4-section title-numbered">13. Course Materials Made Available</h3>
            <br>
            <div class="a4-list">
                @forelse (($syllabus->courseMaterials ?? []) as $material)
                    <div class="indent-level-1-5">{{ $loop->index + 1 }}. {{ $material->material_text }}</div>
                @empty
                    <div class="indent-level-1-5">a. Course Syllabus</div>
                    <div class="indent-level-1-5">b. Reading Texts</div>
                    <div class="indent-level-1-5">c. Multimedia Resources</div>
                    <div class="indent-level-1-5">d. Lecture Notes and Slide Presentations</div>
                @endforelse
            </div>
            <br>

            <h3 class="a4-section title-numbered">14. Contribution of Course to Meeting the Professional Component</h3>
            <br>
            <div class="a4-list">
                <div class="indent-level-1-5">a. General Education: {{ $syllabus->contribution_general_ed ?? '0' }} %
                </div>
                <div class="indent-level-1-5">b. Professional Education:
                    {{ $syllabus->contribution_professional_ed ?? '100' }} %</div>
                <div class="indent-level-1-5">c. ITE Professional Courses:
                    {{ $syllabus->contribution_ite_professional ?? '0' }} %</div>
                <div class="indent-level-1-5">d. ITE Electives: {{ $syllabus->contribution_ite_electives ?? '0' }} %
                </div>
            </div>

            <h3 class="a4-section title-lettered">E. Others</h3>
            <div class="a4-section">
                <strong class="indent-level-1 title-numbered section-e-title">1. Life-long Learning
                    Opportunities</strong>
                <p class="indent-level-2-text">
                    The course offers lifelong learning in network design, troubleshooting, security, and emerging
                    technologies, fostering expertise crucial for modern IT careers and adaptability in the
                    ever-evolving tech landscape.
                </p>
            </div>
            <br>
            <div class="a4-section">
                <strong class="indent-level-1 title-numbered section-e-title">2. Course Policies</strong>
                <p class="indent-level-2-text">The policies to be implemented in this course are
                    based on the approved academic policies indicated in the University Code or Student Handbook.</p>
            </div>
            <br>
            <div class="a4-section">
                <strong class="indent-level-1 title-numbered section-e-title">3. Ethics and Conduct</strong>

                <p class="indent-level-2 section-e-subtitle"><strong>Class Conduct</strong></p>

                <p class="indent-level-2 section-e-em"><em>Class Preparation:</em></p>
                <p class="indent-level-2-text section-e-description">It is essential that students come prepared to get
                    the most out of a class
                    lecture and case discussions. Concepts covered in the class are easier to comprehend if a student
                    has completed assignments before coming to the class. Undivided commitment, attention and efforts
                    are key to better study and learning.</p>

                <p class="indent-level-2 section-e-em"><em>Class Attendance:</em></p>
                <p class="indent-level-2-text section-e-description">Students must plan to maintain regular attendance
                    in each course. They must
                    understand that regular attendance is essential for learning and maintaining good academic
                    performance. It is possible that a student may obtain an unofficially dropped grade of 5.00 if
                    he reaches a number of absences as indicated in the policy.</p>

                <p class="indent-level-2 section-e-em"><em>Class Participation:</em></p>
                <p class="indent-level-2-text section-e-description">Class attendance is NOT class participation. It is
                    essential that a student
                    come prepared to get the most out of a class lecture and case discussion. Material is generally
                    not easily comprehensible if a student has not completed assignments prior to class session.</p>

                <p class="indent-level-2 section-e-em"><em>Expected Study Habits:</em></p>
                <p class="indent-level-2-text section-e-description">The coursework is based on continuous
                    teacher-student interaction, class
                    discussions and student participation. The course instructors make assignments of reading
                    materials prior to their discussion/lecture in the class. It is essential for a student to go
                    through the reading material prior to the class in order to interact effectively with the
                    instructor and fellow students. Students must not only prepare assigned topics from the required
                    textbooks, recommended books, and supplementary material, but where necessary, will also search
                    for relevant material elsewhere.</p>

                <p class="indent-level-2 section-e-subtitle"><strong>Student Conduct</strong></p>

                <p class="indent-level-2 section-e-em"><em>Awareness of Institution Policies:</em></p>
                <p class="indent-level-2-text section-e-description">It is the student's responsibility to keep
                    informed
                    about the College
                    policies and the deadlines for various activities. READING THE EMAIL COMMUNICATIONS will keep
                    student up to date on deadlines for various activities such as add/drop, withdraws, registration,
                    tuition fee payments, clearance from library and computer lab, etc.</p>

                <p class="indent-level-2 section-e-em"><em>Punctuality:</em></p>
                <p class="indent-level-2-text section-e-description">Both students and instructors are expected to be
                    in
                    the class on time.
                    Students should be in the class before the arrival of instructor. The instructor may take roll
                    call at the beginning of the class. Late comers may be marked as absent.</p>

                <p class="indent-level-2 section-e-em"><em>Discipline:</em></p>
                <p class="indent-level-2-text section-e-description">The College admits students assuming mature
                    conduct
                    on the part of students.
                    Regularity in class attendance, proper behavior in and out of the class rooms towards
                    instructors, colleagues and staff of the College are expected norms. Misbehavior towards faculty
                    members and staff may result in immediate separation from the program. Damage to Institution
                    property may result in appropriate penalty and/or in serious situations separation from the
                    program.</p>

                <p class="indent-level-2 section-e-em"><em>Etiquettes:</em></p>
                <p class="indent-level-2-text section-e-description">Students are required to behave in a decent and
                    socially acceptable manner
                    towards their teachers, coll ege administration and fellow students. It is recommended that all
                    students use English as the medium of communication on the campus, in general, and during class
                    sessions in particular.</p>

                <p class="indent-level-2 section-e-em"><em>Meeting the Deadlines:</em></p>
                <p class="indent-level-2-text section-e-description">Students are provided an "Academic Calendar" for
                    the
                    program which indicates
                    "Important Dates to Remember". Dates for assignment presentations for particular courses are
                    given in the course outline or handouts. It is in students' own interest that these dates are
                    strictly observed and deadlines met. No relaxation is provided after due dates.</p>

                <p class="indent-level-2 section-e-em"><em>Class Room Mannerisms:</em></p>
                <p class="indent-level-2-text section-e-description">Students are expected to conduct themselves as
                    professionals in class and
                    observe a disciplined atmosphere. Class attendance is taken regularly and may constitute a part
                    of the final course grade. Late arrival in the class distracts the instructor and other students.
                    Class participation by students is expected and strongly encouraged. However, to maintain class
                    discipline and effectiveness, it is recommended that a student asks permission from the
                    instructor before asking questions or entering a discussion.</p>

                <p class="indent-level-2 section-e-subtitle"><strong>Attending On-line Classes and
                        Presentations</strong></p>

                <div class="indent-level-2-text">
                    <p class="section-e-description">On-line classes and presentations:</p>
                    <p class="section-e-description">Wear appropriate attire. See CLSU dress code for online meetings,
                        classes, and online defenses
                        and similar activities.</p>
                    <p class="section-e-description">Use appropriate background. A blank neat wall, well-organized book
                        shelves, or clean office
                        environment is acceptable. Avoid backgrounds that may show personal spaces such as bedrooms
                        or kitchens unless it is part of the topic.</p>
                    <p class="section-e-description">As much as possible, say your name before you speak for the first
                        time.</p>
                    <p class="section-e-description">Always mute your microphone when it is not your turn to speak.</p>
                    <p class="section-e-description">Often make eye contact with the camera. An appropriate photo of
                        oneself may be used if there
                        is no web camera.</p>
                    <p class="section-e-description">When appropriate, disable the audio announcement feature when
                        someone exits or enters the
                        meeting.</p>
                    <p class="section-e-description">Do not dominate the meeting by asking too many questions or
                        providing too many responses.
                        Give others a chance to speak.</p>
                    <p class="section-e-description">Be patient and expect technical issues to arise.</p>
                    <p class="section-e-description">Give the meeting your full attention. Avoid multi-tasking and
                        distracting gestures.</p>
                    <p class="section-e-description">When posting messages on message boards, chat boxes, or sending
                        emails, observe the University
                        guidelines for this purpose.</p>
                </div>

                <p class="indent-level-2 section-e-subtitle"><strong>Dress Code (online and face-to-face)</strong></p>
                <p class="indent-level-2-text section-e-description">The students are required to be reasonably well
                    dressed when coming to the
                    university. The dress should conform to the academic environment and should be decent in
                    appearance.</p>

                <p class="indent-level-2 section-e-em"><em>Meetings and classes:</em> </p>
                <p class="indent-level-2-text section-e-description">Be well-groomed. Wear decent, simple or
                    casual comfortable attire. Upper garment should be ample enough to cover the upper body. If the
                    lower half of the body will be shown, no skinny pants, boxer shorts, short shorts, or short
                    skirts.</p>

                <p class="indent-level-2 section-e-em"><em>For presentations:</em> </p>
                <p class="indent-level-2-text section-e-description">Wear corporate, formal, or smart casual attire.
                    Do not hide inappropriate clothing with virtual garments.</p>

                <p class="indent-level-2 section-e-em"><em>Mobile Phones:</em></p>
                <p class="indent-level-2-text section-e-description">Mobile phones must be switched off when attending
                    classes, library,
                    computer lab, and offices.</p>
            </div>

            <div class="a4-section">
                <strong class="indent-level-1 title-numbered section-e-title">4. Academic Integrity</strong>
                <p class="indent-level-2-text section-e-description">Maintaining academic integrity is essential for
                    fostering learning,
                    assuring fair evaluation, and upholding the credibility and quality of education. Policies on
                    academic integrity are as follows:</p>

                <div class="indent-level-2-text section-e-description">
                    <div>a. All are expected to be honest in all of their academic activities, including
                        assignments, assessments, research, and other projects.
                    </div>
                    <div>b. The use of another person's work without giving proper credit is
                        known as plagiarism. In order to properly credit sources, proper citation and reference are
                        required.
                    </div>
                    <div>c. Individual tasks and exams must be completed by each student on
                        their own, without assistance from others.
                    </div>
                </div>

                <p class="indent-level-2-text section-e-description">Any documented case of dishonesty will be dealt
                    with accordingly based on
                    the guidelines stipulated in the CLSU Code of Student Conduct and Discipline.</p>
            </div>

            <br>
            <h3 class="a4-section title-lettered">F. Revision History</h3>
            <table border="1" style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="text-align:center; width:80px;">Revision No.</th>
                        <th style="text-align:center; width:130px;">Date of Revision</th>
                        <th style="text-align:center; width:160px;">Semester of Implementation</th>
                        <th style="text-align:center;">Highlights of Revision</th>
                        <th style="text-align:center; width:180px;">Contributors</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($syllabusRevisions as $revision)
                        <tr>
                            <td style="text-align:center;">{{ $revision->revision_no }}</td>
                            <td style="text-align:center;">
                                {{ $revision->revision_date ? \Carbon\Carbon::parse($revision->revision_date)->format('M d, Y') : '' }}
                            </td>
                            <td style="text-align:center;">{{ $revision->implementation_semester }}</td>
                            <td>{{ $revision->highlights }}</td>
                            <td>{{ $revision->contributors }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center;">No revision history found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <h3 class="a4-section title-lettered">G. Preparation, Review and Approval</h3>
            <table border="1" style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="text-align:center; width:200px;">Role</th>
                        <th style="text-align:center;">Name of Faculty</th>
                        <th style="text-align:center; width:160px;">Signature</th>
                        <th style="text-align:center; width:120px;">Date Signed</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- ── Prepared by ──────────────────────────────────────────────── --}}
                    @php
                        $preparedByNames = collect($syllabus->components ?? [])
                            ->pluck('instructor_name')
                            ->filter()
                            ->unique()
                            ->values();
                        $preparedCount = max($preparedByNames->count(), 1);
                    @endphp

                    @if ($preparedByNames->isNotEmpty())
                        @foreach ($preparedByNames as $preparedName)
                            <tr>
                                @if ($loop->first)
                                    <td rowspan="{{ $preparedCount }}" style="vertical-align:top;">
                                        <strong>Prepared by:</strong>
                                    </td>
                                @endif
                                <td>{{ $preparedName }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td><strong>Prepared by:</strong></td>
                            <td>{{ $lecComponent?->instructor_name ?? 'N/A' }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endif

                    {{-- ── Reviewed by ──────────────────────────────────────────────── --}}
                    {{--
                    "Reviewed by:" label in ONE merged cell (rowspan = reviewer count).
                    Every reviewer row shows their name + "Member" role.
                    If no reviewers, show an empty placeholder row.
                --}}
                    @php $reviewerCount = count($syllabusReviewers); @endphp

                    @if ($reviewerCount > 0)
                        @foreach ($syllabusReviewers as $rIdx => $reviewer)
                            <tr>
                                @if ($rIdx === 0)
                                    <td rowspan="{{ $reviewerCount }}" style="vertical-align:top;">
                                        <strong>Reviewed by:</strong>
                                    </td>
                                @endif
                                <td>
                                    {{ $reviewer->user?->name ?? ($reviewer->name ?? '') }}
                                    <br>
                                    <span style="font-size:9pt; color:#444;">Member</span>
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td><strong>Reviewed by:</strong></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endif

                    {{-- ── Approved by (Dean) ───────────────────────────────────────── --}}
                    <tr>
                        <td><strong>Approved by:</strong></td>
                        <td>
                            {{ $approvedByUser?->name ?? '' }}
                            @if ($approvedByUser)
                                <br>
                                <span style="font-size:9pt; color:#444;">
                                    Dean, {{ $collegeName ?? 'College' }}
                                </span>
                            @endif
                        </td>
                        <td></td>
                        <td></td>
                    </tr>

                    {{-- ── Concurred by (Service Courses only) ─────────────────────── --}}
                    <tr>
                        <td colspan="4"
                            style="font-style:italic; font-size:9pt; color:#555; padding:4px 6px; background:#f8f8f8;">
                            Additional Signatories for Service Courses:
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Concurred by:</strong></td>
                        <td>
                            {{ $concurredByUser->name ?? '' }}
                            @if ($concurredByUser)
                                <br>
                                <span style="font-size:9pt; color:#444;">
                                    Chairperson, {{ $departmentName ?? 'Department' }}
                                </span>
                            @endif
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

    <div id="a4-container"></div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const source = document.getElementById("syllabus-content");
            const container = document.getElementById("a4-container");
            const pageCountEl = document.getElementById("page-count");

            let currentOrientation = "portrait";
            let page = makePage(currentOrientation);
            container.appendChild(page);

            // ── Counter for unique split IDs stored as JS properties ─────────────────
            let _uid = 0;

            function uid(node) {
                if (!node.__sid) node.__sid = ++_uid;
                return node.__sid;
            }

            // ── Walk every direct child of #syllabus-content ──────────────────────────
            Array.from(source.children).forEach(el => {
                const isLandscape = el.classList.contains("landscape");
                const isPortrait = el.classList.contains("portrait");

                if (isLandscape || isPortrait) {
                    const orient = isLandscape ? "landscape" : "portrait";
                    if (pageHasContent() || orient !== currentOrientation) {
                        page = makePage(orient);
                        container.appendChild(page);
                    } else {
                        page.className = "a4-page " + orient;
                    }
                    currentOrientation = orient;
                    el.classList.remove("landscape", "portrait");
                }

                if (el.tagName === "TABLE") {
                    splitTable(el, () => page);
                } else {
                    flow(el, []); // [] = no ancestor wrapper chain yet
                }
            });

            source.remove();
            addPageNumbers();

            // =========================================================================
            //  flow(node, chain)
            //
            //  `chain` = ordered array of original ancestor elements whose shallow
            //  clones must be present on the current page before `node` can be placed.
            //
            //  Algorithm:
            //    1. Get (or create) the deepest wrapper on the current page.
            //    2. Append `node`.
            //    3. If it fits → done.
            //    4. If it doesn't fit AND it has children → split it:
            //         a. Remove it, put a shallow clone in its place.
            //         b. Recurse over each child with `chain + [node]`.
            //    5. If it doesn't fit AND it's atomic → push to a new page.
            // =========================================================================
            function flow(node, chain) {
                // Tables always go through the specialised table splitter.
                if (node.nodeType === Node.ELEMENT_NODE && node.tagName === "TABLE") {
                    splitTable(node, () => deepest(chain));
                    return;
                }

                const target = deepest(chain);
                target.appendChild(node);

                if (!overflows()) return; // fits – nothing more to do

                target.removeChild(node);

                // Collect children (empty for text nodes / void elements)
                const kids = (node.nodeType === Node.ELEMENT_NODE) ?
                    Array.from(node.childNodes) : [];

                if (kids.length === 0) {
                    // Atomic node – push to a fresh page, rebuild the chain there.
                    breakPage();
                    deepest(chain).appendChild(node);
                    return;
                }

                // Non-atomic – place a shallow clone as the wrapper and distribute kids.
                uid(node); // ensure original has an ID
                const clone = shallow(node);
                clone.__sid = node.__sid; // clone inherits same ID
                target.appendChild(clone);

                const deeper = [...chain, node]; // extend the ancestry
                kids.forEach(kid => flow(kid, deeper));
            }

            // =========================================================================
            //  deepest(chain)
            //
            //  Walk `page`, following last-children that match each level of `chain`
            //  by __sid. If a level is missing on the current page, create it (and all
            //  subsequent levels) by appending fresh shallow clones.
            //
            //  Returns the innermost live clone on the current page.
            // =========================================================================
            function deepest(chain) {
                let node = page;
                for (let i = 0; i < chain.length; i++) {
                    const orig = chain[i];
                    uid(orig);

                    // Search backwards through node's children for a matching __sid.
                    let found = null;
                    for (let j = node.children.length - 1; j >= 0; j--) {
                        if (node.children[j].__sid === orig.__sid) {
                            found = node.children[j];
                            break;
                        }
                    }

                    if (!found) {
                        // This level doesn't exist yet on the current page – create the
                        // rest of the chain from here.
                        for (let k = i; k < chain.length; k++) {
                            const c = shallow(chain[k]);
                            c.__sid = chain[k].__sid; // same ID as the original
                            node.appendChild(c);
                            node = c;
                        }
                        return node;
                    }
                    node = found;
                }
                return node; // page itself if chain is empty
            }

            // =========================================================================
            //  breakPage
            //  Start a new page.  Does NOT rebuild the wrapper chain – callers that
            //  need the chain recreated should call deepest(chain) after this.
            // =========================================================================
            function breakPage() {
                page = makePage(currentOrientation);
                container.appendChild(page);
            }

            // =========================================================================
            //  splitTable
            //
            //  Distributes <tbody> rows across pages.
            //  • A colgroup mirroring the header's column widths is injected into every
            //    shell so continuation tables have identical column proportions.
            //  • Headers are NOT repeated on continuation pages (cleaner output).
            //  • `getTarget` is a callback that returns the DOM node to append shells
            //    to. On a new page this callback re-runs, picking up the freshly rebuilt
            //    wrapper (if any).
            // =========================================================================
            function splitTable(table, getTarget) {
                const thead = table.querySelector(":scope > thead");
                const rows = Array.from(table.querySelectorAll(":scope > tbody > tr"));

                // Table has no body rows – treat as a plain block.
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

                const cg = colgroup(table, thead);

                // ── First shell (includes thead) ──────────────────────────────────────
                let target = getTarget();
                let shell = shell_(table, thead, true, cg);
                target.appendChild(shell);

                if (overflows()) {
                    // Header alone overflows the current page → move to a new page.
                    target.removeChild(shell);
                    breakPage();
                    target = getTarget();
                    shell = shell_(table, thead, true, cg);
                    target.appendChild(shell);
                    // If it STILL overflows on a blank page, accept it (can't split a header).
                }

                // ── Row-by-row distribution ───────────────────────────────────────────
                rows.forEach(row => {
                    const tbody = shell.querySelector("tbody");
                    tbody.appendChild(row);

                    if (!overflows()) return; // fits

                    tbody.removeChild(row);

                    // Edge-case: the row alone is taller than a full page.
                    if (tbody.children.length === 0) {
                        tbody.appendChild(row); // accept the overflow, prevent infinite loop
                        return;
                    }

                    // Row doesn't fit – open a continuation page (no header).
                    breakPage();
                    target = getTarget();
                    shell = shell_(table, thead, false, cg);
                    target.appendChild(shell);
                    shell.querySelector("tbody").appendChild(row);
                });
            }

            // ── Table helpers ─────────────────────────────────────────────────────────

            function colgroup(table, thead) {
                const existing = table.querySelector(":scope > colgroup");
                if (existing) return existing.cloneNode(true);
                if (!thead) return null;

                const headerRows = thead.querySelectorAll("tr");
                const leaf = headerRows[headerRows.length - 1];
                if (!leaf) return null;

                const cg = document.createElement("colgroup");
                Array.from(leaf.cells).forEach(cell => {
                    const col = document.createElement("col");
                    const sw = cell.style.width;
                    const aw = cell.getAttribute("width");
                    if (sw) col.style.width = sw;
                    else if (aw) col.style.width = /^\d+$/.test(aw) ? aw + "px" : aw;
                    cg.appendChild(col);
                });
                return cg;
            }

            function shell_(orig, thead, withHeader, cg) {
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

            // ── General helpers ───────────────────────────────────────────────────────

            function overflows() {
                return page.scrollHeight > page.clientHeight;
            }

            function pageHasContent() {
                return page.children.length > 0;
            }

            function shallow(el) {
                const c = el.cloneNode(false);
                c.removeAttribute("id");
                return c;
            }

            function makePage(orientation) {
                const div = document.createElement("div");
                div.className = "a4-page " + (orientation || "portrait");
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
                        "Course Syllabus: {{ $syllabus->course->course_code }} | Page " +
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
            'previewVariant' => $previewVariant ?? 'complete',
            'activeSavedVersion' => $activeSavedVersion ?? null,
            'openButton' => 'none',
        ])
    @endif

</body>

</html>
