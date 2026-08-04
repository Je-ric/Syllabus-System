<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>F.003 Review Form — {{ $syllabus->course->course_code ?? 'Course' }}</title>

    @if (!empty($isSnapshot) && !empty($inlineReviewCss))
        <style>{!! $inlineReviewCss !!}</style>
    @else
        @vite(['resources/css/review.css'])
    @endif

    @php
        /* ── Helpers ──────────────────────────────────────────────────────── */

        /** @var \App\Models\Syllabus $syllabus */
        /** @var \App\Models\SyllabusReviewForm|null $reviewForm */

        $course   = $syllabus->course;
        $calendar = $syllabus->academicCalendar;
        $preparer = $syllabus->preparer;

        // Part A auto-fill values
        $program    = $course?->program;
        $dept       = $program?->departments?->first();
        $college    = $dept?->college;

        $degreeProgram  = $program?->name ?? '—';
        $collegeName    = $college?->name ?? '—';
        $deptName       = $dept?->name ?? '—';
        $courseCode     = $course?->course_code ?? '—';
        $courseTitle    = $course?->course_title ?? '—';
        $prerequisite   = $course?->prerequisite ?? 'None';
        $corequisite    = $course?->corequisite ?? 'None';

        $lecUnits = $course?->lec_units ?? 0;
        $labUnits = $course?->lab_units ?? 0;
        $creditUnitsText = $lecUnits . ' unit/s lecture' . ($labUnits ? ' and ' . $labUnits . ' unit/s laboratory' : '');

        $lecHours = $course?->lec_class_hours ?? 0;
        $labHours = $course?->lab_class_hours ?? 0;
        $classHoursText = $lecHours . ' hr/s/wk lecture' . ($labHours ? ' and ' . $labHours . ' hr/s/wk laboratory' : '');

        $semesterAY = ($calendar?->semester ?? '—') . ' ' . ($calendar?->academic_year ?? '');

        // Faculty names from components
        $facultyNames = collect($syllabus->components ?? [])
            ->map(fn($c) => $c->user?->name)
            ->filter()
            ->unique()
            ->values()
            ->implode(', ');
        if (blank($facultyNames)) $facultyNames = $preparer?->name ?? '—';

        $courseLead = $reviewForm?->course_lead_name ?? $preparer?->name ?? '—';
        $dateSubmitted = $reviewForm?->submitted_at
            ? \Carbon\Carbon::parse($reviewForm->submitted_at)->format('M d, Y')
            : '—';

        // Part B
        $classification = $reviewForm?->classification; // 'updating' | 'revision' | null

        // Reviewers split by role
        $reviewers = $syllabus->reviewers ?? collect();
        $chairReviewer  = $reviewers->firstWhere('role', 'chair');
        $memberReviewers = $reviewers->where('role', 'member')->values();

        // Part C — selected change types
        $selectedChanges = $reviewForm?->natureOfChanges?->pluck('change_type')->toArray() ?? [];

        $updatingOptions = [
            'schedule_calendar'   => 'Schedule/calendar changes',
            'faculty_contact'     => 'Faculty/contact details',
            'references_textbooks'=> 'References/textbooks/resources',
            'typographical_formatting' => 'Typographical/formatting corrections',
            'minor_administrative'=> 'Minor administrative updates',
            'other_updating'      => 'Other',
        ];
        $revisionOptions = [
            'stakeholder_feedback'              => 'Stakeholder feedback',
            'cqi_findings'                      => 'CQI findings/action plan',
            'policy_curricular'                 => 'Policy/curricular changes',
            'accreditation_qa'                  => 'Accreditation/QA recommendation',
            'change_in_cos_po_mapping'          => 'Change in COs/PO-CO mapping',
            'change_in_grading_assessments_content' => 'Change in grading/major assessments/core content',
            'other_revision'                    => 'Other',
        ];

        // Part D — attachments
        $attachments = $reviewForm?->attachments ?? collect();
        $attachmentOptions = [
            'draft_syllabus'   => 'Draft course syllabus for review',
            'cqi_report'       => 'CQI report (ISO audit, AUN-QA, accreditation findings)',
            'feedback_summary' => 'Feedback summary (student, stakeholder, alumni/employer)',
            'policy_memo'      => 'Policy memo (CHED, university, college/department)',
            'mapping_evidence' => 'Mapping/assessment evidence (curriculum map, PO-CO map)',
            'other'            => 'Other',
        ];

        // Part E — checklist criteria (mirrors ReviewCriteria.php)
        $criteriaA = [
            'A1' => 'The official syllabus template is used.',
            'A2' => 'All required course details are complete and correct.',
            'A3' => 'The syllabus shows the version number and date of preparation or revision.',
            'A4' => 'Submission is correctly identified as Updating or Revision.',
        ];
        $criteriaB = [
            'B1' => 'Course Outcomes are SMART (specific, measurable, achievable, relevant, time-bound).',
            'B2' => 'Number of COs follows institutional policy (preferably 3; written justification attached if more).',
            'B3' => 'Course Outcomes are clearly mapped to the appropriate Program Outcomes.',
            'B4' => 'Course content is aligned with the stated Course Outcomes.',
            'B5' => 'Teaching-learning activities are aligned with the stated Course Outcomes.',
            'B6' => 'Assessment tasks are aligned with the stated Course Outcomes.',
        ];
        $criteriaCU = [
            'CU1' => 'Proposed changes are minor/routine and do not substantially alter the course design.',
            'CU2' => 'No change was made to the Course Outcomes.',
            'CU3' => 'No change was made to the grading system.',
            'CU4' => 'No change was made to core course content.',
        ];
        $criteriaCR = [
            'CR1' => 'There is a clear reason for the revision (feedback, CQI findings, policy changes, etc.).',
            'CR2' => 'Supporting evidence for the revision is attached.',
            'CR3' => 'Revised content, TLAs, assessment tasks, and grading remain constructively aligned.',
            'CR4' => 'The revision clearly addresses the feedback, findings, or recommendation used as basis.',
            'CR5' => 'For multi-section courses, the syllabus shows how common COs, assessments, and grading will be followed.',
        ];

        // Checklist responses keyed by [reviewer_user_id][criterion_code]
        $checklistResponses = [];
        foreach ($reviewForm?->checklistResponses ?? [] as $resp) {
            $checklistResponses[$resp->reviewer_user_id][$resp->criterion_code] = $resp;
        }

        // All reviewers for the checklist columns
        $allReviewers = collect();
        if ($chairReviewer) $allReviewers->push($chairReviewer);
        foreach ($memberReviewers as $m) $allReviewers->push($m);

        // Part F — decision
        $decision = $reviewForm?->decision;
        $decisionLabels = [
            'approved_as_updating'    => 'Approved as Updating',
            'approved_as_revision'    => 'Approved as Revision',
            'approved_with_corrections' => 'Approved with Corrections',
            'returned_for_revision'   => 'Returned for Revision',
            'reclassified_as_revision'=> 'Reclassified as Revision',
        ];
        $decisionClasses = [
            'approved_as_updating'    => 'rf-decision-approved',
            'approved_as_revision'    => 'rf-decision-approved',
            'approved_with_corrections' => 'rf-decision-corrections',
            'returned_for_revision'   => 'rf-decision-returned',
            'reclassified_as_revision'=> 'rf-decision-reclassified',
        ];

        // Part I
        $chairRec  = $reviewForm?->recommendedByChair;
        $deanApproval = $reviewForm?->approvedByDean;

        // Snapshot status
        $syllabusStatus = $syllabus->status ?? 'draft';
        $statusLabels = [
            'draft'        => 'Draft',
            'under_review' => 'Under Review',
            'approved'     => 'Approved',
            'for_revision' => 'For Revision',
        ];
        $statusClasses = [
            'draft'        => 'rf-status-draft',
            'under_review' => 'rf-status-under-review',
            'approved'     => 'rf-status-approved',
            'for_revision' => 'rf-decision-returned',
        ];

        // Helper: response badge HTML
        $responseBadge = function(?string $r): string {
            if ($r === 'satisfied')     return '<span class="rf-badge rf-badge-satisfied">✓ Satisfied</span>';
            if ($r === 'not_satisfied') return '<span class="rf-badge rf-badge-not">✗ Not Satisfied</span>';
            if ($r === 'not_applicable') return '<span class="rf-badge rf-badge-na">N/A</span>';
            return '<span class="rf-empty">—</span>';
        };

        // Helper: checkbox HTML
        $checkbox = fn(bool $checked): string =>
            '<span class="rf-box' . ($checked ? ' checked' : '') . '"></span>';
    @endphp
