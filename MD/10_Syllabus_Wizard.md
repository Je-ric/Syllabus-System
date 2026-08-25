# Syllabus Wizard (Complete Functional Memory Guide)

Practical reference for how the Syllabus Wizard works today.
Use this when changing logic, debugging behavior, or explaining the flow to non-technical users.

## Files Used (Source of Truth)

- Step list source
  - `app/Models/Syllabus.php` (`getWizardSteps()`)
- Parent wizard
  - `app/Livewire/Syllabus/SyllabusWizard.php`
  - `resources/views/livewire/syllabus/syllabus-wizard.blade.php`
- Step components
  - `app/Livewire/Syllabus/Wizard/Steps/AcademicCalendarStep.php`
  - `app/Livewire/Syllabus/Wizard/Steps/ComponentsStep.php`
  - `app/Livewire/Syllabus/Wizard/Steps/CourseOutcomesStep.php`
  - `app/Livewire/Syllabus/Wizard/Steps/WeeklyCoverageStep.php`
  - `app/Livewire/Syllabus/Wizard/Steps/CourseEvaluationStep.php`
  - `app/Livewire/Syllabus/Wizard/Steps/ReviewStep.php`
- Step views
  - `resources/views/livewire/syllabus/wizard/steps/academic-calendar.blade.php`
  - `resources/views/livewire/syllabus/wizard/steps/course-components.blade.php`
  - `resources/views/livewire/syllabus/wizard/steps/course-outcomes.blade.php`
  - `resources/views/livewire/syllabus/wizard/steps/weekly-coverage.blade.php`
  - `resources/views/livewire/syllabus/wizard/steps/course-evaluation.blade.php`
  - `resources/views/livewire/syllabus/wizard/steps/review.blade.php`
- Service classes (business logic)
  - `app/Services/Syllabus/CourseOutcomeService.php`
  - `app/Services/Syllabus/Weeks/WeekGenerationService.php`
  - `app/Services/Syllabus/Weeks/WeekLockService.php`
  - `app/Services/Syllabus/Weeks/WeekContentService.php`
  - `app/Services/Syllabus/Weeks/WeekResourceService.php`
  - `app/Services/Syllabus/CourseEvaluationService.php`
  - `app/Services/Syllabus/Review/SyllabusReviewService.php`
  - `app/Services/Syllabus/SyllabusRevisionHistoryService.php`
  - `app/Services/Syllabus/Review/SyllabusApprovalService.php`
  - `app/Services/Syllabus/Snapshots/SyllabusSnapshotService.php`
- Models
  - `app/Models/Syllabus.php`
  - `app/Models/SyllabusWeek.php`
  - `app/Models/WeekContent.php`
  - `app/Models/CourseComponent.php`
  - `app/Models/CourseComponentSchedule.php`
  - `app/Models/CourseOutcome.php`
  - `app/Models/SyllabusEvaluationItem.php`
  - `app/Models/CompleteSyllabus.php`
  - `app/Models/SyllabusReviewer.php`
  - `app/Models/SyllabusRevision.php`

Related docs: `MD/02_Academic_Calendar_and_Events.md`, `MD/04_Course_Management.md`

## Security Implementation

### Authorization
- **Role-Based Access Control**: Wizard routes protected by `role:admin,faculty,ovpaa` middleware.
- **Ownership Validation**: SyllabusWizard mount verifies `prepared_by == Auth::id()` for existing syllabi (403 if not owner).
- **Admin Override**: SyllabusController::authorizeSyllabusAccess() allows admin access regardless of ownership.
- **Scope-Based Access**: Reviewers and deans have read-only access to syllabi they're assigned to review.

### Input Validation
- **Parameter Validation**: All IDs (syllabusId, courseId) validated to ensure existence in database.
- **Server-Side Validation**: Each step component implements Laravel validation rules.
- **SecurityValidator**: Used for injection detection in free-text fields (revision highlights, contributors, material URLs).
- **PO Mapping Validation**: Course selection blocked if course has no PO mappings.

### Business Logic Security
- **Status-Based Access Control**: Syllabus editing restricted to draft and for_revision statuses only.
- **Duplicate Prevention**: System checks for existing syllabus by user for course before creating new ones.
- **Week Locking**: Exam and non-teaching weeks are locked to prevent tampering with assessment schedules.
- **Reviewer Conflict Prevention**: Reviewer dropdown excludes preparer and component instructors.

