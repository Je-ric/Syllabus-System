# Review Form and Approval Workflow

Practical reference for how the F.003 review form, reviewer assignments, and approval workflow function in CSMS.

## Files Used (Source of Truth)

- Controllers
  - `app/Http/Controllers/Syllabus/SyllabusReviewFormController.php` — Review form preview (live and saved snapshots)
  - `app/Http/Controllers/Syllabus/ReviewQueueController.php` — Review queue index and show pages
- Livewire Components
  - `app/Livewire/Syllabus/SyllabusReviewPage.php` — Reviewer-facing checklist and decision interface
  - `app/Livewire/Syllabus/Steps/ReviewStep.php` — Author-facing review form preparation and approval
- Services
  - `app/Services/Syllabus/Review/SyllabusReviewFormService.php` — All review form mutations and business logic
  - `app/Services/Syllabus/Snapshots/SyllabusSnapshotService.php` — Review form HTML snapshot generation and retrieval
- Models
  - `app/Models/SyllabusReviewForm.php` — Main review form record
  - `app/Models/SyllabusReviewer.php` — Reviewer assignments
  - `app/Models/SyllabusReviewNatureOfChange.php` — Part C nature of change options
  - `app/Models/SyllabusReviewAttachment.php` — Part D attachments
  - `app/Models/SyllabusReviewChecklistResponse.php` — Part E checklist responses
- Data
  - `app/Data/ReviewCriteria.php` — Fixed criterion definitions (A, B, C_updating, C_revision)
- Views
  - `resources/views/Syllabus/template/review_form.blade.php` — F.003 form template (live preview)
  - `resources/views/Syllabus/review_queue/index.blade.php` — Review queue listing
  - `resources/views/Syllabus/review_queue/show.blade.php` — Review queue detail page
  - `resources/views/livewire/syllabus/review-page/review-page.blade.php` — Reviewer checklist interface
  - `resources/views/livewire/syllabus/review-page/partials/` — Decision, chair approval, faculty response, reviewer status
  - `resources/views/livewire/syllabus/wizard/steps/review.blade.php` — Author review form preparation
  - `resources/views/livewire/syllabus/wizard/steps/partials/review/` — Review form sub-components
- Routes
  - `routes/web.php` (review form routes)
    - `GET /syllabus/{syllabus}/review-form/preview` — Live preview (author, reviewers, deans, admins)
    - `GET /syllabus/complete/{completeSyllabus}/review-form/preview` — Saved snapshot preview
    - `GET /syllabus/review-queue` — Review queue index (reviewers, admins)
    - `GET /syllabus/review-queue/{syllabus}` — Review queue show (assigned reviewers, admins)

## Key Concepts

- **Two tracks**: Updating (minor changes) vs Revision (substantial changes) — determines which Part C criteria apply.
- **Three-part review**: Author prepares form → Reviewers complete checklist → Chair records decision → Dean approves.
- **Checklist responses**: Per-reviewer, per-criterion responses (satisfied/not_satisfied/not_applicable) with optional comments.
- **Decision types**: `approved_as_updating`, `approved_as_revision`, `approved_with_corrections`, `returned_for_revision`, `reclassified_as_revision`.
- **Reviewer roles**: Chair (records decision, recommends approval) and Members (complete checklist only).
- **Part H compliance**: For `approved_with_corrections` and `returned_for_revision` decisions, faculty must respond using Part H. For `approved_with_corrections`, verifier must confirm compliance. For `returned_for_revision`, faculty can resubmit for review.
- **Required actions**: Only required for `returned_for_revision` decisions (not for `approved_with_corrections`). Chair can update required actions/comments and deadline for `returned_for_revision` decisions.
- **Snapshots**: Review form HTML is frozen and stored on disk when syllabus is approved for archival purposes.

## Conditions (If / Then)

### Author Preparation (ReviewStep Livewire)

- If author saves classification:
  - Then `SyllabusReviewFormService::setClassification()` is called.
  - If classification changes from updating to revision (or vice versa):
    - Then all track-specific checklist responses (C_updating/C_revision) are deleted.
    - Then classification is updated in DB transaction.
  - If classification is unchanged: no-op.

- If author saves nature of change:
  - Then `SyllabusReviewFormService::syncNatureOfChange()` is called.
  - Updating track options: schedule_calendar, faculty_contact, references_textbooks, typographical_formatting, minor_administrative, other_updating.
  - Revision track options: stakeholder_feedback, cqi_findings, policy_curricular, accreditation_qa, change_in_cos_po_mapping, change_in_grading_assessments_content, other_revision.
  - Then existing nature_of_change rows are deleted and new ones inserted per selected types.

- If author saves attachments:
  - Then `SyllabusReviewFormService::syncAttachments()` is called.
  - Attachment types: draft_syllabus, cqi_report, feedback_summary, policy_memo, mapping_evidence, other.
  - Then existing attachment rows are deleted and new ones inserted per selected types.
  - If `other` is selected: `other_label` must be provided.

