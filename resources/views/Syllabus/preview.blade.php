<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/preview.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Franklin:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tahoma&display=swap" rel="stylesheet">

    <title>Course Syllabus - {{ $syllabus->course->course_code }}</title>

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
        <div class="a4-subtitle">Republic of the Philippines</div>
        <div class="a4-title">CENTRAL LUZON STATE UNIVERSITY</div>
        <div class="a4-subtitle">Science City of Muñoz, Nueva Ecija</div>
        <div class="a4-subtitle">Office of the Vice President for Academic Affairs</div>
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
                    {{-- <div>a.</div> --}}
                    <div>a. Excellent service to humanity is our commitment.</div>
                </div>
                <div class="indent-level-1-5">
                    {{-- <div>b.</div> --}}
                    <div>b. We are committed to develop globally-competent and empowered human resources, and to generate
                        knowledge and technologies for inclusive societal development.</div>
                </div>
                <div class="indent-level-1-5">
                    {{-- <div>c.</div> --}}
                    <div>c. We are dedicated to uphold CLSU's core values and principles, comply with statutory and
                        regulatory standards and continuously improve the effectiveness of our quality management
                        systems.</div>
                </div>
                <div class="indent-level-1-5">
                    {{-- <div>d.</div> --}}
                    <div>d. Mahalaga ang inyong tinig upang higit na mapahusay ang kalidad ng aming paglilingkod.</div>
                </div>
            </div>
        </div>

        <div class="a4-section">
            <strong class="indent-level-1 title-numbered">5. Goals of the {{ $collegeName }}</strong>
            <div class="a4-list">
                @forelse ($collegeGoals as $goal)
                    <div class="indent-level-1-5">
                        {{-- <div></div> --}}
                        <div>{{ $goal->college_goals_code }}. {{ $goal->goal_text }}</div>
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
                        {{-- <div></div> --}}
                        <div>{{ $objective->dept_obj_code }}. {{ $objective->objective_text }}</div>
                    </div>
                @empty
                    <div class="indent-level-1-5">No department objectives found.</div>
                @endforelse
            </div>
        </div>

        <div class="a4-section">
            <h3 class="a4-section title-lettered">B. Program Information</h3>
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

            <strong class="title-numbered">2. Program Outcomes (POs) and its Relationship to the Program Educational Objectives (PEOs)</strong>
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

            <h3 class="a4-section title-lettered">C. Instructor Information</h3>
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

        <h3 class="a4-section title-lettered">D. Course Information</h3>
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

        <div class="landscape">
            <h3 class="a4-section title-numbered">9. Course Outcomes (COs) and Relationship to Program Outcomes</h3>

            <table>
                <thead>
                    <tr>
                        <th style="text-align:left;">Program Outcomes (Reference)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pos as $po)
                        <tr>
                            <td>{{ $po->po_code }} - {{ $po->po_text }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td style="text-align:center;">No program outcomes found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <table>
                <thead>
                    <tr>
                        <th style="text-align:left;">Course Outcomes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($courseOutcomes as $co)
                        <tr>
                            <td>{{ $co->co_code }}: {{ $co->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td style="text-align:center;">No course outcomes found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="portrait">
            <h3 class="a4-section title-numbered">Testing if portrait is working</h3>
        </div>

    </div>

    <div id="a4-container"></div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const source = document.getElementById("syllabus-content");
            const container = document.getElementById("a4-container");
            const pageCount = document.getElementById("page-count");
            const PAGE_HEIGHT = {
                portrait: 1122,
                landscape: 794
            };
            let currentOrientation = "portrait";
            let page = createNewPage(currentOrientation);
            container.appendChild(page);

            Array.from(source.children).forEach(element => {
                const hasLandscapeClass = element.classList?.contains("landscape");
                const hasPortraitClass = element.classList?.contains("portrait");
                const explicitOrientation = hasLandscapeClass ? "landscape" : (hasPortraitClass ? "portrait" : null);
                const targetOrientation = explicitOrientation ?? (currentOrientation === "landscape" ? "portrait" : "portrait");

                if (targetOrientation !== currentOrientation) {
                    if (page.children.length > 0) {
                        page = createNewPage(targetOrientation);
                        container.appendChild(page);
                    } else {
                        page.classList.remove("portrait", "landscape");
                        page.classList.add(targetOrientation);
                    }
                    currentOrientation = targetOrientation;
                }

                if (explicitOrientation) {
                    element.classList.remove("landscape", "portrait");
                }

                if (element.tagName === "TABLE") {
                    splitTable(element);
                } else {
                    appendElement(element);
                }
            });

            source.remove();
            addPageNumbers();

            function appendElement(el) {
                page.appendChild(el);

                if (page.scrollHeight > getCurrentPageMaxHeight()) {
                    page.removeChild(el);

                    if (['DIV', 'UL', 'OL', 'LI'].includes(el.tagName) && el.children.length > 0) {
                        splitContainer(el);
                    } else {
                        page = createNewPage(currentOrientation);
                        container.appendChild(page);
                        page.appendChild(el);
                    }
                }
            }

            function splitContainer(el) {
                if (!el.hasAttribute('data-split-id')) {
                    el.setAttribute('data-split-id', Math.random().toString(36).substr(2, 9));
                }

                function ensureWrapperPath(path) {
                    let current = page;
                    path.forEach(node => {
                        let wrapper = current.lastElementChild;
                        if (!wrapper || wrapper.getAttribute('data-split-id') !== node.getAttribute('data-split-id')) {
                            wrapper = node.cloneNode(false);
                            wrapper.removeAttribute('id');
                            wrapper.setAttribute('data-split-id', node.getAttribute('data-split-id'));
                            current.appendChild(wrapper);
                        }
                        current = wrapper;
                    });
                    return current;
                }

                function appendNodes(nodes, parentPath) {
                    nodes.forEach(child => {
                        let currentWrapper = ensureWrapperPath(parentPath);
                        currentWrapper.appendChild(child);

                        if (page.scrollHeight > getCurrentPageMaxHeight()) {
                            currentWrapper.removeChild(child);
                            if (!currentWrapper.hasChildNodes()) currentWrapper.parentNode.removeChild(currentWrapper);

                            if (child.nodeType === Node.ELEMENT_NODE && ['DIV', 'UL', 'OL', 'LI'].includes(child.tagName) && child.childNodes.length > 0) {
                                if (!child.hasAttribute('data-split-id')) {
                                    child.setAttribute('data-split-id', Math.random().toString(36).substr(2, 9));
                                }
                                appendNodes(Array.from(child.childNodes), [...parentPath, child]);
                            } else {
                                page = createNewPage(currentOrientation);
                                container.appendChild(page);
                                currentWrapper = ensureWrapperPath(parentPath);
                                currentWrapper.appendChild(child);
                            }
                        }
                    });
                }

                appendNodes(Array.from(el.childNodes), [el]);
                document.querySelectorAll('[data-split-id]').forEach(e => e.removeAttribute('data-split-id'));
            }

            function splitTable(table) {
                const thead = table.querySelector("thead");
                const rows = Array.from(table.querySelectorAll("tbody tr"));

                // If table has no body rows, append directly.
                if (!rows.length) {
                    appendElement(table);
                    return;
                }

                let newTable = createTableWithHeader(table, thead); // Include header only on the first page
                page.appendChild(newTable);

                rows.forEach(row => {
                    newTable.querySelector("tbody").appendChild(row);

                    if (page.scrollHeight > getCurrentPageMaxHeight()) {
                        newTable.querySelector("tbody").removeChild(row);

                        page = createNewPage(currentOrientation);
                        container.appendChild(page);

                        newTable = createTableWithHeader(table, thead); // Repeat header on each continued page
                        page.appendChild(newTable);

                        newTable.querySelector("tbody").appendChild(row);
                    }
                });
            }

            function createTableWithHeader(original, thead) {
                const table = document.createElement("table");
                table.className = original.className;
                table.style.width = "100%";
                table.style.borderCollapse = "collapse";

                if (thead) {
                    table.appendChild(thead.cloneNode(true));
                }

                const tbody = document.createElement("tbody");
                table.appendChild(tbody);

                return table;
            }

            function getCurrentPageMaxHeight() {
                return PAGE_HEIGHT[currentOrientation] ?? PAGE_HEIGHT.portrait;
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
                    footer.style.position = "absolute";
                    footer.style.bottom = "20px";
                    footer.style.left = "60px";
                    footer.style.right = "60px";
                    footer.style.borderTop = "1px solid #808080";
                    footer.style.paddingTop = "10px";
                    footer.style.textAlign = "right";
                    footer.style.fontSize = "10pt";
                    footer.style.color = "#808080";
                    footer.innerText = "Course Syllabus: {{ $syllabus->course->course_code }} | Page " + (
                        index + 1) + " of " + pages.length;
                    p.appendChild(footer);
                });

                if (pageCount) {
                    pageCount.textContent = pages.length + " page" + (pages.length !== 1 ? "s" : "");
                }
            }
        });
    </script>



</body>

</html>