### Transaction Safety
- **Database Transactions**: Week content saves, course component saves, and review form submissions run inside DB transactions.
- **Week Generation**: Regeneration operations use transactions to ensure atomic deletion and recreation.
- **Review Form Resubmission**: Complex multi-table operations wrapped in transactions.

### Audit Logging
- **Action Logging**: Syllabus creation, submission, version saves, reviewer assignments, and revision changes logged.
- **User Attribution**: All audit logs include the authenticated user who performed the action.
- **Module Tracking**: AuditLog::record() includes module name for traceability.

### Rate Limiting
- **Current Status**: Rate limiting is not currently implemented on wizard endpoints.
- **Recommended Enhancement**: Add rate limiting to syllabus creation and week generation endpoints to prevent automated abuse.

## What It Is

- A guided 6-step multi-step process to create or edit one syllabus.
- New syllabi start as `draft`.
- Users fill required sections and click **Save as Done** which freezes a version and sends to review.
- Saving is event-driven between parent wizard and child step components.
- Step components stay mounted (shown/hidden) for faster switching.

## Step Order (Current)

The UI renders these steps:

1. `academic_calendar`
2. `course_components`
3. `course_outcomes`
4. `weekly_coverage`
5. `course_evaluation`
6. `review`

Any legacy mention of `co_po_mapping` is outdated for this wizard screen.

## Start Conditions (If / Then)

- If `syllabusId` is present:
  - Then load existing syllabus.
  - If the logged-in user is not the preparer:
    - Then abort with `403 Unauthorized`.
  - If saved `current_step` in DB is valid:
    - Then use it.
  - If saved `current_step` is invalid:
    - Then force `academic_calendar` and update DB.
  - **Security**: Ownership validation prevents unauthorized access to syllabi.

- If `syllabusId` is missing but `courseId` is present:
  - If the course has no PO mappings (`course_curriculum_maps` is empty):
    - Then redirect back to course selection with an error toast.
    - Then no syllabus row is created.
  - If the course has PO mappings:
    - If a syllabus already exists for this course by this user:
      - Then redirect to the existing syllabus wizard with an info toast.
    - If no existing syllabus:
      - Then create a new syllabus row immediately with:
        - `status = draft`, `current_step = academic_calendar`, `prepared_by = current user`
        - `academic_calendar_id = null`
  - **Security**: PO mapping validation prevents incomplete syllabus creation; duplicate detection prevents data duplication.

- If neither `syllabusId` nor `courseId` is provided:
  - Then abort with `404`.

> Any faculty user can create a syllabus for any course regardless of department assignment — as long as PO mappings exist.

## Navigation Sequence (Actual Runtime)

When a user clicks `Next`, `Previous`, or a tab:

1. Parent dispatches `syllabus-save-step` for the current step.
2. Parent immediately changes `$currentStep` to target step in the same request.
3. Parent persists `syllabi.current_step`.
4. Parent dispatches `syllabus-step-changed` to notify the newly active step.
5. Child step (the old one) saves itself when it hears `syllabus-save-step`.
6. Child dispatches `syllabus-step-saved`.
7. Parent receives `syllabus-step-saved`, clears dirty flag, refreshes syllabus model.

Switching step and saving happen in one round trip for speed.

### Course Outcomes Navigation Guard

- If the user navigates away from `course_outcomes` while `stepDirty['course_outcomes']` is `true`:
  - Then `saveAndNavigate()` dispatches `request-co-save-and-navigate` instead of navigating immediately.
  - Alpine's `coManager` intercepts this event, calls `saveAll()` to persist pending COs, then calls `$wire.onCoSaveAndNavigate(toStep)`.
  - `onCoSaveAndNavigate()` dispatches `navigate-after-save` which completes the navigation.
  - This prevents silent data loss when the user has unsaved CO rows and clicks Next.

## UI Conditions (If / Then)

- If user clicks `Next`, `Previous`, or a step tab:
  - Then `_navigating` Alpine flag is set to `true`, showing a full-screen "Saving & switching…" overlay.
  - Once the new step is rendered and `syllabus-step-changed` fires, `_navigating` is reset to `false`.
  - Uses Alpine `x-show="_navigating"` instead of `wire:loading` to avoid flicker from Livewire's debounce.
