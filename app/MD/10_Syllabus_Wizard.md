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
  - `app/Livewire/Syllabus/Steps/AcademicCalendarStep.php`
  - `app/Livewire/Syllabus/Steps/ComponentsStep.php`
  - `app/Livewire/Syllabus/Steps/CourseOutcomesStep.php`
  - `app/Livewire/Syllabus/Steps/WeeklyCoverageStep.php`
  - `app/Livewire/Syllabus/Steps/CourseEvaluationStep.php`
  - `app/Livewire/Syllabus/Steps/ReviewStep.php`
- Step views
  - `resources/views/livewire/syllabus/steps/academic-calendar.blade.php`
  - `resources/views/livewire/syllabus/steps/course-components.blade.php`
  - `resources/views/livewire/syllabus/steps/course-outcomes.blade.php`
  - `resources/views/livewire/syllabus/steps/weekly-coverage.blade.php`
  - `resources/views/livewire/syllabus/steps/course-evaluation.blade.php`
  - `resources/views/livewire/syllabus/steps/review.blade.php`
- Key models used by steps
  - `app/Models/Course.php`
  - `app/Models/CourseComponent.php`
  - `app/Models/CourseOutcome.php`
  - `app/Models/AcademicCalendar.php`
  - `app/Models/AcademicCalendarEvent.php`
  - `app/Models/SyllabusWeek.php`
  - `app/Models/WeekContent.php`
  - `app/Models/SyllabusEvaluationItem.php`
  - `app/Models/Reference.php`
  - `app/Models/OnlineMaterial.php`

Related docs:
- `app/MD/02_Academic_Calendar_and_Events.md`
- `app/MD/04_Course_Management.md`
- `app/MD/13_Livewire_Beginner_Guide.md`

## What It Is

- A guided multi-step process to create or edit one syllabus.
- New syllabi start as `draft`.
- Users fill required sections and submit to chair review.
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

Note:
- Any legacy mention of `co_po_mapping` is outdated for this wizard screen.

## Start Conditions (If / Then)

- If `syllabusId` is present:
  - Then load existing syllabus.
  - If the logged-in user is not the preparer:
    - Then stop with `403 Unauthorized`.
  - If saved `current_step` in DB is valid:
    - Then use it.
  - If saved `current_step` is invalid:
    - Then force `academic_calendar` and update DB.

- If `syllabusId` is missing but `courseId` is present:
  - Then create a new syllabus row immediately:
    - `status = draft`
    - `current_step = academic_calendar`
    - `prepared_by = current user`
    - `academic_calendar_id = null`

- If neither `syllabusId` nor `courseId` is provided:
  - Then stop with `404`.

## Navigation Sequence (Actual Runtime)

When a user clicks `Next`, `Previous`, or a tab:

1. Parent dispatches `syllabus-save-step` for the current step.
2. Parent immediately changes `$currentStep` to the target step in the same request.
3. Parent persists `syllabi.current_step`.
4. Parent dispatches `syllabus-step-changed` to notify the newly active step.
5. Child step (the old one) saves itself when it hears `syllabus-save-step`.
6. Child dispatches `syllabus-step-saved`.
7. Parent receives `syllabus-step-saved`, clears dirty flag, refreshes syllabus model.

Practical meaning:
- Switching step and saving happen in one round trip for speed.
- Wizard does not wait for a second request just to switch tabs.

## Wizard UI Conditions (If / Then)

- If user clicks `Next`, `Previous`, or a step tab:
  - Then a full-screen “Saving & switching…” overlay is shown via `wire:loading` until Livewire re-renders.
- If a tab is before the current step index:
  - Then it is styled as “completed” (visual only; not a validation gate).
- If a child step component does not have a `:key` attribute:
  - Then Livewire keeps it mounted; step switching is show/hide only (intentional for speed and state preservation).

## Submit Conditions (If / Then)

When `Submit for Review` is clicked:

- If academic calendar is missing:
  - Then block submit and show error toast.
- If required Course Component fields are incomplete:
  - Then block submit and show error toast.
- If there is no non-empty Course Outcome in DB:
  - Then block submit and show error toast.
- If no syllabus week exists:
  - Then block submit and show error toast.
- If Course Evaluation is incomplete:
  - Then block submit and show error toast.
  - (Definition: at least one assessable week exists, and every assessable week_content has a non-null weight.)
- If `course_outcomes` step is marked dirty:
  - Then block submit and show warning toast to save course outcomes first.

- If all required checks pass:
  - Then set syllabus `status = under_review`.
  - Then set `current_step = review`.
  - Then redirect to syllabus show page with success toast.

## Step-by-Step Rules

### 1) Academic Calendar Step

Purpose:
- Select one academic calendar for this syllabus.

