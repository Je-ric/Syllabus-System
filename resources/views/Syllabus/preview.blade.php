<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Syllabus - {{ $syllabus->course->course_code }}</title>
    <style>
        @page {
            size: A4;
            margin: 12mm 12mm 16mm 12mm;
        }

        body {
            margin: 0;
            font-family: Tahoma;
            color: #111827;
            font-size: 12px;
            line-height: 1.55;
            background: #f2f4f7;
        }

        .screen-toolbar {
            width: 210mm;
            margin: 12px auto 0;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        .screen-btn {
            border: 1px solid #111827;
            background: #fff;
            color: #111827;
            padding: 6px 10px;
            font-size: 12px;
            cursor: pointer;
            text-decoration: none;
        }

        .a4-wrap {
            padding: 12px 0 20px;
        }

        .a4-page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 18mm 16mm;
            background: #fff;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
        }

        .a4-title {
            text-align: center;
            font-weight: 700;
            letter-spacing: 0.04em;
        }

        .indent-level-1,
        .indent-level-1-5,
        .indent-level-1-5-text,
        .indent-level-2 {
            display: block;
        }

        .indent-level-1 {
            padding-left: 40px;
        }

        .indent-level-1-5 {
            padding-left: 55px;
        }

        .indent-level-1-5-text {
            padding-left: 40px;
            text-indent: 20px;
        }

        .indent-level-2 {
            padding-left: 60px;
            text-indent: 40px;
        }

        .a4-subtitle {
            text-align: center;
            font-size: 12px;
            color: #4b5563;
            margin-top: 2px;
        }

        .a4-section {
            margin-top: 10px;
        }

        .a4-section h3 {
            font-size: 12px;
            font-weight: 700;
            margin: 0 0 6px;
            text-transform: uppercase;
            text-align: justify;
        }

        .a4-list {
            display: grid;
            gap: 4px;
        }

        .a4-row {
            display: grid;
            grid-template-columns: 20px 1fr;
            gap: 6px;
        }

        .a4-code {
            font-weight: 700;
            color: #1f2937;
        }

        ul {
            padding-left: 80px;
        }

        ul li {
            list-style-type: disc;
            list-style-position: outside;
            padding-left: 8px;
        }

        p {
            margin: 4px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            margin-top: 4px;
        }

        table td {
            border: 1px solid #000;
            padding: 4px;
        }

        table th {
            border: 1px solid #000;
            padding: 4px;
            background: #d9d9d9;
        }

        .kv-table td:first-child {
            width: 5%;
            white-space: nowrap;
            vertical-align: top;
        }

        .print-block {
            break-inside: avoid-page;
            page-break-inside: avoid;
        }

        .print-break-before {
            break-before: page;
            page-break-before: always;
        }

        .print-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            border-top: 1px solid #000;
            padding-top: 2mm;
            text-align: right;
            font-size: 10px;
            color: #111827;
            background: #fff;
        }

        .print-footer .page-number::after {
            content: counter(page);
        }

        @media print {
            body {
                background: #fff;
            }

            .screen-toolbar {
                display: none !important;
            }

            .a4-wrap {
                padding: 0;
            }

            .a4-page {
                width: auto;
                min-height: auto;
                margin: 0;
                padding: 0;
                box-shadow: none;
                background: transparent;
            }
        }
    </style>
