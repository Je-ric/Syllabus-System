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
            <span class="t-title">{{ $syllabus->course->course_code }} - {{ $syllabus->course->course_title }}</span>
            <span class="t-pages" id="page-count"></span>
        </div>
        <button onclick="window.print()">Print / Save PDF</button>
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
                <div class="a4-subtitle">Office of the Vice President for Academic Affairs</div>
            </div>
            <div aria-hidden="true"></div>
        </div>
        <div class="a4-section a4-title">COURSE SYLLABUS</div>
        <div class="a4-subtitle">{{ $syllabus->course->course_code }} - {{ $syllabus->course->course_title }}</div>

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
            <div class="a4-list">
                <div class="indent-level-1-5">
                    <div>a.&nbsp; Excellent service to humanity is our commitment.</div>
                </div>
                <div class="indent-level-1-5">
                    <div>b.&nbsp; We are committed to develop globally-competent and empowered human resources, and to
                        generate knowledge and technologies for inclusive societal development.</div>
                </div>
                <div class="indent-level-1-5">
                    <div>c.&nbsp; We are dedicated to uphold CLSU's core values and principles, comply with statutory
                        and
                        regulatory standards and continuously improve the effectiveness of our quality management
                        systems.</div>
                </div>
                <div class="indent-level-1-5">
                    <div>d.&nbsp; Mahalaga ang inyong tinig upang higit na mapahusay ang kalidad ng aming paglilingkod.
                    </div>
                </div>
            </div>
        </div>

        <div class="a4-section">
            <strong class="indent-level-1 title-numbered">5. Goals of the {{ $collegeName }}</strong>
            <div class="a4-list">
                @forelse ($collegeGoals as $goal)
                    <div class="indent-level-1-5">
                        <div>{{ $goal->college_goals_code }}.&nbsp; {{ $goal->goal_text }}</div>
                    </div>
                @empty
                    <div class="indent-level-1-5">No college goals found.</div>
                @endforelse
            </div>
        </div>

        <div class="a4-section">
            <strong class="indent-level-1 title-numbered">6. Objectives of the {{ $departmentName }}</strong>
            <div class="a4-list">
                @forelse ($departmentObjectives as $objective)
                    <div class="indent-level-1-5">
                        <div>{{ $objective->dept_obj_code }}.&nbsp; {{ $objective->objective_text }}</div>
                    </div>
                @empty
                    <div class="indent-level-1-5">No department objectives found.</div>
                @endforelse
            </div>
        </div>
        <br>

        <h3 class="title-lettered">B. Program Information</h3>

        <div class="a4-section">
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

        <div class="a4-section">
            <strong class="title-numbered">1. Program Educational Objectives (PEOs)</strong>
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
                            <td>{{ $peo->peo_code }}. {{ $peo->peo_text }}</td>
                            <td><i class="bx bx-check">&#10003;</i></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2">No PEOs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="a4-section">
            <strong class="title-numbered">2. Program Outcomes (POs) and its Relationship to the Program Educational
                Objectives (PEOs)</strong>
            <table>
                <thead>
                    <tr>
                        <th colspan="2">Program Outcomes</th>
                        <th colspan="{{ max($peos->count(), 1) }}">Program Educational Objectives</th>
                    </tr>
                    <tr>
                        <th colspan="2">By the time of graduation, students of the program have the ability to:</th>
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
                                <td>{!! $po->peos->contains('id', $peo->id) ? '&#10003;' : ' ' !!}</td>
                            @empty
                                <td>-</td>
                            @endforelse
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $peos->count() + 2 }}">No POs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="a4-section">
            <h3 class="title-lettered">C. Instructor Information</h3>
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
                        <td>{!! $lecLabValue($lecComponent?->consultation_hours, $labComponent?->consultation_hours) !!}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="a4-section">
            <h3 class="title-lettered">D. Course Information</h3>
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
                        <td>{!! $lecLabValue($lecComponent?->schedule, $labComponent?->schedule) !!}</td>
                    </tr>
                </tbody>
            </table>
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
                    {{-- Header Row 1 --}}
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
                                <strong>{{ $co->co_code }}:</strong>
                                {{ $co->description }}
                            </td>

                            @foreach ($pos as $po)
                                <td style="text-align:center;">
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

            @if ($syllabus->course->has_lec_lab)
                <p><strong>Lecture (LEC)</strong></p>
            @endif
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
                    @forelse (($weeklyCoverageRows['LEC'] ?? []) as $row)
                        <tr>
                            @if (($row['is_exam'] ?? false) === true)
                                <td style="text-align:center;">{{ $row['week_label'] }}</td>
                                <td colspan="5" style="text-align:center; font-weight:bold;">
                                    {{ $row['exam_label'] ?? 'Exam' }}
                                </td>
                            @else
                                <td style="text-align:center;">{{ $row['week_label'] }}</td>
                                <td>{{ $row['co_description'] ?? '' }}</td>
                                <td>{{ $row['topics'] ?? '' }}</td>
                                <td>{{ $row['learning_outcomes'] ?? '' }}</td>
                                <td>{{ $row['tla'] ?? '' }}</td>
                                <td style="text-align:center;">{{ $row['assessment_task'] ?? '' }}</td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;">No weekly coverage found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

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
                                    <td style="text-align:center;">{{ $row['week_label'] }}</td>
                                    <td colspan="5" style="text-align:center; font-weight:bold;">
                                        {{ $row['exam_label'] ?? 'Exam' }}
                                    </td>
                                @else
                                    <td style="text-align:center;">{{ $row['week_label'] }}</td>
                                    <td>{{ $row['co_description'] ?? '' }}</td>
                                    <td>{{ $row['topics'] ?? '' }}</td>
                                    <td>{{ $row['learning_outcomes'] ?? '' }}</td>
                                    <td>{{ $row['tla'] ?? '' }}</td>
                                    <td style="text-align:center;">{{ $row['assessment_task'] ?? '' }}</td>
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

        {{-- ============================================================
             SECTION 11: COURSE EVALUATION (portrait)
             ============================================================ --}}
        <div class="portrait">
            <h3 class="a4-section title-numbered">11. Course Evaluation</h3>

            {{-- a. Course Requirements --}}
            <p class="indent-level-1"><strong>a. Course Requirements</strong></p>
            <p class="indent-level-2">The student performance in this course will be rated based on the following:</p>

            <table border="1" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th rowspan="2" style="text-align:center; width: 90px;">CO</th>
                        <th colspan="2" style="text-align:center;">
                            {{ $syllabus->course->has_lec_lab ? 'LECTURE (67%)' : 'LECTURE' }}
                        </th>
                        @if ($syllabus->course->has_lec_lab)
                            <th colspan="2" style="text-align:center;">LABORATORY (33%)</th>
                        @endif
                        <th rowspan="2" style="text-align:center; width: 120px;">Performance Standard</th>
                    </tr>
                    <tr>
                        <th style="text-align:center;">Assessment Task</th>
                        <th style="text-align:center; width: 90px;">Weight (%)</th>
                        @if ($syllabus->course->has_lec_lab)
                            <th style="text-align:center;">Assessment Task</th>
                            <th style="text-align:center; width: 90px;">Weight (%)</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse (($evaluationRows ?? []) as $row)
                        <tr>
                            <td style="text-align:center;">{{ $row['co_label'] ?? '' }}</td>
                            <td>{{ $row['lec_task'] ?? '' }}</td>
                            <td style="text-align:center;">{{ $row['lec_weight'] ?? '' }}</td>
                            @if ($syllabus->course->has_lec_lab)
                                <td>{{ $row['lab_task'] ?? '' }}</td>
                                <td style="text-align:center;">{{ $row['lab_weight'] ?? '' }}</td>
                            @endif
                            <td style="text-align:center;">
                                @if (($row['is_exam'] ?? false) === true)
                                    <strong>60%</strong>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $syllabus->course->has_lec_lab ? 6 : 4 }}" style="text-align:center;">
                                No evaluation items found.
                            </td>
                        </tr>
                    @endforelse

                    <tr>
                        <td style="text-align:left; font-weight:bold;">Total</td>
                        <td></td>
                        <td style="text-align:center; font-weight:bold;">{{ $evaluationTotals['lec'] ?? '' }}%</td>
                        @if ($syllabus->course->has_lec_lab)
                            <td></td>
                            <td style="text-align:center; font-weight:bold;">{{ $evaluationTotals['lab'] ?? '' }}%
                            </td>
                        @endif
                        <td style="text-align:center; font-weight:bold;">60%</td>
                    </tr>
                </tbody>
            </table>

            <p style="margin-top: 10px; font-weight:bold;">Minimum Average for Satisfactory Performance</p>
            <p style="margin-top: 2px;">60%</p>

            {{-- b. Computation of Final Course Average Score (FCAS) --}}
            <p style="margin-top: 14px;" class="indent-level-1"><strong>b. Computation of Final Course Average Score
                    (FCAS)</strong></p>
            @if ($syllabus->course->has_lec_lab)
                <p class="indent-level-2">
                    <strong>FCAS = (0.67) × LecAve + (0.33) × LabAve + APP</strong>
                </p>
                <p class="indent-level-2">Where:</p>
                <div class="a4-list">
                    <div class="indent-level-2">FCAS &nbsp;= Final Course Average Score</div>
                    <div class="indent-level-2">LecAve = Lecture Average Score</div>
                    <div class="indent-level-2">LabAve = Laboratory Average Score</div>
                    <div class="indent-level-2">APP &nbsp;&nbsp;&nbsp;= Additional point incentive for student
                        athletes, performers and student delegates/representatives [CLSU BOR Resolution No. 32-09]</div>
                </div>
            @else
                <p class="indent-level-2">
                    <strong>FCAS = LecAve + APP</strong>
                </p>
                <p class="indent-level-2">Where:</p>
                <div class="a4-list">
                    <div class="indent-level-2">FCAS &nbsp;= Final Course Average Score</div>
                    <div class="indent-level-2">LecAve = Lecture Average Score</div>
                    <div class="indent-level-2">APP &nbsp;&nbsp;&nbsp;= Additional point incentive for student
                        athletes, performers and student delegates/representatives [CLSU BOR Resolution No. 32-09]</div>
                </div>
            @endif

            {{-- c. Transmutation --}}
            <p style="margin-top: 14px;" class="indent-level-1"><strong>c. Transmutation</strong></p>
            <p class="indent-level-2">The final grades will correspond to the weighted average scores shown below:</p>

            <table border="1" style="width:100%; border-collapse: collapse; margin-top: 6px;">
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
            <p style="margin-top: 4px;" class="indent-level-2"><strong>Passing Mark: 60%</strong></p>

            <h3 class="a4-section title-numbered">12. References</h3>

            <div class="a4-list">
                @forelse (($allReferences ?? []) as $refText)
                    <div class="indent-level-1-5">{{ $refText }}</div>
                @empty
                    <div class="indent-level-1-5">No references encoded.</div>
                @endforelse

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

            <h3 class="a4-section title-numbered">13. Course Materials Made Available</h3>
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

            <h3 class="a4-section title-numbered">14. Contribution of Course to Meeting the Professional Component</h3>
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

            {{-- 1. Life-long Learning Opportunities --}}
            <div class="a4-section">
                <strong class="indent-level-1 title-numbered">1. Life-long Learning Opportunities</strong>
                <p class="indent-level-2">
                    {{ $syllabus->lifelong_learning ?? 'The course offers lifelong learning in network design, troubleshooting, security, and emerging technologies, fostering expertise crucial for modern IT careers and adaptability in the ever-evolving tech landscape.' }}
                </p>
            </div>

            {{-- 2. Course Policies --}}
            <div class="a4-section">
                <strong class="indent-level-1 title-numbered">2. Course Policies</strong>
                <p class="indent-level-2">The policies to be implemented in this course are based on the approved
                    academic
                    policies indicated in the University Code or Student Handbook.</p>
            </div>

            {{-- 3. Ethics and Conduct --}}
            <div class="a4-section">
                <strong class="indent-level-1 title-numbered">3. Ethics and Conduct</strong>

                {{-- Class Conduct --}}
                <p class="indent-level-2"><strong>Class Conduct</strong></p>

                <p class="indent-level-2"><em>Class Preparation:</em></p>
                <p class="indent-level-2">It is essential that students come prepared to get the most out of a class
                    lecture and case discussions. Concepts covered in the class are easier to comprehend if a student
                    has completed assignments before coming to the class. Undivided commitment, attention and efforts
                    are key to better study and learning.</p>

                <p class="indent-level-2"><em>Class Attendance:</em></p>
                <p class="indent-level-2">Students must plan to maintain regular attendance in each course. They must
                    understand that regular attendance is essential for learning and maintaining good academic
                    performance. It is possible that a student may obtain an unofficially dropped grade of 5.00 if
                    he reaches a number of absences as indicated in the policy.</p>

                <p class="indent-level-2"><em>Class Participation:</em></p>
                <p class="indent-level-2">Class attendance is NOT class participation. It is essential that a student
                    come prepared to get the most out of a class lecture and case discussion. Material is generally
                    not easily comprehensible if a student has not completed assignments prior to class session.</p>

                <p class="indent-level-2"><em>Expected Study Habits:</em></p>
                <p class="indent-level-2">The coursework is based on continuous teacher-student interaction, class
                    discussions and student participation. The course instructors make assignments of reading
                    materials prior to their discussion/lecture in the class. It is essential for a student to go
                    through the reading material prior to the class in order to interact effectively with the
                    instructor and fellow students. Students must not only prepare assigned topics from the required
                    textbooks, recommended books, and supplementary material, but where necessary, will also search
                    for relevant material elsewhere.</p>

                {{-- Student Conduct --}}
                <p class="indent-level-2"><strong>Student Conduct</strong></p>

                <p class="indent-level-2"><em>Awareness of Institution Policies:</em></p>
                <p class="indent-level-2">It is the student's responsibility to keep informed about the College
                    policies and the deadlines for various activities. READING THE EMAIL COMMUNICATIONS will keep
                    student up to date on deadlines for various activities such as add/drop, withdraws, registration,
                    tuition fee payments, clearance from library and computer lab, etc.</p>

                <p class="indent-level-2"><em>Punctuality:</em></p>
                <p class="indent-level-2">Both students and instructors are expected to be in the class on time.
                    Students should be in the class before the arrival of instructor. The instructor may take roll
                    call at the beginning of the class. Late comers may be marked as absent.</p>

                <p class="indent-level-2"><em>Discipline:</em></p>
                <p class="indent-level-2">The College admits students assuming mature conduct on the part of students.
                    Regularity in class attendance, proper behavior in and out of the class rooms towards
                    instructors, colleagues and staff of the College are expected norms. Misbehavior towards faculty
                    members and staff may result in immediate separation from the program. Damage to Institution
                    property may result in appropriate penalty and/or in serious situations separation from the
                    program.</p>

                <p class="indent-level-2"><em>Etiquettes:</em></p>
                <p class="indent-level-2">Students are required to behave in a decent and socially acceptable manner
                    towards their teachers, college administration and fellow students. It is recommended that all
                    students use English as the medium of communication on the campus, in general, and during class
                    sessions in particular.</p>

                <p class="indent-level-2"><em>Meeting the Deadlines:</em></p>
                <p class="indent-level-2">Students are provided an "Academic Calendar" for the program which indicates
                    "Important Dates to Remember". Dates for assignment presentations for particular courses are
                    given in the course outline or handouts. It is in students' own interest that these dates are
                    strictly observed and deadlines met. No relaxation is provided after due dates.</p>

                <p class="indent-level-2"><em>Class Room Mannerisms:</em></p>
                <p class="indent-level-2">Students are expected to conduct themselves as professionals in class and
                    observe a disciplined atmosphere. Class attendance is taken regularly and may constitute a part
                    of the final course grade. Late arrival in the class distracts the instructor and other students.
                    Class participation by students is expected and strongly encouraged. However, to maintain class
                    discipline and effectiveness, it is recommended that a student asks permission from the
                    instructor before asking questions or entering a discussion.</p>

                {{-- Attending Online Classes --}}
                <p class="indent-level-2"><strong>Attending On-line Classes and Presentations</strong></p>
                <ul>
                    <li>Wear appropriate attire. See CLSU dress code for online meetings, classes, and online defenses
                        and similar activities.</li>
                    <li>Use appropriate background. A blank neat wall, well-organized book shelves, or clean office
                        environment is acceptable. Avoid backgrounds that may show personal spaces such as bedrooms
                        or kitchens unless it is part of the topic.</li>
                    <li>As much as possible, say your name before you speak for the first time.</li>
                    <li>Always mute your microphone when it is not your turn to speak.</li>
                    <li>Often make eye contact with the camera. An appropriate photo of oneself may be used if there
                        is no web camera.</li>
                    <li>When appropriate, disable the audio announcement feature when someone exits or enters the
                        meeting.</li>
                    <li>Do not dominate the meeting by asking too many questions or providing too many responses.
                        Give others a chance to speak.</li>
                    <li>Be patient and expect technical issues to arise.</li>
                    <li>Give the meeting your full attention. Avoid multi-tasking and distracting gestures.</li>
                    <li>When posting messages on message boards, chat boxes, or sending emails, observe the University
                        guidelines for this purpose.</li>
                </ul>

                {{-- Dress Code --}}
                <p class="indent-level-2"><em>Dress Code (online and face-to-face)</em></p>
                <p class="indent-level-2">The students are required to be reasonably well dressed when coming to the
                    university. The dress should conform to the academic environment and should be decent in
                    appearance.</p>
                <p class="indent-level-2"><em>Meetings and classes:</em> Be well-groomed. Wear decent, simple or
                    casual comfortable attire. Upper garment should be ample enough to cover the upper body. If the
                    lower half of the body will be shown, no skinny pants, boxer shorts, short shorts, or short
                    skirts.</p>
                <p class="indent-level-2"><em>For presentations:</em> Wear corporate, formal, or smart casual attire.
                    Do not hide inappropriate clothing with virtual garments.</p>

                {{-- Mobile Phones --}}
                <p class="indent-level-2"><em>Mobile Phones:</em></p>
                <p class="indent-level-2">Mobile phones must be switched off when attending classes, library,
                    computer lab, and offices.</p>
            </div>

            {{-- 4. Academic Integrity --}}
            <div class="a4-section">
                <strong class="indent-level-1 title-numbered">4. Academic Integrity</strong>
                <p class="indent-level-2">Maintaining academic integrity is essential for fostering learning,
                    assuring fair evaluation, and upholding the credibility and quality of education. Policies on
                    academic integrity are as follows:</p>
                <div class="a4-list">
                    <div class="indent-level-1-5">a. All are expected to be honest in all of their academic
                        activities, including assignments, assessments, research, and other projects.</div>
                    <div class="indent-level-1-5">b. The use of another person's work without giving proper credit is
                        known as plagiarism. In order to properly credit sources, proper citation and reference are
                        required.</div>
                    <div class="indent-level-1-5">c. Individual tasks and exams must be completed by each student on
                        their own, without assistance from others.</div>
                </div>
                <p class="indent-level-2">Any documented case of dishonesty will be dealt with accordingly based on
                    the guidelines stipulated in the CLSU Code of Student Conduct and Discipline.</p>
            </div>

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
                                style="font-style:italic; font-size:9pt; color:#555;
                                               padding:4px 6px; background:#f8f8f8;">
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
            const pageCount = document.getElementById("page-count");

            // Usable content dimensions in px (page size minus padding)
            // Portrait:  794 - 72 - 72 = 650 px tall content area
            // Landscape: 794 - 56 - 56 = 682 px tall content area
            const CONTENT_HEIGHT = {
                portrait: 978, // 1122 - 72top - 72bottom
                landscape: 682, //  794 - 56top - 56bottom
            };

            let currentOrientation = "portrait";
            let page = createNewPage(currentOrientation);
            container.appendChild(page);

            // ── Process each top-level child of #syllabus-content ──────────────────
            Array.from(source.children).forEach(element => {
                const wantsLandscape = element.classList.contains("landscape");
                const wantsPortrait = element.classList.contains("portrait");

                let targetOrientation;
                if (wantsLandscape) targetOrientation = "landscape";
                else if (wantsPortrait) targetOrientation = "portrait";
                else targetOrientation = currentOrientation;

                // FIX 1: Orientation-tagged elements ALWAYS start a new page.
                if (wantsLandscape || wantsPortrait) {
                    // Only start a new page if the current one isn't already empty
                    // AND the orientation is changing (or it's a fresh section break)
                    if (pageHasContent(page) || targetOrientation !== currentOrientation) {
                        page = createNewPage(targetOrientation);
                        container.appendChild(page);
                    } else {
                        // Empty page but wrong orientation — just reclassify it
                        page.classList.remove("portrait", "landscape");
                        page.classList.add(targetOrientation);
                    }
                    currentOrientation = targetOrientation;
                    // Remove orientation classes so content isn't double-styled
                    element.classList.remove("landscape", "portrait");
                } else if (targetOrientation !== currentOrientation) {
                    page = createNewPage(targetOrientation);
                    container.appendChild(page);
                    currentOrientation = targetOrientation;
                }

                // Dispatch by element type
                if (element.tagName === "TABLE") {
                    splitTable(element);
                } else {
                    appendElement(element);
                }
            });

            source.remove();
            addPageNumbers();

            // ── Helpers ─────────────────────────────────────────────────────────────

            // True if the page already contains any element nodes.
            // (scrollHeight/clientHeight is unreliable because scrollHeight can equal clientHeight
            // even when the page contains content that doesn't overflow.)
            function pageHasContent(p) {
                return !!(p && p.children && p.children.length > 0);
            }

            // Measure actual used height inside a page (excludes padding via scrollHeight vs clientHeight gap)
            function pageContentHeight(p) {
                return p.scrollHeight - p.clientHeight;
            }

            // FIX 2 & 4: appendElement — use scrollHeight vs a stored baseline,
            // NOT the entire page height as the limit.
            function appendElement(el) {
                page.appendChild(el);

                if (page.scrollHeight > getMaxPageHeight()) {
                    page.removeChild(el);

                    // Try splitting containers before giving up and starting a new page
                    if (canSplit(el)) {
                        splitContainer(el);
                    } else {
                        page = createNewPage(currentOrientation);
                        container.appendChild(page);
                        page.appendChild(el);
                    }
                }
            }

            function canSplit(el) {
                return (
                    el.nodeType === Node.ELEMENT_NODE && ["DIV", "UL", "OL", "LI", "SECTION"].includes(el
                        .tagName) &&
                    el.childNodes.length > 0
                );
            }

            function getMaxPageHeight() {
                // Use the rendered page height so pagination stays correct in both
                // on-screen preview and browser print preview.
                return page ? page.clientHeight : (currentOrientation === "landscape" ? 794 : 1122);
            }

            // FIX 3 & 4: splitContainer — recursively splits a container element
            // across pages, placing as much as fits on the current page first.
            function splitContainer(el) {
                if (!el.hasAttribute("data-split-id")) {
                    el.setAttribute("data-split-id", Math.random().toString(36).substr(2, 9));
                }

                function ensureWrapperPath(path) {
                    let current = page;
                    path.forEach(node => {
                        let wrapper = current.lastElementChild;
                        const id = node.getAttribute("data-split-id");
                        if (!wrapper || wrapper.getAttribute("data-split-id") !== id) {
                            wrapper = node.cloneNode(false);
                            wrapper.removeAttribute("id");
                            wrapper.setAttribute("data-split-id", id);
                            current.appendChild(wrapper);
                        }
                        current = wrapper;
                    });
                    return current;
                }

                function appendNodes(nodes, parentPath) {
                    nodes.forEach(child => {
                        // Special-case tables: split them across pages instead of moving the whole table.
                        if (child.nodeType === Node.ELEMENT_NODE && child.tagName === "TABLE") {
                            splitTable(child, () => ensureWrapperPath(parentPath));
                            return;
                        }

                        let currentWrapper = ensureWrapperPath(parentPath);
                        currentWrapper.appendChild(child);

                        if (page.scrollHeight > getMaxPageHeight()) {
                            currentWrapper.removeChild(child);

                            // Clean up empty wrappers
                            let w = currentWrapper;
                            while (w && w !== page && !w.hasChildNodes()) {
                                const parent = w.parentNode;
                                if (parent) parent.removeChild(w);
                                w = parent;
                            }

                            // Start new page
                            page = createNewPage(currentOrientation);
                            container.appendChild(page);

                            // Recurse into child if splittable, otherwise place whole
                            if (child.nodeType === Node.ELEMENT_NODE && canSplit(child)) {
                                if (!child.hasAttribute("data-split-id")) {
                                    child.setAttribute("data-split-id", Math.random().toString(36).substr(2,
                                        9));
                                }
                                appendNodes(Array.from(child.childNodes), [...parentPath, child]);
                            } else {
                                currentWrapper = ensureWrapperPath(parentPath);
                                currentWrapper.appendChild(child);
                            }
                        }
                    });
                }

                appendNodes(Array.from(el.childNodes), [el]);

                // Clean up split tracking attributes
                document.querySelectorAll("[data-split-id]").forEach(e =>
                    e.removeAttribute("data-split-id")
                );
            }

            // FIX 3: splitTable — fill the current page first, then continue on next pages.
            // Tables continue across pages WITHOUT repeating the header (cleaner for long tables).
            function splitTable(table, getAppendTarget) {
                const thead = table.querySelector("thead");
                const rows = Array.from(table.querySelectorAll("tbody tr"));

                if (!rows.length) {
                    appendElement(table);
                    return;
                }

                const getTarget = (typeof getAppendTarget === "function") ?
                    getAppendTarget :
                    () => page;

                let target = getTarget();
                const targetWasEmpty = !(target && target.children && target.children.length > 0);

                let shell = createTableShell(table, thead, true);
                target.appendChild(shell);

                // If the table header alone doesn't fit (because the page already has content),
                // move the entire table to a fresh page before adding rows.
                if (!targetWasEmpty && page.scrollHeight > getMaxPageHeight()) {
                    target.removeChild(shell);
                    page = createNewPage(currentOrientation);
                    container.appendChild(page);
                    shell = createTableShell(table, thead, true);
                    target = getTarget();
                    target.appendChild(shell);
                }

                rows.forEach(row => {
                    shell.querySelector("tbody").appendChild(row);

                    if (page.scrollHeight > getMaxPageHeight()) {
                        shell.querySelector("tbody").removeChild(row);

                        // Don't start a new page if shell is empty (very tall single row edge case)
                        if (shell.querySelector("tbody").children.length === 0) {
                            // Row is taller than a full page — just put it anyway to avoid infinite loop
                            shell.querySelector("tbody").appendChild(row);
                            return;
                        }

                        page = createNewPage(currentOrientation);
                        container.appendChild(page);

                        // FIX 2: Continuation table — no header repeat, starts from top of new page
                        target = getTarget();
                        shell = createTableShell(table, thead, false);
                        target.appendChild(shell);
                        shell.querySelector("tbody").appendChild(row);
                    }
                });
            }

            function createTableShell(original, thead, includeHeader) {
                const table = document.createElement("table");

                // Copy class names and inline styles so weekly-coverage-table etc. carry over
                table.className = original.className;
                table.style.cssText = original.style.cssText;
                // FIX 2: Landscape tables must not exceed the content width.
                // The CSS already constrains this via padding, but set width explicitly.
                table.style.width = "100%";
                table.style.borderCollapse = "collapse";
                table.setAttribute("border", original.getAttribute("border") || "");

                if (includeHeader && thead) {
                    table.appendChild(thead.cloneNode(true));
                }

                const tbody = document.createElement("tbody");
                table.appendChild(tbody);
                return table;
            }

            function createNewPage(orientation = "portrait") {
                const div = document.createElement("div");
                div.className = "a4-page " + orientation;
                return div;
            }

            function addPageNumbers() {
                const pages = document.querySelectorAll(".a4-page");

                pages.forEach((p, index) => {
                    const footer = document.createElement("div");
                    footer.style.cssText = [
                        "position: absolute",
                        "bottom: 20px",
                        "left: 60px",
                        "right: 60px",
                        "border-top: 1px solid #808080",
                        "padding-top: 6px",
                        "text-align: right",
                        "font-size: 9pt",
                        "color: #808080",
                        "font-family: Tahoma, sans-serif",
                    ].join(";");
                    // Blade renders this as a literal string in the output HTML
                    footer.innerText =
                        "Course Syllabus: {{ $syllabus->course->course_code }} | Page " +
                        (index + 1) + " of " + pages.length;
                    p.appendChild(footer);
                });

                if (pageCount) {
                    pageCount.textContent =
                        pages.length + " page" + (pages.length !== 1 ? "s" : "");
                }
            }
        });
    </script>

</body>

</html>