- If author submits review form:
  - Then classification must be set (validation error if null).
  - Then at least one nature of change must be selected (validation error if empty).
  - Then `submitted_at` is set to now.
  - Then syllabus status is set to `under_review`.
  - Then AuditLog recorded.

### Reviewer Access (SyllabusReviewPage Livewire)

- If user accesses review page:
  - If user has role `admin`: access granted, uses first real assignment if exists.
  - If user is not admin:
    - Then user must be assigned as reviewer for this syllabus.
    - If not assigned: abort with 403.
  - Then reviewer assignment is loaded with role (chair/member) and status.
  - Then checklist responses are loaded for this reviewer.
  - Then decision fields are pre-filled if already recorded.

### Reviewer Checklist (SyllabusReviewPage Livewire)

- If reviewer saves a criterion response:
  - Then response must be one of: satisfied, not_satisfied, not_applicable.
  - Then criterion code must be valid (exists in ReviewCriteria).
  - Then `SyllabusReviewFormService::saveChecklistResponse()` is called.
  - Then response is upserted per reviewer_user_id + criterion_code.
  - If all required criteria for the classification are answered:
    - Then reviewer assignment status is set to `approved`.
  - Then progress percentage updates in UI.

- If classification is not set by author:
  - Then reviewer can still save responses but sees warning banner.
  - Then track-specific criteria (C_updating/C_revision) are not available.

### Chair Decision (SyllabusReviewPage Livewire)

- If chair saves decision:
  - Then user must have chair role (checked in assignment, not just user role).
  - Then decision must be one of: approved_as_updating, approved_as_revision, approved_with_corrections, returned_for_revision, reclassified_as_revision.
  - If decision is `returned_for_revision`:
    - Then required_actions must be filled.
    - Then target_compliance_date must be set.
  - Then `SyllabusReviewFormService::recordDecision()` is called.
  - Then decision, decision_made_at, decision_made_by are recorded.
  - If decision is `returned_for_revision`:
    - Then syllabus status is set to `for_revision`.
  - If decision is `reclassified_as_revision`:
    - Then all checklist responses are deleted.
    - Then all reviewer statuses are reset to `pending`.
    - Then classification is set to `revision`.
  - Then AuditLog recorded.

- If chair updates decision for `returned_for_revision`:
  - Then required actions and deadline fields are shown (even if decision unchanged).
  - Then chair can modify required_actions and target_compliance_date.
  - Then save button is disabled if no changes detected.
  - Then button text shows "Update Decision" when updating existing decision.

### Chair Recommendation (SyllabusReviewPage Livewire)

- If chair recommends approval:
  - Then user must have chair role.
  - Then a decision must already be recorded.
  - Then `SyllabusReviewFormService::recommendApproval()` is called.
  - Then recommended_by_chair_id and recommended_by_chair_at are set.
  - Then AuditLog recorded.

### Part H Faculty Response (ReviewStep Livewire)

- If decision is `approved_with_corrections` or `returned_for_revision`:
  - Then Part H panel is shown to faculty in review form.
  - If faculty saves response:
    - Then part_h_faculty_response is saved.
    - Then AuditLog recorded.
  - If decision is `approved_with_corrections`:
    - Then faculty sees "Submit Response" button.
    - Then response awaits verification by chair/admin.
  - If decision is `returned_for_revision`:
    - Then faculty sees "Save Response" and "Resubmit for Review" buttons.
    - If faculty clicks "Resubmit for Review":
      - Then part_h_faculty_response is saved.
      - Then decision, required_actions, and target_compliance_date are reset.
      - Then syllabus status is set to `pending_review`.
      - Then all reviewer statuses are reset to `pending`.
      - Then AuditLog recorded.
      - Then reviewers are notified of resubmission.

### Part H Verification (SyllabusReviewPage Livewire)

- If a verifier confirms Part H compliance:
  - Then user must be authorized (typically chair or admin).
  - Then `SyllabusReviewFormService::verifyPartH()` is called.
  - Then part_h_verified_by and part_h_verified_at are set.
  - Then AuditLog recorded.

### Dean Approval (ReviewStep Livewire)

- If dean approves syllabus:
  - Then user must have dean role.
  - Then `SyllabusApprovalService::recordDeanApproval()` is called.
  - Then approved_by_dean_id and approved_by_dean_at are set.
  - Then syllabus status is set to `approved`.
  - Then review form HTML snapshot is generated and stored.
  - Then AuditLog recorded.

### Review Form Preview (SyllabusReviewFormController)

- If user requests live preview:
  - Then user must be author, assigned reviewer, dean, or admin.
  - If unauthorized: abort with 403.
  - Then syllabus is loaded with all review form relationships.
  - Then review_form.blade.php template is rendered with current data.
  - Then HTML is returned with Content-Type: text/html.

- If user requests saved snapshot preview:
  - Then user must be author, assigned reviewer, dean, or admin.
  - If unauthorized: abort with 403.
  - Then complete_syllabus must have review_form_path.
  - If no path: abort with 404.
  - Then HTML is loaded from disk via `SyllabusSnapshotService::getSavedHtml()`.
  - If file not found: abort with 404.
  - Then HTML is returned with Content-Type: text/html.