- If a tab is before the current step index:
  - Then it is styled as "completed" (visual only; not a validation gate).
- If a step has missing required fields:
  - Then an amber dot indicator appears on the tab.
  - The indicator is computed once per `render()` into `$stepMissing[]` and read from that cache in Blade — no repeated DB queries per loop iteration.
- The layout is two-column: main content (left) + sticky step navigator (right, desktop only).
  - Mobile: horizontal scrollable step strip at top.

## Save as Done (Primary Submit Action)

`saveAsDone()` replaces the traditional one-click submit. It:

1. Validates that `academic_calendar_id` is set.
2. Generates **three** HTML snapshots via `SyllabusSnapshotService`:
   - Complete syllabus
   - Abridged syllabus
   - Assessment plan
3. Wraps steps 3–6 in `DB::transaction()` with `lockForUpdate()` on `CompleteSyllabus` to prevent race conditions when two requests compute the same version number simultaneously.
4. Saves all three snapshots to local disk (`syllabus_snapshots` disk).
5. Mirrors to **Google Drive** silently (non-fatal if unavailable).
6. Creates a `CompleteSyllabus` version record with checksums and paths.
7. Sets syllabus `status = under_review`, `current_step = review`.
8. Records AuditLog.

The overlay shows progress messages: "Rendering syllabus…", "Uploading to Google Drive…", "Freezing version record…"

## Submit for Review

`submitForReview()` runs the step completion checks and sets status to `under_review`. It is the primary submit action on the Review step — the Submit for Review button is fully functional.

Gate checks (all must pass before submit):
- Academic calendar selected.
- Course components complete.
- At least one non-blank course outcome saved.
- At least one syllabus week exists.
- Course evaluation complete (all assessable weeks have weights).
- Course Outcomes step must not have unsaved pending changes (`stepDirty['course_outcomes']` must be false).

## Step-by-Step Rules

### 1) Academic Calendar Step

**Purpose**: Select one academic calendar for this syllabus.

- Loads all calendars ordered by `academic_year DESC`, `semester DESC`.
- Stores as plain arrays (not Eloquent models) to keep Livewire snapshot small.
- Uses `getFormattedSemester()` for display.
- `isLoaded` is reset to `false` in `onStepChanged()` before calling `loadData()`, so returning to this step always fetches fresh calendar data.

Conditions:
- If user changes the dropdown:
  - Then `updatedAcademicCalendarId()` runs.
  - If component is not yet loaded (`isLoaded = false`):
    - Then returns early (prevents double-save during mount).
  - If validation passes:
    - Then save `academic_calendar_id` to syllabus.
    - Then dispatch `syllabus-step-saved`.
    - Then dispatch `syllabus-calendar-updated` → weekly coverage reloads.

Validation:
- Required.
- Must exist in `academic_calendars`.
- No uniqueness constraint — the same calendar can be reused across multiple syllabi (even for the same course).
- **Security**: Server-side validation prevents injection attacks and ensures calendar ID exists in database.

### 2) Course Components Step

**Purpose**: Capture lecture and laboratory teaching details, schedules, and consultation hours.

**Save-before-navigate pattern**: This step has LEC and LAB sections managed by independent Alpine components. When the user navigates away, the parent dispatches `request-push-and-navigate` → each Alpine section pushes a save promise to `window._beforeSaveAllPromises` → `$wire.onPushAndNavigate()` awaits all promises, saves via Livewire, then dispatches `navigate-after-save` → parent completes navigation.

Conditions:
- If first load and no LEC row exists:
  - Then prefill LEC name/email/phone/office from logged-in user profile (if available).
  - Prefill only fills fields that are currently empty.

- If course has lab (`has_lec_lab = true`):
  - Then LAB section is shown and required.
- If course has no lab:
  - Then LAB section hidden, not required.

Fields:
- Instructor name, email, phone (optional), office (optional) — for LEC and LAB.
- **Schedule management**: dynamic add/remove schedule rows managed entirely in Alpine (client-side). No public Livewire methods for schedule mutations.
- **Consultation hours**: inline rows to manage user consultation hours (stored in `user_consultation_hours`).
- Class hours and performance standard are read-only from the course record.
- `lec_performance_standard` defaults to `60.00`.