</head>
<body>

{{-- ── Toolbar (live preview only) ──────────────────────────── --}}
@if (empty($isSnapshot))
<div id="rf-toolbar">
    <div>
        <span class="t-title">F.003 Review Form — {{ $courseCode }}</span>
        <span class="t-pages" id="rf-page-count"></span>
    </div>
    <div class="t-right">
        <button type="button" onclick="window.print()">Print</button>
        <button type="button"
            onclick="window.location.href='{{ route('syllabus.wizard', ['syllabusId' => $syllabus->id]) }}'">
            Back to Wizard
        </button>
    </div>
</div>
@endif

<div id="rf-container">
<div class="rf-page">

    {{-- ── Header ──────────────────────────────────────────────── --}}
    <div class="rf-header">
        <div class="rf-office">Office of Learning Outcomes and Innovation</div>
        <div class="rf-form-title">Integrated OBTL Syllabus Review Form</div>
        <div class="rf-form-subtitle">(For Updating and Revision)</div>
        <div class="rf-form-note">Parts A–D shall be accomplished by the submitting faculty member.</div>
    </div>

    {{-- ── Part A — Course Identification ─────────────────────── --}}
    <div class="rf-section-heading">A. Course Identification</div>
    <table class="rf-kv-table">
        <tbody>
            <tr><td>Degree Program</td><td>{{ $degreeProgram }}</td></tr>
            <tr><td>College</td><td>{{ $collegeName }}</td></tr>
            <tr><td>Department/s</td><td>{{ $deptName }}</td></tr>
            <tr><td>Course Code</td><td>{{ $courseCode }}</td></tr>
            <tr><td>Course Title</td><td>{{ $courseTitle }}</td></tr>
            <tr><td>Prerequisite</td><td>{{ $prerequisite }}</td></tr>
            <tr><td>Co-requisite</td><td>{{ $corequisite }}</td></tr>
            <tr><td>Credit Units</td><td>{{ $creditUnitsText }}</td></tr>
            <tr><td>Class Hours</td><td>{{ $classHoursText }}</td></tr>
            <tr><td>Semester &amp; Academic Year</td><td>{{ $semesterAY }}</td></tr>
            <tr><td>Faculty Member/s</td><td>{{ $facultyNames }}</td></tr>
            <tr>
                <td>
                    Course Lead
                    <span class="rf-small rf-italic">(if applicable)</span>
                </td>
                <td>{{ $courseLead }}</td>
            </tr>
            <tr><td>Date Submitted</td><td>{{ $dateSubmitted }}</td></tr>
        </tbody>
    </table>
    <p class="rf-small rf-italic rf-mt-4">
        * Applicable to multi-section courses where faculty members collaborate on and adopt a single, unified syllabus to maintain instructional consistency.
    </p>

    {{-- ── Part B — Classification ──────────────────────────────── --}}
    <div class="rf-section-heading">B. Classification of Submission</div>
    <p class="rf-small rf-mb-4">
        Check <strong>UPDATING</strong> when the changes are minor and routine only.
        Check <strong>REVISION</strong> when the changes affect course outcomes, grading, major assessments, PO-CO alignment, or core content, or when the change is triggered by stakeholder feedback, policy changes, or CQI findings.
    </p>

    <div class="rf-two-col">
        {{-- Updating column --}}
        <div>
            <div class="rf-col-header">
                {!! $checkbox($classification === 'updating') !!} 
                &nbsp;<strong>UPDATING</strong>
                <span class="rf-small"> — reviewer: Program CQI Committee Chair</span>
            </div>
            <div class="rf-col">
                <div class="rf-reviewer-block">
                    <div class="rf-reviewer-label">Program CQI Committee Chair:</div>
                    <div class="rf-reviewer-name">
                        {{ $chairReviewer?->user?->name ?? '—' }}
                    </div>
                </div>
            </div>
        </div>
        {{-- Revision column --}}
        <div>
            <div class="rf-col-header">
                {!! $checkbox($classification === 'revision') !!}
                &nbsp;<strong>REVISION</strong>
                <span class="rf-small"> — reviewer: Program CQI Committee</span>
            </div>
            <div class="rf-col">
                <div class="rf-reviewer-block">
                    <div class="rf-reviewer-label">Program CQI Committee Chair:</div>
                    <div class="rf-reviewer-name">{{ $chairReviewer?->user?->name ?? '—' }}</div>
                    <div class="rf-reviewer-label">Member 1:</div>
                    <div class="rf-reviewer-name">{{ $memberReviewers[0]?->user?->name ?? '—' }}</div>
                    <div class="rf-reviewer-label">Member 2:</div>
                    <div class="rf-reviewer-name">{{ $memberReviewers[1]?->user?->name ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Part C — Nature of Change ────────────────────────────── --}}
    <div class="rf-section-heading">C. Nature of Change and Supporting Basis</div>
    <div class="rf-two-col">
        {{-- Updating options --}}
        <div>
            <div class="rf-col-header">
                If classified as <strong>UPDATING</strong>, check all applicable minor/routine changes:
            </div>
            <div class="rf-col">
                <ul class="rf-check-list">
                    @foreach ($updatingOptions as $key => $label)
                        <li>
                            {!! $checkbox(in_array($key, $selectedChanges)) !!}
                            <span>{{ $label }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        {{-- Revision options --}}
        <div>
            <div class="rf-col-header">
                If classified as <strong>REVISION</strong>, check all applicable substantive bases:
            </div>
            <div class="rf-col">
                <ul class="rf-check-list">
                    @foreach ($revisionOptions as $key => $label)
                        <li>
                            {!! $checkbox(in_array($key, $selectedChanges)) !!}
                            <span>{{ $label }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    {{-- ── Part D — Documentary Attachments ────────────────────── --}}
    <div class="rf-section-heading">D. Documentary Attachment</div>
    <p class="rf-small rf-mb-4">Check all applicable attachments submitted with the syllabus for review.</p>
    <ul class="rf-check-list">
        @foreach ($attachmentOptions as $key => $label)
            @php
                $att = $attachments->firstWhere('attachment_type', $key);
                $isChecked = $att && $att->is_submitted;
                $otherLabel = ($key === 'other' && $att) ? $att->other_label : null;
            @endphp
            <li>
                {!! $checkbox($isChecked) !!}
                <span>
                    {{ $label }}
                    @if ($key === 'other' && $otherLabel)
                        — <em>{{ $otherLabel }}</em>
                    @endif
                </span>
            </li>
        @endforeach
    </ul>

    {{-- ── Submitted by signature line ─────────────────────────── --}}
    <table class="rf-sig-table rf-mt-8">
        <thead>
            <tr>
                <th style="width:40%">Submitted by</th>
                <th style="width:30%">Signature</th>
                <th style="width:30%">Date</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $preparer?->name ?? '—' }}</td>
                <td><div class="sig-line"></div></td>
                <td>{{ $dateSubmitted }}</td>
            </tr>
        </tbody>
    </table>

    {{-- ── Page footer ──────────────────────────────────────────── --}}
    <div class="rf-page-footer">
        <span>ACA.OBE.YYY.F.003 (Revision No. 0; May 11, 2026)</span>
        <span>Page 1</span>
    </div>

