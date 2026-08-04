# 16 Review Form Plan
F.003 Integrated OBTL Syllabus Review Form — full implementation plan, design rationale, flow, rules, and file manifest. Read this before touching any part of the review form feature.

## Why This Document Exists

The F.003 form is a multi-party, multi-stage approval workflow that touches the syllabus wizard, the reviewer portal, the snapshot system, and notifications. Without a single source of truth for the plan, every developer working on different parts of this feature will make incompatible assumptions. This document is that source of truth.

It follows the principles in `MD/.amazonq/rules/08-system-planning-and-flow.md`:
- Know the problem first. Document conditions, sequence, and approach before writing code.
- Define who can do what before building any feature.
- Identify what needs to be logged, notified, and validated upfront.
- List all edge cases before starting.

This document is **living**. Every code change to this feature must update this document in the same session. See `MD/.amazonq/rules/15-md-update.md`.

Related docs: `MD/10_Syllabus_Wizard.md`, `MD/14_Syllabus_CRUD.md`

# 16 Review Form Plan
F.003 Integrated OBTL Syllabus Review Form — full implementation plan, design rationale, flow, principles, and file manifest.

## Why This Document Exists

The F.003 form is a multi-party, multi-stage approval workflow touching the syllabus wizard, the reviewer portal, the snapshot system, and notifications. Without a single source of truth every developer working on a different part of this feature will make incompatible assumptions. This document is that source of truth.

It follows the principles in `.amazonq/rules/08-system-planning-and-flow.md`:
- Know the problem before writing code.
- Document conditions, sequence, and approach in an MD before coding.
- Define who can do what before building any feature.
- Identify what needs to be logged, notified, and validated upfront.
- List all edge cases and edge actors before starting.

This document is **living**. Every code change to this feature must update this document in the same session per `.amazonq/rules/15-md-update.md`. Never leave doc updates for a later task.

Related docs: `MD/10_Syllabus_Wizard.md`, `MD/14_Syllabus_CRUD.md`

---

## What It Is

The F.003 Integrated OBTL Syllabus Review Form is in the Central Luzon State University (OLOI) standard form used to formally review and approve a course syllabus. It tracks two review tracks:

- **Updating** — minor, routine changes. Reviewed by the Program CQI Committee Chair alone.
- **Revision** — substantive changes (Course Outcomes, grading, core content, CO-PO mapping). Reviewed by the full Program CQI Committee (Chair + 2 members).

The goal of this feature is to digitize the entire F.003 process inside CSMS so that all parties — faculty, reviewers, and the dean — complete every step inside the system, with every decision and signature permanently recorded and frozen into a versioned HTML snapshot.

---

## Confirmed Decisions (Locked — Do Not Re-debate)

These were resolved through deliberate discussion with the system owner. Do not reopen these without explicit instruction.

| Decision | Rule |
|---|---|
| The syllabus author assigns their own reviewers | Author picks Chair and Members; no system automation for now|
| The author sets the classification (Updating/Revision) | Only the author selects the track |
| The Chair can reclassify from Updating to Revision | On reclassification: checklist resets, author assigns 2 members, review restarts |
| Each syllabus is strictly owned by its author | No other user — even same course — can edit another author's syllabus |
| Deans can approve any syllabus from any college | Not restricted to their own college |
| Course Lead = syllabus author | Auto-filled from `prepared_by`; no separate system concept |
| Notifications fire at every meaningful handoff | See Notification Triggers section |
| Review form snapshot = HTML file, stored on disk | Mirrors how `pdf_path` / `abridged_path` / `evaluation_path` work in `complete_syllabi` |

---

## Roles and Access

| Role | What They Can Do |
|---|---|
| `faculty` | Author — fills Parts A–D, assigns reviewers, submits, fills Part H if corrections required |
| `chair` (as CQI Chair reviewer) | Fills checklist (E.1 or E.2), records decision (Part F), signs (Part G), verifies Part H, recommends approval (Part I) |
| `faculty` (as committee member) | Fills their checklist column (E.2 only, Revision track), signs (Part G), can verify Part H |
| `dean` | Final approval (Part I); can approve any syllabus from any college |