Accessibility:
- All time inputs have `aria-label="Start time"` / `aria-label="End time"`.
- Day selects have `aria-label="Day"`.
- Each schedule/consultation row is wrapped in `role="group"` with a dynamic `aria-label`.

Dirty tracking:
- If any LEC/LAB field changes (after `isLoaded`):
  - Then mark step dirty.
  - Then do not auto-save immediately.

Save behavior:
- On `syllabus-save-step`, `saveComponents()` runs:
  - `updateOrCreate` for LEC component (always).
  - `updateOrCreate` for LAB component (only when course has lab).
  - Schedule sync: delete all existing, re-create from current state.
  - Manual save button also available.
- **Security**: Dirty tracking prevents data loss; schedule conflict detection prevents overlapping consultation hours.

### 3) Course Outcomes Step

**Purpose**: Create and maintain CO list for the syllabus.

**Current implementation**: Uses `CourseOutcomeService` with file-based save design:
- `saveAll(array $drafts)` — receives drafts from Alpine, creates or updates each CO, deletes any not in the set.
- `deleteSingle(int $outcomeId)` — immediate delete with DB re-sequencing.
- COs are stored in a Livewire-owned `outcomes` array with `id`, `co_code`, `description`.

Conditions:
- If there are no DB rows yet:
  - Then the UI still shows 1 blank row (form is never empty on first visit).
- If user clicks **Add CO**:
  - Then Alpine appends a new blank row (no save yet).
- If user removes a row with an existing DB `id`:
  - Then `deleteSingle()` deletes it immediately in DB.
  - Then outcome list is refreshed from service.
