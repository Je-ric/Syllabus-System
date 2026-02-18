@extends('layouts.app')

@section('content')
    <style>
        @page {
            size: A4;
            margin: 12mm;
        }

        .a4-wrap {
            background: #f2f4f7;
            padding: 20px 0;
        }

        .a4-page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto 16px;
            background: #fff;
            color: #111827;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.15);
            padding: 18mm 16mm;
            font-size: 12px;
            line-height: 1.55;
        }

        .a4-title {
            text-align: center;
            font-weight: 700;
            letter-spacing: 0.04em;
        }

        .one-indent {
            display: block;
            padding-left: 40px;
        }

        .one1-indent {
            display: block;
            padding-left: 55px;
        }

        .between-indent {
            display: block;
            padding-left: 40px;
            text-indent: 20px;
        }

        .two-indent {
            display: block;
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
            margin-top: 14px;
        }

        .a4-section h3 {
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 6px;
            text-transform: uppercase;
            text-align: justify;
        }

        .a4-list {
            display: grid;
            gap: 6px;
        }

        .a4-row {
            display: grid;
            grid-template-columns: 40px 1fr;
            gap: 8px;
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

        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
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


        @media print {
            body {
                background: #fff !important;
            }

            .a4-wrap {
                padding: 0;
                background: #fff;
            }

            .a4-page {
                box-shadow: none;
                margin: 0;
                width: auto;
                min-height: auto;
                padding: 0;
            }
        }
    </style>

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
                    <strong class="one-indent">1. Vision of the University</strong>
                    <p class="two-indent">Central Luzon State University (CLSU) as a world-class National University
                        for science and technology in
                        agriculture and allied fields.</p>
                </div>

                <div class="a4-section">
                    <strong class="one-indent">2. Mission of the University</strong>
                    <p class="two-indent">CLSU shall develop globally competitive, work-ready, socially-responsible
                        and empowered human resources who
                        value life-long learning; and to generate, disseminate, and apply knowledge and technologies for
                        poverty
                        alleviation, environmental protection, and sustainable development.</p>
                </div>

                <div class="a4-section">
                    <strong class="one-indent">3. Educational Philosophy</strong>
                    <p class="two-indent">The Central Luzon State University is committed and dedicated to provide a
                        holistic transformative education anchored on
                        its mission statement and its institutional core values. As stated on its mission, the University
                        shall develop globally competitive, work-ready,
                        socially-responsible and empowered human resources who value life-long learning; and shall generate,
                        disseminate, and apply knowledge and technologies
                        for poverty alleviation, environmental protection and sustainable development.
                        In consonance, the educational philosophy of the University is reflective of its teaching and
                        learning environment.</p>
                    <p class="two-indent">Along with the curricular programs, the academic journey of learners
                        revolves on three value-laden dimensions:
                        creativeness and innovativeness, hard work and integrity, and inclusiveness and transformativeness.
                    </p>
                    <br>

                    <div>
                        <div class="between-indent">With these, the university shall:</div>
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
                    </p>
                </div>

                <div class="a4-section">
                    <strong class="one-indent">4. Quality Policy Statement</strong>
                    <p class="one1-indent">a.  Excellent service to humanity is our commitment.</p>
                    <p class="one1-indent">b.  We are committed to develop globally-competent and empowered human
                        resources, and to generate knowledge
                        and technologies for inclusive societal development.</p>
                    <p class="one1-indent">c.  We are dedicated to uphold CLSU’s core values and principles, comply with
                        statutory and regulatory
                        standards and continuously improve the effectiveness of our quality management systems.</p>
                    <p class="one1-indent">d.  Mahalaga ang inyong tinig upang higit na mapahusay ang kalidad ng aming
                        paglilingkod.</p>
                </div>

                <div class="a4-section">
                    <strong class="one-indent">5. Goals of the {{ $collegeName }}</strong>
                    <div class="a4-list">
                        @forelse ($collegeGoals as $goal)
                            <div class="a4-row">
                                <div>{{ $goal->college_goals_code }}.</div>
                                <div>{{ $goal->goal_text }}</div>
                            </div>
                        @empty
                            <div>No college goals found.</div>
                        @endforelse
                    </div>
                </div>

                <div class="a4-section">
                    <strong class="one-indent">6. Objectives of the {{ $departmentName }}</strong>
                    <div class="a4-list">
                        @forelse ($departmentObjectives as $objective)
                            <div class="a4-row">
                                <div>{{ $objective->dept_obj_code }}.</div>
                                <div>{{ $objective->objective_text }}</div>
                            </div>
                        @empty
                            <div>No department objectives found.</div>
                        @endforelse
                    </div>
                </div>

                <h3>B. Program Information</h3>
                <table>
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


                <div class="a4-section">
                    <strong class="title-indent">1. Program Educational Objectives (PEOs)</strong>

                    <table>
                        <tr>
                            <th>
                                <b>Program Educational Objectives</b><br>
                                Three to five years after graduation, the BSIT graduates are:
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


                <div class="a4-section">
                    <strong class="title-indent">
                        Program Outcomes and its Relationship to the Program Educational Objectives
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



                <h3>C. Instructor Information</h3>
                <table>
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


                <h3>D. Course Information</h3>
                <table>
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
@endsection