Key rules per `.amazonq/rules/06-security-guide.md` and `.amazonq/rules/08-system-planning-and-flow.md`:
- Authorization is checked by role AND by record ownership — not just authentication.
- A reviewer can only access a review form if their `user_id` is in `syllabus_reviewers` for that syllabus, OR they have the `dean` role.
- The author can only edit their own review form — not another author's even for the same course.

---

## Files Used (Source of Truth)

### New Migrations
- `database/migrations/2026_07_29_000002_create_syllabus_review_forms_table.php`
- `database/migrations/2026_07_29_000003_create_syllabus_review_nature_of_change_table.php`
- `database/migrations/2026_07_29_000004_create_syllabus_review_attachments_table.php`
- `database/migrations/2026_07_29_000005_create_syllabus_review_checklist_responses_table.php`
- `database/migrations/2026_07_29_000006_add_role_to_syllabus_reviewers_table.php`
- `database/migrations/2026_07_29_000007_add_review_snapshot_to_complete_syllabi_table.php`

### New Models
- `app/Models/SyllabusReviewForm.php`
- `app/Models/SyllabusReviewNatureOfChange.php`
- `app/Models/SyllabusReviewAttachment.php`
- `app/Models/SyllabusReviewChecklistResponse.php`

### Modified Models
- `app/Models/Syllabus.php` — added `reviewForm()` hasOne
- `app/Models/SyllabusReviewer.php` — added `role` to `$fillable`
- `app/Models/CompleteSyllabus.php` — added `review_form_path`, `checksum_review_form` to `$fillable`

### New Service
- `app/Services/Syllabus/SyllabusReviewFormService.php`

### Modified Services
- `app/Services/Syllabus/SyllabusReviewService.php` — `assignReviewer()` accepts `role`; no longer auto-approves
- `app/Services/Syllabus/SyllabusSnapshotService.php` — `saveVersion()` generates and stores review form HTML snapshot

### New Livewire Component
- `app/Livewire/Syllabus/SyllabusReviewChecklist.php` — reviewer portal component

### Modified Livewire Components
- `app/Livewire/Syllabus/Steps/ReviewStep.php` — loads review form, handles author-side mutations
- `app/Livewire/Syllabus/SyllabusWizard.php` — passes `role` to reviewer assignment

### New Views
- `resources/views/livewire/syllabus/steps/review-partials/review-form.blade.php` — author-side panel (Parts A–D, status, Part H)
- `resources/views/livewire/syllabus/review-checklist.blade.php` — reviewer portal component view
- `resources/views/syllabus/review-portal.blade.php` — page layout wrapping the reviewer portal

### Modified Views
- `resources/views/livewire/syllabus/steps/review.blade.php` — includes new `review-form` partial
- `resources/views/livewire/syllabus/steps/review-partials/reviewers.blade.php` — adds role selector, completion badge, portal link per reviewer

### New Notifications
- `app/Notifications/SyllabusSubmittedForReviewNotification.php`
- `app/Notifications/SyllabusReviewerCompletedNotification.php`
- `app/Notifications/SyllabusDecisionRecordedNotification.php`
- `app/Notifications/SyllabusReclassifiedNotification.php`
- `app/Notifications/SyllabusReturnedForRevisionNotification.php`
- `app/Notifications/SyllabusCorrectionsRequiredNotification.php`
- `app/Notifications/SyllabusPartHSubmittedNotification.php`
- `app/Notifications/SyllabusPartHVerifiedNotification.php`
- `app/Notifications/SyllabusRecommendedForApprovalNotification.php`
- `app/Notifications/SyllabusFinallyApprovedNotification.php`

### New Value Object
- `app/Data/ReviewCriteria.php` — fixed F.003 criterion definitions; not stored in DB

