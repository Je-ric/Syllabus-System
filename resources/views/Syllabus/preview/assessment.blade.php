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
    <title>Course Evaluation - {{ $syllabus->course->course_code }}</title>
</head>

<body>

    <div id="toolbar">
        <div class="t-left">
            <span class="t-title">
                {{ $syllabus->course->course_code }} – {{ $syllabus->course->course_title }}
            </span>
        </div>

        <div class="t-center">
            <span class="t-center-label">Assessment Plan</span>
            <p class="t-center-divider">–</p>
            <span class="t-pages" id="page-count"></span>
        </div>

        @if (empty($isSnapshot))
            <div class="t-right">
                <button type="button"
                    onclick="window.location.href='{{ route('syllabus.preview.complete', $syllabus) }}'">
                    Complete
                </button>
                <button type="button"
                    onclick="window.location.href='{{ route('syllabus.preview.abridged', $syllabus) }}'">
                    Abridged
                </button>
                <button type="button" class="is-active"
                    onclick="window.location.href='{{ route('syllabus.preview.assessment', $syllabus) }}'">
                    Assessment Plan
                </button>
                <button type="button" onclick="openSyllabusVersions()">Versions</button>
                <button type="button" onclick="window.print()">Print / Save PDF</button>
            </div>
        @else
            <button type="button" onclick="window.print()">Print / Save PDF</button>
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

        <div class="a4-section a4-title">COURSE EVALUATION</div>
        <br>
        <div class="a4-subtitle a4-title">{{ $syllabus->course->course_code }} - {{ $syllabus->course->course_title }}</div>

        {{-- ═══════════════════════════════════════════════════════════════════
             COURSE EVALUATION — a, b, c
        ════════════════════════════════════════════════════════════════════ --}}
        <div class="portrait">
            <h3 class="a4-section title-numbered">Course Evaluation</h3>
            <br>

            {{-- a. Course Requirements --}}
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
                            <td style="text-align:center; font-weight:bold;">{{ $evaluationTotals['lec'] ?? '' }}%</td>
                            @if ($syllabus->course->has_lec_lab)
                                <td></td>
                                <td style="text-align:center; font-weight:bold;">{{ $evaluationTotals['lab'] ?? '' }}%</td>
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

            {{-- b. Computation of FCAS --}}
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

                {{-- c. Transmutation --}}
                <p style="margin-top:14px;"><strong>c. Transmutation</strong></p>
                <p class="indent-level-2">The final grades will correspond to the weighted average scores shown below:</p>
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

            {{-- ── Prepared by ──────────────────────────────────────────────── --}}
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
                el.classList.remove("portrait");
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
                        while (ci < totalCols && occupied[ci] > 0) { occupied[ci]--; ci++; }
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
                        "Course Evaluation: {{ $syllabus->course->course_code }} | Page " +
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
            'previewVariant' => $previewVariant ?? 'assessment',
            'activeSavedVersion' => $activeSavedVersion ?? null,
            'openButton' => 'none',
        ])
    @endif

</body>

</html>