</div>{{-- /rf-page 1 --}}

{{-- ═══════════════════════════════════════════════════════════
     PAGE 2 — Parts E, F, G, H, I
     ═══════════════════════════════════════════════════════════ --}}
<div class="rf-page">

    {{-- ── Part E — Review Checklist ───────────────────────────── --}}
    <div class="rf-section-heading">E. Review Checklist</div>
    <p class="rf-small rf-mb-4">
        Parts E–I shall be accomplished by the Program CQI Committee.
        Respond to each criterion: <strong>Satisfied</strong>, <strong>Not Satisfied</strong>, or <strong>N/A</strong>.
    </p>

    @php
        // Build checklist sections based on classification
        $checklistSections = [
            'A' => ['label' => 'Section A — Document Control and Course Information', 'criteria' => $criteriaA],
            'B' => ['label' => 'Section B — Course Outcomes and OBE Alignment',       'criteria' => $criteriaB],
        ];
        if ($classification === 'updating' || $classification === null) {
            $checklistSections['C_updating'] = [
                'label'    => 'Section C — Specific Compliance for Updating Track',
                'criteria' => $criteriaCU,
            ];
        }
        if ($classification === 'revision') {
            $checklistSections['C_revision'] = [
                'label'    => 'Section C — Specific Compliance for Revision Track',
                'criteria' => $criteriaCR,
            ];
        }
        if ($classification === null) {
            // Show both when not yet classified
            $checklistSections['C_revision'] = [
                'label'    => 'Section C — Specific Compliance for Revision Track',
                'criteria' => $criteriaCR,
            ];
        }
    @endphp

    @foreach ($checklistSections as $sectionKey => $section)
        <table class="rf-checklist-table">
            <thead>
                <tr>
                    <th class="criterion-col">{{ $section['label'] }}</th>
                    @foreach ($allReviewers as $rev)
                        <th class="response-col">
                            {{ $rev->user?->name ?? '—' }}<br>
                            <span style="font-size:7pt; font-weight:400;">
                                {{ $rev->role === 'chair' ? 'Chair' : 'Member' }}
                            </span>
                        </th>
                        <th class="comments-col">Comments</th>
                    @endforeach
                    @if ($allReviewers->isEmpty())
                        <th class="response-col">Response</th>
                        <th class="comments-col">Comments</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($section['criteria'] as $code => $text)
                    <tr>
                        <td><strong>{{ $code }}.</strong> {{ $text }}</td>
                        @foreach ($allReviewers as $rev)
                            @php
                                $resp = $checklistResponses[$rev->user_id][$code] ?? null;
                            @endphp
                            <td class="rf-center">{!! $responseBadge($resp?->response) !!}</td>
                            <td class="rf-small">{{ $resp?->comments ?? '' }}</td>
                        @endforeach
                        @if ($allReviewers->isEmpty())
                            <td></td><td></td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="rf-mt-4"></div>
    @endforeach

    {{-- ── Part F — Decision ────────────────────────────────────── --}}
    <div class="rf-section-heading">F. Decision of the Program CQI Committee</div>
    <div class="rf-decision-block">
        <div class="rf-decision-label">Decision:</div>
        @if ($decision)
            <span class="rf-decision-value {{ $decisionClasses[$decision] ?? '' }}">
                {{ $decisionLabels[$decision] ?? $decision }}
            </span>
            @if ($reviewForm?->decision_made_at)
                <span class="rf-small rf-mt-2" style="display:block;">
                    Recorded: {{ \Carbon\Carbon::parse($reviewForm->decision_made_at)->format('M d, Y') }}
                </span>
            @endif
        @else
            <span class="rf-empty">No decision recorded yet.</span>
        @endif

        @if ($reviewForm?->required_actions)
            <div class="rf-mt-4">
                <strong>Required Actions / Corrections:</strong>
                <p class="rf-small rf-mt-2">{{ $reviewForm->required_actions }}</p>
            </div>
        @endif
        @if ($reviewForm?->target_compliance_date)
            <div class="rf-mt-4 rf-small">
                <strong>Target Compliance Date:</strong>
                {{ \Carbon\Carbon::parse($reviewForm->target_compliance_date)->format('M d, Y') }}
            </div>
        @endif
    </div>

    {{-- ── Part G — Signatures ──────────────────────────────────── --}}
    <div class="rf-section-heading">G. Signatures of the Program CQI Committee</div>
    <table class="rf-sig-table">
        <thead>
            <tr>
                <th style="width:30%">Role</th>
                <th style="width:35%">Name</th>
                <th style="width:20%">Signature</th>
                <th style="width:15%">Date Signed</th>
            </tr>
        </thead>
        <tbody>
            @if ($chairReviewer)
                <tr>
                    <td>Program CQI Committee Chair</td>
                    <td>{{ $chairReviewer->user?->name ?? '—' }}</td>
                    <td><div class="sig-line"></div></td>
                    <td></td>
                </tr>
            @else
                <tr>
                    <td>Program CQI Committee Chair</td>
                    <td class="rf-empty">Not assigned</td>
                    <td></td><td></td>
                </tr>
            @endif
            @forelse ($memberReviewers as $idx => $member)
                <tr>
                    <td>Committee Member {{ $idx + 1 }}</td>
                    <td>{{ $member->user?->name ?? '—' }}</td>
                    <td><div class="sig-line"></div></td>
                    <td></td>
                </tr>
            @empty
                <tr>
                    <td>Committee Member 1</td>
                    <td class="rf-empty">Not assigned</td>
                    <td></td><td></td>
                </tr>
                <tr>
                    <td>Committee Member 2</td>
                    <td class="rf-empty">Not assigned</td>
                    <td></td><td></td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ── Part H — Action Taken on Review Comments ────────────── --}}
    @if ($decision === 'approved_with_corrections' || $reviewForm?->part_h_faculty_response)
        <div class="rf-section-heading">H. Action Taken on Review Comments (Faculty Response)</div>
        <div class="rf-part-h">
            @if ($reviewForm?->part_h_faculty_response)
                <div class="rf-response-text">{{ $reviewForm->part_h_faculty_response }}</div>
                @if ($reviewForm->part_h_verified_at)
                    <div class="rf-verified-by rf-mt-4">
                        <strong>Verified by:</strong>
                        {{ $reviewForm->partHVerifiedBy?->name ?? '—' }}
                        on {{ \Carbon\Carbon::parse($reviewForm->part_h_verified_at)->format('M d, Y') }}
                    </div>
                @else
                    <div class="rf-verified-by rf-mt-4 rf-empty">Pending verification by reviewer.</div>
                @endif
            @else
                <span class="rf-empty">Faculty has not yet submitted a response to the required corrections.</span>
            @endif
        </div>
    @endif

    {{-- ── Part I — Approval Authority ─────────────────────────── --}}
    <div class="rf-section-heading">I. Approval Authority</div>
    <div class="rf-approval-block">
        <div class="rf-approval-row">
            <span class="rf-approval-label">Recommended for Approval by:</span>
            <span class="rf-approval-value">
                @if ($chairRec)
                    {{ $chairRec->name }}
                    <span class="rf-small">
                        ({{ \Carbon\Carbon::parse($reviewForm->recommended_by_chair_at)->format('M d, Y') }})
                    </span>
                @else
                    <span class="rf-empty">Pending Chair recommendation</span>
                @endif
            </span>
        </div>
        <div class="rf-approval-row">
            <span class="rf-approval-label">Approved by (Dean):</span>
            <span class="rf-approval-value">
                @if ($deanApproval)
                    {{ $deanApproval->name }}
                    <span class="rf-small">
                        ({{ \Carbon\Carbon::parse($reviewForm->approved_by_dean_at)->format('M d, Y') }})
                    </span>
                @else
                    <span class="rf-empty">Pending Dean approval</span>
                @endif
            </span>
        </div>
        @if ($reviewForm?->filed_at)
            <div class="rf-approval-row">
                <span class="rf-approval-label">Filed:</span>
                <span class="rf-approval-value">
                    {{ \Carbon\Carbon::parse($reviewForm->filed_at)->format('M d, Y') }}
                    @if ($reviewForm->filing_type)
                        —
                        {{ $reviewForm->filing_type === 'updating_department' ? 'Department file (Updating)' : 'OLOI file (Revision)' }}
                    @endif
                </span>
            </div>
        @endif
    </div>

    {{-- ── Page footer ──────────────────────────────────────────── --}}
    <div class="rf-page-footer">
        <span>ACA.OBE.YYY.F.003 (Revision No. 0; May 11, 2026)</span>
        <span>Page 2</span>
    </div>

</div>{{-- /rf-page 2 --}}
</div>{{-- /rf-container --}}

@if (empty($isSnapshot))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const pages = document.querySelectorAll('.rf-page');
        const el = document.getElementById('rf-page-count');
        if (el) el.textContent = pages.length + ' page' + (pages.length !== 1 ? 's' : '');
    });
</script>
@endif

</body>
</html>