### New Controller
- `app/Http/Controllers/Syllabus/SyllabusReviewController.php` — handles reviewer portal routes

### Route Changes
- `routes/web.php` — 6 new reviewer portal routes (see Routes section)

---

## Database Schema

### `syllabus_review_forms` (new)

One record per syllabus. The master record tying the entire F.003 form together.

| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `syllabus_id` | FK → syllabi, UNIQUE | One form per syllabus |
| `classification` | ENUM: updating, revision | Nullable — set by author |
| `course_lead_name` | string nullable | Auto-filled from author name |
| `submitted_at` | timestamp nullable | Set when author submits |
| `decision` | ENUM: 5 values | Nullable — set by Chair only |
| `decision_made_at` | timestamp nullable | |
| `required_actions` | text nullable | Part F — what must be fixed |
| `target_compliance_date` | date nullable | Part F deadline |
| `part_h_faculty_response` | text nullable | Part H — author's reply |
| `part_h_verified_by` | FK → users nullable | Chair/member who verified |
| `part_h_verified_at` | timestamp nullable | |
| `recommended_by_chair_id` | FK → users nullable | Part I |
| `recommended_by_chair_at` | timestamp nullable | |
| `approved_by_dean_id` | FK → users nullable | Part I |
| `approved_by_dean_at` | timestamp nullable | |
| `filed_at` | timestamp nullable | |
| `filing_type` | ENUM: updating_department, revision_oloi | Nullable |
| `review_form_snapshot` | longText nullable | Kept for fallback JSON; primary snapshot is HTML file |
| `created_at / updated_at` | timestamps | |

Decision ENUM values: `approved_as_updating`, `approved_as_revision`, `approved_with_corrections`, `returned_for_revision`, `reclassified_as_revision`

### `syllabus_review_nature_of_change` (new)

One row per selected Part C checkbox. Unique per `(review_form_id, change_type)`.

Updating values: `schedule_calendar`, `faculty_contact`, `references_textbooks`, `typographical_formatting`, `minor_administrative`, `other_updating`

Revision values: `stakeholder_feedback`, `cqi_findings`, `policy_curricular`, `accreditation_qa`, `change_in_cos_po_mapping`, `change_in_grading_assessments_content`, `other_revision`

### `syllabus_review_attachments` (new)

One row per Part D attachment type. Unique per `(review_form_id, attachment_type)`.

Types: `draft_syllabus`, `cqi_report`, `feedback_summary`, `policy_memo`, `mapping_evidence`, `other`

Columns: `is_submitted` (boolean), `other_label` (string nullable — only when type = other)

### `syllabus_review_checklist_responses` (new)

One row per reviewer per criterion. Unique per `(review_form_id, reviewer_user_id, criterion_code)`.

| Column | Type | Notes |
|---|---|---|
| `review_form_id` | FK → syllabus_review_forms | |
| `reviewer_user_id` | FK → users | |
| `section` | ENUM: A, B, C_updating, C_revision | |
| `criterion_code` | string(10) | e.g. A1, B3, CU2, CR5 |
| `response` | ENUM: satisfied, not_satisfied, not_applicable | Nullable |
| `comments` | text nullable | |

### `syllabus_reviewers` (modified — add `role` column)

```
role  ENUM: chair, member  default: member
```

The existing `status` (pending/approved/rejected) now reflects real completion — a reviewer's status only becomes `approved` when they finish their entire checklist.

### `complete_syllabi` (modified — add snapshot columns)

```
review_form_path       string nullable  — path to the HTML snapshot of the F.003 form on disk
checksum_review_form   string(64) nullable  — sha256 of the HTML file
```

---

## Checklist Criteria (Fixed — Not in DB)

Defined in `app/Data/ReviewCriteria.php`. These mirror the F.003 form exactly and do not change without a formal form revision.

### Section A — Document Control and Course Information
- **A1** The official syllabus template is used.
- **A2** All required course details are complete and correct.
- **A3** The syllabus shows the version number and date of preparation or revision.
- **A4** Submission is correctly identified as Updating or Revision.