</head>
<body>
    <div class="screen-toolbar">
        <button type="button" class="screen-btn" onclick="window.print()">Print</button>
    </div>

    <div class="a4-wrap">
        <div class="a4-page">
            @php
                $lecLabValue = function ($lecValue, $labValue) {
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

            <div class="a4-subtitle">Republic of the Philippines</div>
            <div class="a4-title">CENTRAL LUZON STATE UNIVERSITY</div>
            <div class="a4-subtitle">Science City of Muñoz, Nueva Ecija</div>
            <div class="a4-subtitle">Office of the Vice President for Academic Affairs</div>

            <div class="a4-section a4-title">COURSE SYLLABUS</div>
            <div class="a4-subtitle">
                {{ $syllabus->course->course_code }} - {{ $syllabus->course->course_title }}
            </div>

            <div class="a4-section">
                <h3>A. University Information</h3>

                <div class="a4-section">
                    <strong class="indent-level-1">1. Vision of the University</strong>
                    <p class="indent-level-2">Central Luzon State University (CLSU) as a world-class National University
                        for science and technology in
                        agriculture and allied fields.</p>
                </div>

                <div class="a4-section">
                    <strong class="indent-level-1">2. Mission of the University</strong>
                    <p class="indent-level-2">CLSU shall develop globally competitive, work-ready, socially-responsible
                        and empowered human resources who
                        value life-long learning; and to generate, disseminate, and apply knowledge and technologies for
                        poverty
                        alleviation, environmental protection, and sustainable development.</p>
                </div>

                <div class="a4-section">
                    <strong class="indent-level-1">3. Educational Philosophy</strong>
                    <p class="indent-level-2">The Central Luzon State University is committed and dedicated to provide a
                        holistic transformative education anchored on
                        its mission statement and its institutional core values. As stated on its mission, the University
                        shall develop globally competitive, work-ready,
                        socially-responsible and empowered human resources who value life-long learning; and shall generate,
                        disseminate, and apply knowledge and technologies
                        for poverty alleviation, environmental protection and sustainable development.
                        In consonance, the educational philosophy of the University is reflective of its teaching and
                        learning environment.</p>
                    <p class="indent-level-2">Along with the curricular programs, the academic journey of learners
                        revolves on three value-laden dimensions:
                        creativeness and innovativeness, hard work and integrity, and inclusiveness and transformativeness.
                    </p>
                    <br>

                    <div>
                        <div class="indent-level-1-5-text">With these, the university shall:</div>
                        <ul>
                            <li>Provide a teaching and learning environment which harness creativity and innovativeness among
                                learners.
                                It advocates the development of individuals to become agents of change, innovators and leaders,
                                imbued with an
                                outward and forward-thinking perspective in their respective fields. It further ensures the
                                vital role of research
                                in promoting quality and excellence. Thus, regular updating of curricular programs, empowerment
                                of human capital,
                                modernizing instructional and pedagogical resources, and equal opportunity for all, are always
                                observed.</li>
                            <li>
                                Adopt experiential learning on its programs along with the dynamic and continuous engagement
                                between the faculty, the staff, the students and the community. The shared values of hard work
                                and integrity puts forth in the discovery of new knowledge and in its application in real-life
                                contexts. Thus, enabling and preparing the learners to be effective and efficient navigators of
                                the future.
                            </li>
                            <li>
                                Provide experiences that enable learners to discover the fulfillment of embracing diversity in
                                the form of various academic collaborations at the local, regional and international levels.
                                Students are guided to acknowledge and respect peoples and their cultures for inclusive societal
                                transformation.
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="a4-section">
                    <strong class="indent-level-1">4. Quality Policy Statement</strong>
                    <div class="a4-list">
                        <div class="a4-row indent-level-1-5">
                            <div>a.</div>
                            <div>Excellent service to humanity is our commitment.</div>
                        </div>
                        <div class="a4-row indent-level-1-5">
                            <div>b.</div>
                            <div>We are committed to develop globally-competent and empowered human resources, and to generate knowledge and technologies for inclusive societal development.</div>
                        </div>
                        <div class="a4-row indent-level-1-5">
                            <div>c.</div>
                            <div>We are dedicated to uphold CLSU’s core values and principles, comply with statutory and regulatory standards and continuously improve the effectiveness of our quality management systems.</div>
                        </div>
                        <div class="a4-row indent-level-1-5">
                            <div>d.</div>
                            <div>Mahalaga ang inyong tinig upang higit na mapahusay ang kalidad ng aming paglilingkod.</div>
                        </div>
                    </div>
                </div>

                <div class="a4-section">
                    <strong class="indent-level-1">5. Goals of the {{ $collegeName }}</strong>
                    <div class="a4-list">
                        @forelse ($collegeGoals as $goal)
                            <div class="a4-row indent-level-1-5">
                                <div>{{ $goal->college_goals_code }}.</div>
                                <div>{{ $goal->goal_text }}</div>
                            </div>
                        @empty
                            <div class="indent-level-1-5">No college goals found.</div>
                        @endforelse
                    </div>
                </div>

                <div class="a4-section">
                    <strong class="indent-level-1">6. Objectives of the {{ $departmentName }}</strong>
                    <div class="a4-list">
                        @forelse ($departmentObjectives as $objective)
                            <div class="a4-row indent-level-1-5">
                                <div>{{ $objective->dept_obj_code }}.</div>
                                <div>{{ $objective->objective_text }}</div>
                            </div>
                        @empty
                            <div class="indent-level-1-5">No department objectives found.</div>
                        @endforelse
                    </div>
                </div>

                <h3>B. Program Information</h3>
                <div class="indent-level-1 print-block">
                    <table class="kv-table">
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
                    </table>
                </div>


                <div class="a4-section indent-level-1 print-block">
                    <strong>1. Program Educational Objectives (PEOs)</strong>

                    <table>
                        <tr>
                            <th>
                                <b>Program Educational Objectives</b>
                                <br>
                                <p>Three to five years after graduation, the BSIT graduates are:</p>
                            </th>
                            <th>Mission</th>
                        </tr>

                        @forelse ($peos as $peo)
                            <tr>
                                <td>{{ $peo->peo_code }}. {{ $peo->peo_text }}</td>
                                <td><i class="bx bx-check"></i></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2">No PEOs found.</td>
                            </tr>
                        @endforelse
                    </table>
                </div>


                <div class="a4-section indent-level-1 print-block">
                    <strong>
                        2. Program Outcomes (POs) and its Relationship to the Program Educational Objectives (PEOs)
                    </strong>

                    <table>
                        <tr>
                            <th colspan="2">Program Outcomes</th>
                            <th colspan="{{ max($peos->count(), 1) }}">
                                Program Educational Objectives
                            </th>
                        </tr>

                        <tr>
                            <th colspan="2">
                                By the time of graduation, students of the program have the ability to:
                            </th>

                            @forelse ($peos as $peo)
                                <th>{{ $peo->peo_code }}</th>
                            @empty
                                <th>-</th>
                            @endforelse
                        </tr>

                        @forelse ($pos as $po)
                            <tr>
                                <td>({{ $po->po_code }})</td>
                                <td>{{ $po->po_text }}</td>

                                @forelse ($peos as $peo)
                                    <td>
                                        {!! $po->peos->contains('id', $peo->id) ? '&#10003;' : '' !!}
                                        {{-- means if that PO has that PEO, then checkmark. --}}
                                    </td>
                                @empty
                                    <td>-</td>
                                @endforelse
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $peos->count() + 1 }}">
                                    No POs found.
                                </td>
                            </tr>
                        @endforelse
                    </table>
                </div>

                <h3 class="print-break-before">C. Instructor Information</h3>
                <div class="indent-level-1 print-block">
                    <table class="kv-table">
                        <tr>
                            <td>1. Name of Instructor/Professor</td>
                            <td>{!! $lecLabValue($lecComponent?->instructor_name, $labComponent?->instructor_name) !!}</td>
                        </tr>
                        <tr>
                            <td>2. Office</td>
                            <td>{!! $lecLabValue($lecComponent?->office, $labComponent?->office) !!}</td>
                        </tr>
                        <tr>
                            <td>3.Phone No. (Optional)</td>
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
                    </table>
                </div>


                <h3>D. Course Information</h3>
                <div class="indent-level-1 print-block">
                    <table class="kv-table">
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
                            <td>4. Pre-requisite </td>
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
                    </table>
                </div>
            </div>

        </div>
    </div>

    <div class="print-footer">
        Course Syllabus: {{ $syllabus->course->course_code }} | Page <span class="page-number"></span>
    </div>
</body>
</html>