Conditions:
- If user changes the dropdown:
  - Then `updatedAcademicCalendarId()` runs.
  - If component is not yet loaded (`isLoaded = false`):
    - Then `updatedAcademicCalendarId()` returns early (prevents double-save during mount).
  - If validation passes:
    - Then save `academic_calendar_id` to syllabus.
    - Then dispatch `syllabus-step-saved`.
    - Then dispatch `syllabus-calendar-updated` (weekly coverage listens and reloads).

Validation:
- Required.
- Must exist in `academic_calendars`.
- Must be unique for the same course in `syllabi.academic_calendar_id` (excluding current syllabus).

### 2) Course Components Step

Purpose:
- Capture lecture and laboratory teaching details.

Conditions:
- If first load and no LEC row exists:
  - Then prefill LEC name/email/phone/office from logged-in user profile (if available).
  - Prefill only fills fields that are currently empty.

- If course has lab (`has_lec_lab = true`):
  - Then LAB section is shown and required.
- If course has no lab:
  - Then LAB is not required.

- If any LEC/LAB field changes:
  - Then mark the step dirty.
  - Then do not auto-save immediately.
  - (Implementation: changes are only tracked after the component is loaded, and only for `lec_*` / `lab_*` fields.)

Save behavior:
- On `syllabus-save-step`, validate completeness and save with `updateOrCreate`.
- Save LEC always.
- Save LAB only when the course has lab.

### 3) Course Outcomes Step

Purpose:
- Create and maintain CO list for the syllabus.

Conditions:
- Course Outcomes are stored in a Livewire-owned `rows` array:
  - Each row is `['id' => int|null, 'description' => string]`.
- If there are no DB rows yet:
  - Then the UI still shows 1 blank row (form is never empty on first visit).
- If user clicks **Add CO**:
  - Then append a new blank row (no validation gate on adding).
- If user removes a row with an existing DB `id`:
  - Then delete it immediately in DB.
  - Then remove the row from the local array.
- If parent requests save (`syllabus-save-step` for `course_outcomes`):
  - Then save runs in `silent` mode:
    - No warning toast when all rows are blank.
    - Step is still considered “saved” from the parent’s perspective.

Save behavior:
- Blank descriptions are ignored (not saved; no error).
- If all rows are blank:
  - Then save exits early.
  - If not silent (Save All button):
    - Then a warning toast is shown.
- Saved COs are always resequenced and re-coded as `CO1`, `CO2`, ... in save order.
- Save updates existing rows (matched by `id`) and creates missing rows.
- After save, any DB COs not present in the saved set are deleted (DB mirrors UI state).
- After a successful non-silent save:
  - Then mark `course_outcomes` as clean via `syllabus-step-dirty dirty:false`.

Data load (PO table in the step):
- If syllabus course + program outcomes exist:
  - Then show each PO with its PO text and the I/E/D level from `course_curriculum_maps` (via `course.programOutcomes()->withPivot('ied')`).

### 4) Weekly Coverage Step

Purpose:
- Generate weekly records and per-week teaching content for LEC/LAB.

Generation rules:
- If no academic calendar is selected:
  - Then block generation.
- If no LEC and no LAB component exists:
  - Then block generation and tell user to complete Course Components first.

- If weeks already exist:
  - Then `ensureWeeksGenerated()` skips creating duplicates.
  - If user clicks Regenerate:
    - Then all existing weeks + week contents are deleted and recreated from the calendar.
    - Then all previously encoded weekly coverage is lost (UI asks for confirmation).

- If weeks do not exist:
  - Then create sequential week records from calendar start/end date in 7-day blocks.
  - Then create default `WeekContent` rows for available components (LEC/LAB).

- If the academic calendar contains an event type `break`:
  - Then any 7-day block that contains a break date is skipped entirely.
  - Then the cursor advances to the next block.
  - Then `week_no` does not increment for the skipped block (week numbers stay sequential with only created weeks).

Locking rules (current implementation):
- If a week range contains an event type `exam`:
  - Then that week is marked locked as `exam`.
  - Then UI shows an “Exam Week” badge and disables editing.
  - Then assessment task is auto-filled for that component:
    - LEC: `1st Term Exam`, `2nd Term Exam`, `Final Term Exam` (in order, capped)
    - LAB: `1st Term Practical Exam`, `2nd Term Practical Exam`, `Final Term Practical Exam` (in order, capped)
- If a week range contains an event type `non_teaching`:
  - Then that week is marked locked as `non_teaching`.
  - Then UI shows a “Non-Teaching Week” badge and disables editing.
  - Then assessment task is auto-filled as `Non-Teaching Week` for all components.