### Section B — Course Outcomes and OBE Alignment
- **B1** Course Outcomes are SMART (specific, measurable, achievable, relevant, time-bound).
- **B2** Number of COs follows institutional policy (preferably 3; written justification attached if more).
- **B3** Course Outcomes are clearly mapped to the appropriate Program Outcomes.
- **B4** Course content is aligned with the stated Course Outcomes.
- **B5** Teaching-learning activities are aligned with the stated Course Outcomes.
- **B6** Assessment tasks are aligned with the stated Course Outcomes.

### Section C-Updating — Specific Compliance for Updating Track
- **CU1** Proposed changes are minor/routine and do not substantially alter the course design.
- **CU2** No change was made to the Course Outcomes.
- **CU3** No change was made to the grading system.
- **CU4** No change was made to core course content.

### Section C-Revision — Specific Compliance for Revision Track
- **CR1** There is a clear reason for the revision (feedback, CQI findings, policy changes, etc.).
- **CR2** Supporting evidence for the revision is attached.
- **CR3** Revised content, TLAs, assessment tasks, and grading remain constructively aligned.
- **CR4** The revision clearly addresses the feedback, findings, or recommendation used as basis.
- **CR5** For multi-section courses, the syllabus shows how common COs, assessments, and grading will be followed.

---

## Conditions (If / Then)

### Classification and Track Selection

- If the author selects **Updating**:
  - Then only the Chair column is shown in Part E.
  - Then only Updating checkboxes are shown in Part C.
  - Then only the Chair is required as a reviewer.
  - Then on decision, Part I filing type is `updating_department`.
- If the author selects **Revision**:
  - Then all three reviewer columns are shown in Part E.
  - Then only Revision checkboxes are shown in Part C.
  - Then Chair + 2 Members are required.
  - Then on decision, Part I filing type is `revision_oloi`.
- If the Chair records decision **reclassified_as_revision**:
  - Then `classification` on the form changes to `revision`.
  - Then all existing `syllabus_review_checklist_responses` for this form are deleted.
  - Then all `syllabus_reviewers.status` for this syllabus are reset to `pending`.
  - Then the author is notified to assign 2 committee members.
  - Then the review restarts from the reviewer-filling stage under the Revision track.

### Reviewer Assignment

- If the author adds a reviewer:
  - Then `role` (chair or member) must be selected — there is no default assumption.
  - Then `status` is set to `pending` — **not** `approved` (old behavior removed).
  - Then only one chair is allowed per syllabus review form.
  - Then for Updating track, only the chair role is valid.
  - Then for Revision track, chair + up to 2 members are valid.
- If the author removes a reviewer:
  - Then their checklist responses are not automatically deleted — the Chair must decide whether to reset or keep them.
  - Then if all reviewers are removed after a submission, the form reverts to draft state.

### Checklist Completion

- If a reviewer saves a checklist response:
  - Then `response` is validated as one of: `satisfied`, `not_satisfied`, `not_applicable`.
  - Then `comments` are optional but encouraged for `not_satisfied`.
  - Then after each save, the service checks if ALL criteria for this reviewer are answered.
  - If all criteria answered:
    - Then `syllabus_reviewers.status` for this reviewer is updated to `approved`.
    - Then author is notified of progress.
    - Then Chair is notified.
  - If not all criteria answered:
    - Then `status` remains `pending`.
- If all reviewers complete (Revision track), OR the Chair completes (Updating track):
  - Then the Chair sees the Part F decision panel unlocked.

### Decision (Part F)

- If the Chair records a decision:
  - Then `decision_made_at` is set.
  - Then the author is notified.
- If decision = `approved_as_updating` or `approved_as_revision`:
  - Then Part I recommendation panel unlocks for the Chair.
  - Then all deans are notified when the Chair recommends.
- If decision = `approved_with_corrections`:
  - Then author sees the Part H response panel on their Review tab.
  - Then author is notified that corrections are required.