### Review Queue (ReviewQueueController)

- If user accesses review queue index:
  - If user has role `admin`: all reviewer assignments are shown.
  - If user is not admin: only assignments for this user are shown.
  - Then assignments are sorted: pending first, then completed.
  - Then view renders with pending and done groupings.

- If user accesses review queue show page:
  - If user has role `admin`: access granted.
  - If user is not admin:
    - Then user must be assigned as reviewer for this syllabus.
    - If not assigned: abort with 403.
  - Then syllabus detail page is rendered.

## Sequences (Typical Flow)

### Author Submits Review Form

1. Author navigates to Review step in wizard.
2. Author selects classification (Updating or Revision).
3. Author selects nature of change checkboxes (track-specific options).
4. Author selects attachment checkboxes (required and optional documents).
5. Author clicks "Submit for Review".
6. System validates classification and nature of change.
7. System sets submitted_at and changes syllabus status to under_review.
8. Reviewers see syllabus in their review queue.

### Reviewer Completes Checklist

1. Reviewer opens syllabus from review queue.
2. Reviewer sees F.003 classification and progress bar.
3. Reviewer answers each criterion (Satisfied/Not Satisfied/N/A).
4. Reviewer optionally adds comments per criterion.
5. When all criteria answered: reviewer status automatically changes to approved.
6. Reviewer sees "Complete" status indicator.

### Chair Records Decision

1. Chair opens syllabus from review queue (all reviewers completed).
2. Chair reviews all checklist responses across reviewers.
3. Chair selects decision type (e.g., Approved as Updating).
4. If decision requires corrections: chair enters required actions and compliance date.
5. Chair clicks "Save Decision".
6. System records decision with timestamp and chair ID.
7. System may change syllabus status depending on decision.
8. Chair clicks "Recommend Approval" (optional, before dean approval).

### Dean Approves Syllabus

1. Dean opens syllabus from review queue or syllabus index.
2. Dean reviews chair's decision and recommendation.
3. Dean clicks "Approve".
4. System records dean approval with timestamp.
5. System changes syllabus status to approved.
6. System generates and stores review form HTML snapshot.
7. Syllabus is now final and cannot be edited.

### Approved with Corrections Flow

1. Chair records decision as "Approved with Corrections" (no required actions/date needed).
2. Faculty sees Part H panel in review form.
3. Faculty enters response describing corrections made.
4. Faculty clicks "Submit Response".
5. Verifier (chair/admin) confirms compliance.
6. Part H is verified and syllabus proceeds to dean approval.

### Returned for Revision Flow

1. Chair records decision as "Returned for Revision" with required actions and compliance deadline.
2. Faculty sees required actions and deadline in review form.
3. Faculty sees Part H panel to describe revisions made.
4. Faculty can save response draft or resubmit for review.
5. If faculty clicks "Resubmit for Review":
   - Part H response is saved.
   - Decision, required actions, and deadline are reset.
   - Syllabus status changes to `pending_review`.
   - All reviewer statuses reset to `pending`.
   - Reviewers are notified to review again.
6. Reviewers complete checklist for revised syllabus.
7. Chair records new decision based on revised content.

## UI Notes

### review_form.blade.php
- Standalone HTML template for F.003 form rendering.
- Auto-fills Part A with course, program, college, and faculty data.
- Displays classification, nature of change, and attachments as checked boxes.
- Shows checklist in table format with reviewer columns.
- Displays decision, chair recommendation, and dean approval status.
- Used for both live preview and saved snapshots.
- Supports inline CSS for snapshot rendering without external dependencies.

### SyllabusReviewPage (review-page.blade.php)
- Left panel: checklist criteria grouped by section (A, B, C).
- Each criterion shows Satisfied/Not Satisfied/N/A buttons with Alpine.js state.
- Progress bar shows completion percentage for current reviewer.
- Right panel: syllabus info, other reviewers status, decision form (chair only).
- Warning banner if F.003 not yet submitted by author.
- Chair decision form shows required actions/date fields only for `returned_for_revision` decisions.
- Chair can update required actions and deadline for existing `returned_for_revision` decisions.
- Save button disabled when no changes detected, shows "Update Decision" when updating.
- All saves use async Alpine.js calls with loading states.

### ReviewStep (wizard/steps/review.blade.php)
- Author interface for preparing review form.
- Classification selector (Updating/Revision) with track-specific nature of change options.
- Attachment checkboxes with "Other" label input.
- Reviewer assignment interface (add/remove chair and members).
- Dean approval button (appears when all prerequisites met).
- Live preview button opens review_form.blade.php in new tab.
- Shows saved versions drawer for historical review form snapshots.
- Part H panel shown for `approved_with_corrections` and `returned_for_revision` decisions.
- For `returned_for_revision`: shows required actions, deadline, and resubmit functionality.
- For `approved_with_corrections`: shows response textarea for corrections description.

Related docs:
- `MD/10_Syllabus_Creation_and_Management.md`
- `MD/11_Syllabus_Approval_Workflow.md`
- `MD/12_Syllabus_Revision_History.md`