- If a week is locked:
  - Then server-side guards prevent:
    - `saveWeek`
    - `resetWeek`
    - `addReference/removeReference`
    - `addMaterial/removeMaterial`
    - `saveAllWeeklyEntries` write path (skips locked weeks)

Editing rules:
- Uses `weekInputs['w{week_no}']` key format to avoid PHP numeric key coercion issues.
- If user collapses a week and opens a different week:
  - Then Alpine watches `openWeek` and triggers `$wire.saveWeek(oldWeekNo)`.
- Save All persists all unlocked weeks.
- `loadData()` is intentionally not called in save paths to avoid overwriting in-progress user edits.

MVGO rule (Week 1):
- If `week_no === 1`:
  - Then CO selection is replaced by a fixed MVGO badge (Mission-Vision-Goals-Objectives).
  - Then assessment task is optional.
  - If an assessment task is entered for Week 1:
    - Then it will appear in Course Evaluation; otherwise it will not.

Calendar event display:
- If the week is editable and has events:
  - Then all events are displayed with a type chip.
  - Event dot colors: `holiday` → emerald, `break` → blue, others → amber.
- If the week is locked:
  - Then only `exam` / `non_teaching` events are shown in the main lock alert.
  - Then other events (e.g., holiday/break) are shown separately under “Other events this week”.

LEC/LAB switching:
- If both components exist:
  - Then user can switch tab.
  - On switch:
    - Save current component data silently.
    - Change active component.
    - Reload inputs for the new component.
- If LAB is requested but the course has no LAB component:
  - Then the switch is ignored (stays on current component).

### 5) Course Evaluation Step

Purpose:
- Encode the weight (%) for each assessment task generated from Weekly Coverage.

Row generation rules:
- If Weekly Coverage has no assessment tasks:
  - Then show empty state (no table).
- If a week has `assessment_task` empty for both LEC and LAB:
  - Then it does not appear in the evaluation table.
- If `assessment_task` is `Non-Teaching Week` on either side:
  - Then that week is excluded from evaluation rows.
- If `assessment_task` contains the word `exam` (case-insensitive) on either side:
  - Then the row is treated as an exam row (`is_exam = true`) and visually highlighted.
  - Then the term label increments in order (capped): `1st Term`, `2nd Term`, `Final Term`.
- If Week 1 has an assessment task:
  - Then the row is flagged `is_mvgo = true` and the outcome label is forced to `MVGO`.
- If the course has no LAB:
  - Then only LEC columns render and total expected is 100%.
- If the course has LAB:
  - Then both LEC + LAB columns render and totals are expected as 67% (LEC) + 33% (LAB).
- If a component has no task for a row:
  - Then its task cell shows “No LEC task” / “No LAB task”, and the weight input is disabled.

Save rules:
- If Save Evaluation is clicked:
  - Then save all rows via `SyllabusEvaluationItem::updateOrCreate()` per `week_content_id`.
  - If a row is exam:
    - Then its `kind` is `exam`.
    - Then its `exam_type` is mapped from the term label.
  - Else:
    - Then its `kind` defaults to `activity`.

Completion rules (submit gate):
- Assessable week contents are those with:
  - `assessment_task` not empty AND not equal to `Non-Teaching Week`
- If there are zero assessable week contents:
  - Then the step is considered incomplete.
- If any assessable week content has no weight (NULL):
  - Then the step is considered incomplete.

### 6) Review Step

Purpose:
- Show final summary before submit.

Conditions:
- If step changes to `review`:
  - Then force reload all summary data.
- If any step dispatches `syllabus-step-saved` while review is loaded:
  - Then force reload summary so review reflects latest saved state.

Summary includes:
- Selected academic calendar
- Lecture/Lab component details
- Course outcomes list
- Weekly coverage count and exam-type mapping summary
- Course evaluation rows / weights summary (as implemented by Review step)

## Event Contract (Parent ↔ Child)

Used events:
- `syllabus-save-step`
- `syllabus-step-saved`
- `syllabus-step-changed`
- `syllabus-step-dirty`
- `syllabus-calendar-updated`
- `syllabus-course-outcomes-updated`

Intent:
- Parent controls navigation.
- Child owns save logic for its step.
- Dirty state is used for safety checks (especially course outcomes before submit).

## Non-Technical Flow (Plain English)

1. Open syllabus wizard.
2. Pick the academic calendar.
3. Fill lecture/lab details.
4. Add course outcomes and save them.
5. Generate weeks and fill weekly coverage for editable weeks.
6. Encode Course Evaluation weights.
7. Review everything.
8. Submit for review.

If something required is missing:
- The system blocks submit and tells the user what to fix.

If a week has an exam event:
- The week appears but is locked so faculty cannot encode coverage there.