- If decision = `returned_for_revision`:
  - Then `Syllabus.status` is set to `for_revision`.
  - Then the author is notified to revise the syllabus itself and resubmit.
  - Then the review form reverts to draft state.
- If decision = `reclassified_as_revision`:
  - Then see Classification and Track Selection section above.

### Part H (Action Taken on Review Comments)

- If the decision is `approved_with_corrections` and `part_h_faculty_response` is null:
  - Then the approval process is blocked — Part I cannot proceed until Part H is submitted and verified.
- If the author submits `part_h_faculty_response`:
  - Then Chair and all committee members are notified that Part H is ready for verification.
- If a Chair or member verifies Part H:
  - Then `part_h_verified_by` and `part_h_verified_at` are set.
  - Then the dean is notified that the syllabus is ready for final approval.
  - Then Part I recommendation panel unlocks.

### Part I (Approval Authority)

- If the Chair clicks Recommend for Approval:
  - Then `recommended_by_chair_id` and `recommended_by_chair_at` are set.
  - Then **all deans** (not just the college dean) are notified.
- If a dean approves:
  - Then `approved_by_dean_id` and `approved_by_dean_at` are set.
  - Then `Syllabus.status` is updated to `approved`.
  - Then `filed_at` is set to `now()`.
  - Then `filing_type` is set based on `classification`.
  - Then the author is notified of final approval.

### HTML Snapshot (Save as Done)

- If the author clicks **Save as Done** in the wizard:
  - Then `SyllabusSnapshotService::saveVersion()` runs.
  - Then the existing syllabus HTML snapshots are generated (complete, abridged, assessment) as before.
  - Then `SyllabusReviewFormService::generateReviewFormHtml()` is called.
  - Then the review form HTML is written to disk at the path: `Syllabus Snapshots / {college} / {dept} / {program} / {faculty} / {course} / v{N} ({AY} {semester}) / ReviewForm - {course_code}.html`
  - Then `review_form_path` and `checksum_review_form` are stored in the `complete_syllabi` record.
  - If the review form does not yet exist (not submitted):
    - Then the snapshot captures current state (blank/partial) — this is intentional. It records the state at save time.

### Auto-fill (Part A)

- If the review form loads for any user:
  - Then the following fields are auto-populated from the existing syllabus and cannot be manually entered as separate inputs:

| F.003 Part A Field | Source |
|---|---|
| Degree Program | `course → program → program_name` |
| College | `course → program → departments → college → name` |
| Department/s | `course → program → departments → name` |
| Course Code | `course → course_code` |
| Course Title | `course → course_title` |
| Prerequisite | `course → prerequisite` (if stored) |
| Co-requisite | `course → corequisite` (if stored) |
| Credit Units | `course → lec_units + lab_units` |
| Class Hours | `course → lec_class_hours + lab_class_hours` |
| Semester & AY | `syllabus → academicCalendar → semester + academic_year` |
| Faculty member/s | `syllabus → components → instructor_email` (resolved to names) |
| Course Lead | `syllabus → preparer → name` (auto-filled into `course_lead_name`) |
| Date Submitted | `submitted_at` (set on submission) |

### Security and Authorization

Per `.amazonq/rules/06-security-guide.md`:
- If any route in `/syllabus/review/*` is accessed:
  - Then the middleware must verify the user is either:
    - An assigned reviewer (`syllabus_reviewers.user_id = Auth::id()` for this syllabus), OR
    - A user with the `dean` role.
  - If neither condition is met:
    - Then abort with `403 Forbidden`.
- If an author opens the review form in the wizard:
  - Then `SyllabusWizard::mount()` already enforces `prepared_by === Auth::id()` — no change needed.
- All `$fillable` arrays are explicit on all new models — no mass assignment without explicit permission.
- All output in Blade uses `{{ }}` (escaped) — `{!! !!}` is forbidden unless the content is a known-safe HTML snapshot.