- If parent requests save (`syllabus-save-step` for `course_outcomes`):
  - Then step dispatches `syllabus-step-saved` without saving (save handled by Alpine's `saveAll` before navigation).

Navigation guard:
- If `stepDirty['course_outcomes']` is true when navigating away:
  - Then `saveAndNavigate()` dispatches `request-co-save-and-navigate`.
  - Alpine's `coManager` auto-saves, then calls `onCoSaveAndNavigate(toStep)` to complete navigation.
  - This prevents silent discard of pending CO rows.

Save behavior (via `saveAll`):
- Blank descriptions are ignored.
- If all rows are blank, save exits early with warning toast.
- Saved COs are always resequenced and re-coded as `CO1`, `CO2`, ... in save order.
- After save: `outcomes` is refreshed from DB.
- **Security**: Dirty tracking prevents data loss; batch operations reduce database calls.

**PO Reference table**:
- If syllabus course + program outcomes exist:
  - Then show each PO with its PO code, PO text, and the I/E/D level from `course_curriculum_maps`.
- Course Info and PO Reference drawers are accessible via buttons in the sticky sidebar.

### 4) Weekly Coverage Step

**Purpose**: Generate weekly records and per-week teaching content for LEC/LAB.

Business logic is split across four service classes:
- `WeekGenerationService` — create/regenerate weeks from academic calendar
- `WeekLockService` — compute locked weeks based on calendar events
- `WeekContentService` — populate, save, reset week content
- `WeekResourceService` — add/remove references and online materials

Generation rules:
- If no academic calendar is selected: block generation.
- If no LEC and no LAB component exists: block generation.

- If weeks already exist:
  - Then `loadData()` skips creating duplicates.
  - If user clicks **Regenerate**:
    - Then a `wire:confirm` dialog is shown before proceeding.
    - Then all existing weeks + week contents are deleted and recreated from the calendar.
    - Then all previously encoded weekly coverage is lost.

- If weeks do not exist:
  - Then create sequential week records from calendar start/end date in 7-day blocks.
  - Then create default `WeekContent` rows for available components (LEC/LAB).

- If the academic calendar contains an event type `break`:
  - Then any 7-day block that contains a break date is skipped entirely.
  - Then the cursor advances to the next block.
  - Then `week_no` does not increment for the skipped block.

Locking rules:
- If a week range contains an event type `exam`:
  - Then that week is marked locked as `exam`.
  - Then UI shows an "Exam Week" badge and disables editing.
  - Then assessment task is auto-filled for each component:
    - LEC: `1st Term Exam`, `2nd Term Exam`, `Final Term Exam` (in order, capped)
    - LAB: `1st Term Practical Exam`, `2nd Term Practical Exam`, `Final Term Practical Exam` (in order, capped)
- If a week range contains an event type `non_teaching`:
  - Then that week is marked locked as `non_teaching`.
  - Then UI shows a "Non-Teaching Week" badge and disables editing.
  - Then assessment task is auto-filled as `Non-Teaching Week` for all components.
- If a week is locked:
  - Then server-side guards in `WeekContentService`, `WeekResourceService` prevent:
    - `saveWeek`, `saveAllWeeklyEntries`, `resetWeek`
    - `addReference`, `removeReference`, `addMaterial`, `removeMaterial`
  - **Security**: Server-side locking prevents tampering with exam and non-teaching schedules.

Editing rules:
- Uses `weekInputs['w{week_no}']` key format to avoid PHP numeric key coercion.
- If user collapses a week and opens a different week:
  - Then Alpine watches open week and triggers `$wire.saveWeek(oldWeekNo)`.
- Save All persists all unlocked weeks via `WeekContentService::save()`.
- `loadData()` is never called from save paths to avoid overwriting in-progress edits.
- `$syllabusWeeks` is a protected non-serialised Collection rebuilt in `boot()` and populated in `loadData()`. It is not re-queried on every `render()`.

Week content fields (saved via modal or inline):
- `course_outcome_id`, `learning_outcomes`, `assessment_task`, `topic`, `teaching_activities`
- References: dynamic `[['text' => '']]` rows
- Materials: dynamic `[['name' => '', 'url' => '']]` rows
- **Security**: Material URLs validated with SecurityValidator for injection attempts before saving.

MVGO rule (Week 1):
- If `week_no === 1`:
  - Then CO selection is replaced by a fixed MVGO badge (Mission-Vision-Goals-Objectives).
  - Then assessment task is optional.
  - If an assessment task is entered for Week 1: it appears in Course Evaluation; otherwise it does not.

Calendar event display:
- If the week is editable and has events: all events displayed with type chip.
- Event dot colors: `holiday` → emerald, `break` → blue, others → amber.
- If the week is locked: only `exam` / `non_teaching` events shown in the lock alert; others shown separately under "Other events this week".

LEC/LAB switching:
- If both components exist: user can switch tab.
- On switch: save current component content, change active component, reload inputs.
- If LAB is requested but course has no LAB: ignored.

Week edit modal accessibility:
- Modal container has `role="dialog"`, `aria-modal="true"`, `aria-labelledby="week-modal-title"`.
- Escape key closes the modal via `x-on:keydown.escape.window`.

### 5) Course Evaluation Step

**Purpose**: Encode the weight (%) for each assessment task generated from Weekly Coverage.

Row generation rules (handled by `CourseEvaluationService::loadRows()`):
- If Weekly Coverage has no assessment tasks: show empty state.
- If a week has `assessment_task` empty for both LEC and LAB: excluded from evaluation table.
- If `assessment_task` is `Non-Teaching Week` on either side: excluded.
- If `assessment_task` contains the word `exam` (case-insensitive): treated as exam row (`is_exam = true`), visually highlighted.
  - Term label increments in order (capped): `1st Term`, `2nd Term`, `Final Term`.
- If Week 1 has an assessment task: row flagged `is_mvgo = true`, outcome label forced to `MVGO`.
- If course has no LAB: only LEC columns render, total expected = 100%.
- If course has LAB: both LEC + LAB columns, totals expected as 67% (LEC) + 33% (LAB) or whatever the performance standard dictates.
- If a component has no task for a row: shows "No LEC task" / "No LAB task", weight input disabled.

Inputs:
- `inputs[week_content_id]['weight']` — wire:model.lazy for weight.
- `inputs[week_content_id]['outcome_label']` — auto-resolved CO code, editable except for MVGO rows.

Save rules (via `CourseEvaluationService::persist()`):
- If **Save Evaluation** clicked:
  - Then save all rows via `SyllabusEvaluationItem::updateOrCreate()` per `week_content_id`.
  - If a row is exam: `kind = 'exam'`, `exam_type` mapped from term label.
  - Else: `kind = 'activity'`.
- Running totals recomputed on every weight change via `updated()` hook.
- **Security**: Dirty tracking prevents data loss; numeric validation ensures weights are valid integers.

Completion rules (submit gate):
- Assessable week contents: `assessment_task` not empty AND not `Non-Teaching Week`.
- If zero assessable: step incomplete.
- If any assessable week content has no weight (NULL): step incomplete.

### 6) Review Step

**Purpose**: Final summary, revision history, reviewer management, and submission.

Summary data loaded includes:
- Selected academic calendar
- Course component details (LEC + LAB)
- Course outcomes list
- Weekly coverage count and exam-type mapping
- Version history (`completeVersions` from `CompleteSyllabus`)
- Latest saved version info

**Property design**:
- `$course` is stored as a plain `array` (not an Eloquent model) to avoid bloating the Livewire snapshot JSON.
- `$syllabusWeeks` and `$completeVersions` are typed as `array` — plain arrays, not Collections — to prevent dehydration issues on re-hydration.
- `$academicCalendars` is not loaded in ReviewStep; the review step reads calendar info from the already-loaded `$syllabus->academicCalendar` relation.
- Dean and faculty user lists are loaded once in `loadData()` / `loadReviewerLists()` and stored as plain arrays. They are not re-queried on every `render()`.

**Reviewer management** (via `SyllabusReviewService`):
- `addReviewer(?int $reviewerUserId)` — assigns a reviewer (auto-approved). Lives on `SyllabusWizard` because the Blade partial calls `$wire.$parent`.
- `removeReviewer(int $syllabusReviewerId)` — removes a reviewer.
- `updateReviewerStatus(int $syllabusReviewerId, string $status)` — updates status.
- After mutations, `syllabus-reviewers-updated` is dispatched so `ReviewStep::onReviewersUpdated()` refreshes the reviewer list.
- Render provides `$deanUsers` and `$facultyUsers` for dropdown selection.
- **Security**: Reviewer dropdown excludes preparer and component instructors to prevent conflict of interest.

**Revision History** (via `SyllabusRevisionHistoryService`):
- `saveRevision()` — all values as typed arguments, zero wire:model typing.
- `resequenceRevisions()` — renumbers all revisions 0, 1, 2… by current order.
- `removeRevision(int $revisionId)` — deletes and dispatches `revision-deleted`.
- Audit logging on create/update/delete.
- **Security**: Highlights and contributors fields validated with SecurityValidator for injection attempts before saving.

**Approval Signatures** (via `SyllabusApprovalService`):
- `saveApproved()` / `clearApproved()` — sets/clears `approved_by` on syllabus.
- `saveConcurred()` / `clearConcurred()` — sets/clears `concurred_by` on syllabus.
- Validation: Concurred-by must differ from Approved-by.
- **Security**: Validation prevents the same user from being both approver and concurrer.

**Submit for Review button**:
- Fully functional. Triggers `submitForReview()` on the parent wizard.
- All gate checks must pass before submission proceeds.

**Save as Done button**:
- Triggers `saveAsDone()` on the parent wizard (see section above).
- Progress overlay shown during snapshot generation.
- **Security**: Version freezing uses DB transactions with row locking to prevent race conditions.

## Event Contract (Parent ↔ Child)

Used events:
- `syllabus-save-step` — parent tells current child to save.
- `syllabus-step-saved` — child confirms save complete.
- `syllabus-step-changed` — parent notifies new active step.
- `syllabus-step-dirty` — child reports dirty state.
- `syllabus-calendar-updated` — academic calendar changed (weekly coverage reloads).
- `syllabus-course-outcomes-updated` — outcomes changed (other steps react).
- `syllabus-reviewers-updated` — reviewer changes (ReviewStep re-renders).
- `request-co-save-and-navigate` — parent asks Alpine coManager to save COs then navigate.
- `navigate-after-save` — Alpine/child tells parent to complete navigation after save.
- `lw-toast` — toast notification dispatch.

## Non-Technical Flow (Plain English)

1. Open syllabus wizard.
2. Pick the academic calendar.
3. Fill lecture/lab details and schedules.
4. Add course outcomes and save them.
5. Generate weeks and fill weekly coverage for editable weeks.
6. Encode Course Evaluation weights.
7. Review everything, manage revision history and reviewers.
8. Click **Submit for Review** — system sets status to `under_review` and redirects.
   - Or click **Save as Done** — system freezes a version snapshot and sends to review.

If something required is missing: the system blocks submit and tells the user what to fix.
If a week has an exam event: the week appears but is locked so faculty cannot encode coverage there.
If the user navigates away from Course Outcomes with unsaved changes: the system auto-saves before navigating.
