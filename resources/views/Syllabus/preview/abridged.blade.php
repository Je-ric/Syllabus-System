<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @if (!empty($isSnapshot) && !empty($inlinePreviewCss))
        <style>{!! $inlinePreviewCss !!}</style>
    @else
        @vite(['resources/css/preview.css', 'resources/js/app.js'])
    @endif
    <title>Abridged Course Syllabus - {{ $syllabus->course->course_code }}</title>
</head>

<body>

    {{-- ── Toolbar ─────────────────────────────────────────────────────────── --}}
    <div id="toolbar">
        <div class="t-left">
            <span class="t-title">
                {{ $syllabus->course->course_code }} – {{ $syllabus->course->course_title }}
                <span style="font-weight:400; color:#94a3b8; font-size:11px; margin-left:6px;">Abridged</span>
            </span>
            <span class="t-pages" id="page-count"></span>
        </div>
        <button onclick="window.print()">Print / Save PDF</button>
    </div>

    {{-- ── Hidden source ───────────────────────────────────────────────────── --}}
    <div id="syllabus-content" style="display:none;">

        {{-- ═══════════════════════════════════════════════════════════════════
             LETTERHEAD
        ════════════════════════════════════════════════════════════════════ --}}
        <div style="display:grid; grid-template-columns:80px 1fr 80px;
                    align-items:center; column-gap:12px; margin-bottom:8px;">
            <div style="display:flex; justify-content:flex-start;">
                <img src="{{ (!empty($isSnapshot) && !empty($inlineLogoDataUri))
                        ? $inlineLogoDataUri
                        : asset('assets/clsu-logo-green.png') }}"
                    alt="CLSU Logo"
                    style="width:80px; height:auto;" />
            </div>
            <div style="text-align:center;">
                <div class="a4-subtitle">Republic of the Philippines</div>
                <div class="a4-title">CENTRAL LUZON STATE UNIVERSITY</div>
                <div class="a4-subtitle">Science City of Muñoz, Nueva Ecija</div>
                <div class="a4-subtitle">Office of the Vice President for Academic Affairs</div>
            </div>
            <div aria-hidden="true"></div>
        </div>

        <div class="a4-section a4-title" style="text-align:center;">ABRIDGED COURSE SYLLABUS</div>

        <div class="a4-section" style="font-style:italic; font-size:9pt; color:#444; line-height:1.5; border:1px solid #ccc; padding:8px 12px;">
            This resource is designed for students' use and serves as a supplementary guide. However, it is not
            intended to replace or substitute the official OBTL (Outcome-Based Teaching and Learning) syllabus
            format provided by the university. Students should refer to the official OBTL syllabus for
            comprehensive and accurate information regarding their courses and academic requirements.
        </div>

        {{-- ═══════════════════════════════════════════════════════════════════
             SECTION I — COURSE AND INSTRUCTOR INFORMATION
        ════════════════════════════════════════════════════════════════════ --}}
        <div class="a4-section">
            <h3 class="title-numbered" style="font-size:11pt; margin-bottom:6px;">
                I. Course and Instructor Information
            </h3>

            @php
                $hasLab = (bool) ($syllabus->course?->has_lec_lab);

                $lecLabValue = function ($lecValue, $labValue) use ($hasLab) {
                    if (! $hasLab) {
                        return ! blank($lecValue) ? e($lecValue) : 'N/A';
                    }
                    $lines = [];
                    if (! blank($lecValue)) { $lines[] = 'LEC: ' . e($lecValue); }
                    if (! blank($labValue)) { $lines[] = 'LAB: ' . e($labValue); }
                    return count($lines) ? implode('<br>', $lines) : 'N/A';
                };
            @endphp

            <table class="kv-table">
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
                        <td>{!! $lecLabValue($lecComponent?->schedule, $labComponent?->schedule) !!}</td>
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
                        <td>{!! $lecLabValue($lecComponent?->consultation_hours, $labComponent?->consultation_hours) !!}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════════
             SECTION II — COURSE OUTCOMES
        ════════════════════════════════════════════════════════════════════ --}}
        <div class="a4-section">
            <h3 class="title-numbered" style="font-size:11pt; margin-bottom:6px;">
                II. Course Outcomes
            </h3>

            <table border="1" style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="text-align:center; width:50px;">CO No.</th>
                        <th style="text-align:center;">Course Outcomes</th>
                        <th style="text-align:center; width:200px;">
                            Program Outcomes Addressed<br>
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
                            <td style="text-align:center;">
                                {{ $coPoLetterMap[$co->id] ?? '' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align:center;">No course outcomes found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════════
             SECTION III — COURSE CALENDAR (WEEKLY COVERAGE)
        ════════════════════════════════════════════════════════════════════ --}}
        <div class="a4-section">
            <h3 class="title-numbered" style="font-size:11pt; margin-bottom:6px;">
                III. Course Calendar
            </h3>

            {{-- Reusable weekly table macro for both LEC and LAB --}}
            @php
                $lecRows = $abridgedWeeklyRows['LEC'] ?? [];
                $labRows = $abridgedWeeklyRows['LAB'] ?? [];
            @endphp

            {{-- ── LEC table ──────────────────────────────────────────── --}}
            @if ($syllabus->course->has_lec_lab)
                <p style="margin:0 0 4px; font-size:9pt;"><strong>Lecture (LEC)</strong></p>
            @endif

            <table class="weekly-coverage-table" border="1" style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="text-align:center; width:50px;">CO No.</th>
                        <th style="text-align:center; width:70px;">Wk No.</th>
                        <th style="text-align:center;">Topics</th>
                        <th style="text-align:center;">Learning Activities</th>
                        <th style="text-align:center; width:120px;">Assessment</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($lecRows as $row)
                        @if ($row['is_exam'])
                            {{-- Exam: single full-width row, centered, bold, shaded --}}
                            <tr style="background:#f0f0f0;">
                                <td colspan="5"
                                    style="text-align:center; font-weight:bold; font-style:italic;">
                                    {{ $row['exam_label'] }}
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td style="text-align:center; vertical-align:top;">{{ $row['co_no'] }}</td>
                                <td style="text-align:center; vertical-align:top;">{{ $row['wk_label'] }}</td>
                                <td style="vertical-align:top;">{!! nl2br(e($row['topics'])) !!}</td>
                                <td style="vertical-align:top;">{!! nl2br(e($row['tla'])) !!}</td>
                                <td style="vertical-align:top;">{!! nl2br(e($row['assessment'])) !!}</td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center;">No weekly coverage found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- ── LAB table (LEC+LAB courses only) ───────────────────── --}}
            @if ($syllabus->course->has_lec_lab)
                <p style="margin:10px 0 4px; font-size:9pt;"><strong>Laboratory (LAB)</strong></p>

                <table class="weekly-coverage-table" border="1" style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th style="text-align:center; width:50px;">CO No.</th>
                            <th style="text-align:center; width:70px;">Wk No.</th>
                            <th style="text-align:center;">Topics</th>
                            <th style="text-align:center;">Learning Activities</th>
                            <th style="text-align:center; width:120px;">Assessment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($labRows as $row)
                        @if ($row['is_exam'])
                            {{-- Exam: single full-width row, centered, bold, shaded --}}
                            <tr style="background:#f0f0f0;">
                                <td colspan="5"
                                    style="text-align:center; font-weight:bold; font-style:italic;">
                                    {{ $row['exam_label'] }}
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td style="text-align:center; vertical-align:top;">{{ $row['co_no'] }}</td>
                                <td style="text-align:center; vertical-align:top;">{{ $row['wk_label'] }}</td>
                                <td style="vertical-align:top;">{!! nl2br(e($row['topics'])) !!}</td>
                                <td style="vertical-align:top;">{!! nl2br(e($row['tla'])) !!}</td>
                                <td style="vertical-align:top;">{!! nl2br(e($row['assessment'])) !!}</td>
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
        <div class="a4-section">
            <h3 class="title-numbered" style="font-size:11pt; margin-bottom:6px;">
                IV. Course Evaluation
            </h3>

            <p class="indent-level-1"><strong>a. Course Requirements</strong></p>
            <p class="indent-level-2">The student performance in this course will be rated based on the following:</p>

            <table border="1" style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th rowspan="2" style="text-align:center; width:70px;">CO</th>
                        <th colspan="2" style="text-align:center;">
                            {{ $syllabus->course->has_lec_lab ? 'LECTURE (67%)' : 'LECTURE' }}
                        </th>
                        @if ($syllabus->course->has_lec_lab)
                            <th colspan="2" style="text-align:center;">LABORATORY (33%)</th>
                        @endif
                        <th rowspan="2" style="text-align:center; width:110px;">Performance Standard</th>
                    </tr>
                    <tr>
                        <th style="text-align:center;">Assessment Task</th>
                        <th style="text-align:center; width:80px;">Weight (%)</th>
                        @if ($syllabus->course->has_lec_lab)
                            <th style="text-align:center;">Assessment Task</th>
                            <th style="text-align:center; width:80px;">Weight (%)</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php
                        $lecPassMark = $lecComponent?->performance_standard
                            ? (int) round((float) str_replace('%', '', (string) $lecComponent->performance_standard))
                            : 60;
                        $labPassMark = $labComponent?->performance_standard
                            ? (int) round((float) str_replace('%', '', (string) $labComponent->performance_standard))
                            : 60;
                    @endphp
                    @forelse ($evaluationRows as $row)
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
                                    <strong>{{ $lecPassMark }}%</strong>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $syllabus->course->has_lec_lab ? 6 : 4 }}"
                                style="text-align:center;">No evaluation items found.</td>
                        </tr>
                    @endforelse

                    <tr>
                        <td style="font-weight:bold;">Total</td>
                        <td></td>
                        <td style="text-align:center; font-weight:bold;">{{ $evaluationTotals['lec'] ?? '' }}%</td>
                        @if ($syllabus->course->has_lec_lab)
                            <td></td>
                            <td style="text-align:center; font-weight:bold;">{{ $evaluationTotals['lab'] ?? '' }}%</td>
                        @endif
                        <td style="text-align:center; font-weight:bold;">{{ $lecPassMark }}%</td>
                    </tr>
                </tbody>
            </table>

            <p style="margin-top:8px;"><strong>Minimum Average for Satisfactory Performance: {{ $lecPassMark }}%</strong></p>

            {{-- b. FCAS formula --}}
            <p style="margin-top:12px;" class="indent-level-1">
                <strong>b. Computation of Final Course Average Score (FCAS)</strong>
            </p>
            @if ($syllabus->course->has_lec_lab)
                <p class="indent-level-2"><strong>FCAS = (0.67) × LecAve + (0.33) × LabAve + APP</strong></p>
            @else
                <p class="indent-level-2"><strong>FCAS = LecAve + APP</strong></p>
            @endif

            {{-- c. Transmutation --}}
            <p style="margin-top:12px;" class="indent-level-1">
                <strong>c. Transmutation</strong>
            </p>
            <p class="indent-level-2">The final grades will correspond to the weighted average scores shown below:</p>

            <table border="1" style="width:100%; border-collapse:collapse; margin-top:4px;">
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
                        <td style="text-align:center;">95.60 – 100.00</td><td style="text-align:center;">1.00</td>
                        <td style="text-align:center;">77.80 – 82.24</td><td style="text-align:center;">2.00</td>
                        <td style="text-align:center;">60.00 – 64.44</td><td style="text-align:center;">3.00</td>
                    </tr>
                    <tr>
                        <td style="text-align:center;">91.15 – 95.59</td><td style="text-align:center;">1.25</td>
                        <td style="text-align:center;">73.35 – 77.79</td><td style="text-align:center;">2.25</td>
                        <td style="text-align:center;">Below 60</td><td style="text-align:center;">5.00</td>
                    </tr>
                    <tr>
                        <td style="text-align:center;">86.70 – 91.14</td><td style="text-align:center;">1.50</td>
                        <td style="text-align:center;">68.90 – 73.34</td><td style="text-align:center;">2.50</td>
                        <td></td><td></td>
                    </tr>
                    <tr>
                        <td style="text-align:center;">82.25 – 86.69</td><td style="text-align:center;">1.75</td>
                        <td style="text-align:center;">64.45 – 68.89</td><td style="text-align:center;">2.75</td>
                        <td></td><td></td>
                    </tr>
                </tbody>
            </table>
            <p class="indent-level-2" style="margin-top:4px;"><strong>Passing Mark: {{ $lecPassMark ?? 60 }}%</strong></p>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════════
             SECTION V — REQUIRED READING MATERIALS
        ════════════════════════════════════════════════════════════════════ --}}
        <div class="a4-section">
            <h3 class="title-numbered" style="font-size:11pt; margin-bottom:6px;">
                V. Required Reading Materials
            </h3>

            @if ($allReferences->isNotEmpty())
                <p class="indent-level-1" style="margin-bottom:4px;"><strong>Textbooks / eBooks</strong></p>
                <div class="a4-list">
                    @foreach ($allReferences as $ref)
                        <div class="indent-level-1-5">• {{ $ref }}</div>
                    @endforeach
                </div>
            @else
                <div class="indent-level-1-5" style="font-style:italic; color:#666;">
                    No references encoded.
                </div>
            @endif

            @if ($onlineMaterialLinks->isNotEmpty())
                <p class="indent-level-1" style="margin-top:10px; margin-bottom:4px;">
                    <strong>Online Materials</strong>
                </p>
                <div class="a4-list">
                    @foreach ($onlineMaterialLinks as $url)
                        @php
                            $link = \Illuminate\Support\Str::startsWith($url, ['http://', 'https://'])
                                ? $url : 'https://' . $url;
                        @endphp
                        <div class="indent-level-1-5">
                            <a href="{{ $link }}" target="_blank"
                               style="text-decoration:underline;">{{ $url }}</a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ═══════════════════════════════════════════════════════════════════
             PREPARED BY
        ════════════════════════════════════════════════════════════════════ --}}
        <div class="a4-section" style="margin-top:24px;">
            <p style="margin-bottom:32px;"><strong>Prepared by:</strong></p>
            <p style="border-top:1px solid #000; display:inline-block; padding-top:4px; min-width:220px;">
                @php
                    $preparedByName = collect($syllabus->components ?? [])
                        ->pluck('instructor_name')
                        ->filter()
                        ->unique()
                        ->implode(' / ')
                        ?: ($lecComponent?->instructor_name ?? 'N/A');
                @endphp
                {{ $preparedByName }}<br>
                <span style="font-size:8.5pt; color:#444;">Course Instructor / Professor</span>
            </p>
        </div>

    </div>{{-- /#syllabus-content --}}

    <div id="a4-container"></div>

    {{-- ── Pagination script (portrait-only, no landscape) ─────────────────── --}}
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const source    = document.getElementById("syllabus-content");
        const container = document.getElementById("a4-container");
        const pageCount = document.getElementById("page-count");

        const PAGE_HEIGHT = 978; // 1122px - 72px top - 72px bottom padding

        let page = createPage();
        container.appendChild(page);

        Array.from(source.children).forEach(element => {
            if (element.tagName === "TABLE") {
                splitTable(element);
            } else {
                appendElement(element);
            }
        });

        source.remove();
        addPageNumbers();

        // ── Helpers ────────────────────────────────────────────────────────────

        function appendElement(el) {
            page.appendChild(el);
            if (page.scrollHeight > getMaxHeight()) {
                page.removeChild(el);
                if (canSplit(el)) {
                    splitContainer(el);
                } else {
                    page = createPage();
                    container.appendChild(page);
                    page.appendChild(el);
                }
            }
        }

        function canSplit(el) {
            return el.nodeType === Node.ELEMENT_NODE &&
                   ["DIV","UL","OL","LI","SECTION"].includes(el.tagName) &&
                   el.childNodes.length > 0;
        }

        function getMaxHeight() {
            return page ? page.clientHeight : 1122;
        }

        function splitContainer(el) {
            if (!el.hasAttribute("data-split-id")) {
                el.setAttribute("data-split-id", Math.random().toString(36).substr(2,9));
            }

            function ensureWrapper(path) {
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
                    if (child.nodeType === Node.ELEMENT_NODE && child.tagName === "TABLE") {
                        splitTable(child, () => ensureWrapper(parentPath));
                        return;
                    }
                    let w = ensureWrapper(parentPath);
                    w.appendChild(child);
                    if (page.scrollHeight > getMaxHeight()) {
                        w.removeChild(child);
                        let ww = w;
                        while (ww && ww !== page && !ww.hasChildNodes()) {
                            const p = ww.parentNode;
                            if (p) p.removeChild(ww);
                            ww = p;
                        }
                        page = createPage();
                        container.appendChild(page);
                        if (child.nodeType === Node.ELEMENT_NODE && canSplit(child)) {
                            if (!child.hasAttribute("data-split-id")) {
                                child.setAttribute("data-split-id", Math.random().toString(36).substr(2,9));
                            }
                            appendNodes(Array.from(child.childNodes), [...parentPath, child]);
                        } else {
                            w = ensureWrapper(parentPath);
                            w.appendChild(child);
                        }
                    }
                });
            }

            appendNodes(Array.from(el.childNodes), [el]);
            document.querySelectorAll("[data-split-id]").forEach(e => e.removeAttribute("data-split-id"));
        }

        function splitTable(table, getTarget) {
            const thead = table.querySelector("thead");
            const rows  = Array.from(table.querySelectorAll("tbody tr"));

            if (!rows.length) { appendElement(table); return; }

            const target = typeof getTarget === "function" ? getTarget : () => page;

            let shell = makeShell(table, thead, true);
            target().appendChild(shell);

            if (page.scrollHeight > getMaxHeight()) {
                target().removeChild(shell);
                page = createPage();
                container.appendChild(page);
                shell = makeShell(table, thead, true);
                target().appendChild(shell);
            }

            rows.forEach(row => {
                shell.querySelector("tbody").appendChild(row);
                if (page.scrollHeight > getMaxHeight()) {
                    shell.querySelector("tbody").removeChild(row);
                    if (shell.querySelector("tbody").children.length === 0) {
                        shell.querySelector("tbody").appendChild(row);
                        return;
                    }
                    page = createPage();
                    container.appendChild(page);
                    shell = makeShell(table, thead, false);
                    target().appendChild(shell);
                    shell.querySelector("tbody").appendChild(row);
                }
            });
        }

        function makeShell(orig, thead, includeHeader) {
            const t = document.createElement("table");
            t.className        = orig.className;
            t.style.cssText    = orig.style.cssText;
            t.style.width      = "100%";
            t.style.borderCollapse = "collapse";
            t.setAttribute("border", orig.getAttribute("border") || "");

            // Preserve column widths across split-table continuations.
            // Multi-row headers: scan all rows, track rowspan/colspan, map col→width.
            const existingCg = orig.querySelector(":scope > colgroup");
            if (existingCg) {
                t.appendChild(existingCg.cloneNode(true));
            } else if (thead) {
                const hRows = Array.from(thead.querySelectorAll("tr"));
                if (hRows.length > 0) {
                    const totalCols = Array.from(hRows[0].cells)
                        .reduce((s, cell) => s + (parseInt(cell.getAttribute("colspan") || "1", 10)), 0);
                    const widthMap = new Array(totalCols).fill(null);
                    const occupied = new Array(totalCols).fill(0);
                    hRows.forEach(row => {
                        let ci = 0;
                        Array.from(row.cells).forEach(cell => {
                            while (ci < totalCols && occupied[ci] > 0) { occupied[ci]--; ci++; }
                            const cs = parseInt(cell.getAttribute("colspan") || "1", 10);
                            const rs = parseInt(cell.getAttribute("rowspan") || "1", 10);
                            const sw = cell.style.width;
                            const aw = cell.getAttribute("width");
                            let w = null;
                            if (sw && sw !== "") { w = sw; }
                            else if (aw && aw !== "") { w = /^\d+$/.test(aw) ? aw + "px" : aw; }
                            if (w && cs > 1) {
                                const m = w.match(/^([\d.]+)(px|%|mm)?$/);
                                if (m) w = (parseFloat(m[1]) / cs).toFixed(1) + (m[2] || "px");
                            }
                            for (let i = 0; i < cs; i++) {
                                if (ci + i < totalCols) {
                                    if (widthMap[ci + i] === null && w) widthMap[ci + i] = w;
                                    if (rs > 1) occupied[ci + i] = Math.max(occupied[ci + i], rs - 1);
                                }
                            }
                            ci += cs;
                        });
                    });
                    const cg = document.createElement("colgroup");
                    widthMap.forEach(w => { const col = document.createElement("col"); if (w) col.style.width = w; cg.appendChild(col); });
                    t.appendChild(cg);
                }
            }

            if (includeHeader && thead) t.appendChild(thead.cloneNode(true));
            t.appendChild(document.createElement("tbody"));
            return t;
        }

        function createPage() {
            const div = document.createElement("div");
            div.className = "a4-page portrait";
            return div;
        }

        function addPageNumbers() {
            const pages = document.querySelectorAll(".a4-page");
            pages.forEach((p, index) => {
                const footer = document.createElement("div");
                footer.style.cssText = [
                    "position:absolute","bottom:20px","left:60px","right:60px",
                    "border-top:1px solid #808080","padding-top:6px",
                    "text-align:right","font-size:9pt","color:#808080",
                    "font-family:Tahoma,sans-serif"
                ].join(";");
                footer.innerText =
                    "Abridged Course Syllabus: {{ $syllabus->course->course_code }} | Page " +
                    (index + 1) + " of " + pages.length;
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