### Validation Rules

Per `.amazonq/rules/14-Input validation.md`:
- `classification` — required before submission; must be `updating` or `revision`.
- `change_type` — must be from the allowed ENUM list; cross-check that types match the selected classification (Updating types not allowed on Revision track and vice versa).
- `attachment_type` — must be from the allowed ENUM list; one per type per form.
- `criterion_code` — must exist in `ReviewCriteria` definitions for the current classification and section.
- `response` — must be `satisfied`, `not_satisfied`, or `not_applicable`; cannot be null when submitting the checklist.
- `decision` — Chair only; must be from the allowed ENUM; cannot be set unless all required reviewers have status `approved`.
- `part_h_faculty_response` — required (non-empty) before Part H verification can occur.
- `target_compliance_date` — if provided, must be a future date.
- `role` on `SyllabusReviewer` — must be `chair` or `member`; only one `chair` allowed per syllabus.

### Error Handling

Per `.amazonq/rules/13-condition-completeness.md` and `.amazonq/rules/02-project-principles-and-coding-standards.md`:
- Every catch block in `SyllabusReviewFormService` logs the error and dispatches `lw-toast` with type `error` — no silent swallowing.
- Reclassification is wrapped in a DB transaction — if checklist reset or reviewer status reset fails, the classification change rolls back.
- Dean approval is wrapped in a DB transaction — `Syllabus.status`, `approved_by_dean_id`, `filed_at`, and `filing_type` must all succeed or all roll back.
- If the reviewer portal is accessed for a syllabus whose review form does not yet exist (author hasn't submitted):
  - Then show a "not yet submitted" empty state — do not crash.
- If a reviewer attempts to save a checklist response for a criterion not in `ReviewCriteria` for the current track:
  - Then reject with a validation error — do not silently insert an invalid row.

---

## Notification Triggers

Per `.amazonq/rules/08-system-planning-and-flow.md` — notifications fire at every meaningful handoff.

| Event | Who Gets Notified | Notification Class |
|---|---|---|
| Author submits for review | Assigned reviewers | `SyllabusSubmittedForReviewNotification` |
| A reviewer completes checklist | Author + Chair | `SyllabusReviewerCompletedNotification` |
| All reviewers complete (Revision) | Chair | `SyllabusReviewerCompletedNotification` |
| Chair records decision | Author | `SyllabusDecisionRecordedNotification` |
| Decision = reclassified | Author | `SyllabusReclassifiedNotification` |
| Decision = returned for revision | Author | `SyllabusReturnedForRevisionNotification` |
| Decision = approved with corrections | Author | `SyllabusCorrectionsRequiredNotification` |
| Author submits Part H | Chair + all members | `SyllabusPartHSubmittedNotification` |
| Part H verified | Dean (all deans) | `SyllabusPartHVerifiedNotification` |
| Chair recommends approval | All deans | `SyllabusRecommendedForApprovalNotification` |
| Dean approves | Author | `SyllabusFinallyApprovedNotification` |

---

## Routes

```php
// All routes below require auth middleware.
// Access control: reviewer must be in syllabus_reviewers for this syllabus, OR have dean role.

Route::get('/syllabus/review/{syllabus}',
    [SyllabusReviewController::class, 'show'])
    ->name('syllabus.review.show');

Route::post('/syllabus/review/{syllabus}/checklist',
    [SyllabusReviewController::class, 'saveChecklist'])
    ->name('syllabus.review.checklist.save');

Route::post('/syllabus/review/{syllabus}/decision',
    [SyllabusReviewController::class, 'recordDecision'])
    ->name('syllabus.review.decision');

Route::post('/syllabus/review/{syllabus}/verify-part-h',
    [SyllabusReviewController::class, 'verifyPartH'])
    ->name('syllabus.review.part-h.verify');

Route::post('/syllabus/review/{syllabus}/recommend',
    [SyllabusReviewController::class, 'recommend'])
    ->name('syllabus.review.recommend');

Route::post('/syllabus/review/{syllabus}/dean-approve',
    [SyllabusReviewController::class, 'deanApprove'])
    ->name('syllabus.review.dean-approve');
```

---

## Two User Interfaces

### Interface 1 — Author Side (inside the wizard Review tab)

Rendered inside `resources/views/livewire/syllabus/steps/review.blade.php` as a new partial `review-form.blade.php` inserted before the existing reviewers section.

| Part | Content | Editable by Author? |
|---|---|---|
| A — Course Identification | Auto-filled from syllabus; read-only | No |
| B — Classification | Updating / Revision radio | Yes, before submission |
| C — Nature of Change | Checkboxes — only relevant group for selected track shown | Yes, before submission |
| D — Attachments | Checklist of document types | Yes, before submission |
| Reviewer assignment | Role selector added (Chair/Member) to existing reviewer UI | Yes, before submission |
| Reviewer progress | Badge per reviewer: checklist complete / incomplete | No — read-only |
| F — Decision | Shown once Chair records it | No — read-only |
| H — Compliance response | Text area; shown only when decision = `approved_with_corrections` | Yes, after decision |
| I — Approval status | Shows Chair recommendation and Dean approval status | No — read-only |

### Interface 2 — Reviewer Side (dedicated portal page)

Located at `/syllabus/review/{syllabus}`. Wraps the `SyllabusReviewChecklist` Livewire component.

| Part | Chair | Member | Dean |
|---|---|---|---|
| A — Course summary | Read-only | Read-only | Read-only |
| B — Classification | Read-only | Read-only | Read-only |
| C — Nature of change | Read-only | Read-only | Read-only |
| D — Attachments | Read-only | Read-only | Read-only |
| E — Checklist | Own column, editable | Own column, editable | Read-only (all columns) |
| F — Decision | Editable (only after all reviewers done) | Read-only | Read-only |
| G — Signature block | Own block | Own block | Read-only |
| H — Verify | Can verify | Can verify | Read-only |
| I — Recommend / Approve | Can recommend | — | Can approve |

For **Updating track**: only the Chair has a checklist column. Members are not assigned.
For **Revision track**: Chair + both members each fill their own column independently.

---

## Service API: `SyllabusReviewFormService`

All methods follow the project's error-handling rules: try-catch, log on failure, rethrow or dispatch toast — never silently swallow.

```php
findOrCreate(Syllabus $syllabus): SyllabusReviewForm
setClassification(SyllabusReviewForm, string $classification): void
syncNatureOfChange(SyllabusReviewForm, array $selectedTypes): void
syncAttachments(SyllabusReviewForm, array $submitted): void
saveChecklistResponse(SyllabusReviewForm, int $reviewerUserId, string $criterionCode, string $response, ?string $comments): void
recordDecision(SyllabusReviewForm, string $decision, ?string $requiredActions, ?string $targetDate): void
savePartHResponse(SyllabusReviewForm, string $response): void
verifyPartH(SyllabusReviewForm, int $verifierUserId): void
recommendApproval(SyllabusReviewForm, int $chairUserId): void
recordDeanApproval(SyllabusReviewForm, int $deanUserId): void
generateReviewFormHtml(SyllabusReviewForm): string
isChecklistComplete(SyllabusReviewForm, int $reviewerUserId): bool
buildSnapshot(SyllabusReviewForm): array
```

Side effects per method:
- `recordDecision()` → fires notification + triggers reclassification reset if applicable (DB transaction wraps the reset)
- `saveChecklistResponse()` → checks completion after each save; flips `SyllabusReviewer.status` to `approved` if complete
- `recordDeanApproval()` → DB transaction: updates `Syllabus.status`, sets `filed_at`, `filing_type`
- `generateReviewFormHtml()` → called by `SyllabusSnapshotService::saveVersion()`; writes file to disk; stores `review_form_path` + checksum

---

## End-to-End Sequence

```
1. AUTHOR — Opens wizard → Review tab
   └─