# Syllabus Wizard (Complete Functional Memory Guide)

This file is the practical reference for how the Syllabus Wizard works today.
Use this when changing logic, debugging behavior, or explaining flow to non-technical users.

## Scope and Source of Truth

- Parent wizard:
- `app/Livewire/Syllabus/SyllabusWizard.php`
- `resources/views/livewire/syllabus/syllabus-wizard.blade.php`
- Step components:
- `app/Livewire/Syllabus/Steps/AcademicCalendarStep.php`
- `app/Livewire/Syllabus/Steps/ComponentsStep.php`
- `app/Livewire/Syllabus/Steps/CourseOutcomesStep.php`
- `app/Livewire/Syllabus/Steps/WeeklyCoverageStep.php`
- `app/Livewire/Syllabus/Steps/ReviewStep.php`
- Step views:
- `resources/views/livewire/syllabus/steps/academic-calendar.blade.php`
- `resources/views/livewire/syllabus/steps/course-components.blade.php`
- `resources/views/livewire/syllabus/steps/course-outcomes.blade.php`
- `resources/views/livewire/syllabus/steps/weekly-coverage.blade.php`
- `resources/views/livewire/syllabus/steps/review.blade.php`

Related docs:
- `app/MD/02_Academic_Calendar_and_Events.md`
- `app/MD/04_Course_Management.md`
- `app/MD/13_Livewire_Beginner_Guide.md`

## What It Is

- A guided multi-step process to create or edit one syllabus.
- The syllabus starts as `draft`.
- Users fill required sections and submit to chair review.
- Saving is event-driven between parent wizard and child step components.
- Step components stay mounted in the page and are shown/hidden for faster switching.

## Step Order (Current)

The UI renders these steps:
1. `academic_calendar`
2. `course_components`
3. `course_outcomes`
4. `weekly_coverage`
5. `review`

Note:
- Any legacy mention of `co_po_mapping` is outdated for this wizard screen and should not be treated as an active tab here.

## Start Conditions (If / Then)

- If `syllabusId` is present:
- Then load existing syllabus.
- If logged-in user is not the preparer:
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

When user clicks `Next`, `Previous`, or a tab:
1. Parent dispatches `syllabus-save-step` for the current step.
2. Parent immediately changes `$currentStep` to target step in same request.
3. Parent persists `syllabi.current_step`.
4. Parent dispatches `syllabus-step-changed` to notify newly active step.
5. Child step (the old one) saves itself when it hears `syllabus-save-step`.
6. Child emits `syllabus-step-saved`.
7. Parent receives `syllabus-step-saved`, clears dirty flag, refreshes syllabus model.

Practical meaning:
- Switching step and saving happen in one round trip for better speed.
- Wizard does not wait for a second request just to switch tabs.

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

- If `course_outcomes` step is marked dirty:
- Then block submit and show warning toast to save course outcomes first.

- If all required checks pass:
- Then set syllabus `status = under_review`
- Then set `current_step = review`
- Then redirect to syllabus show page with success toast.

## Step-by-Step Rules

## 1) Academic Calendar Step

Purpose:
- Select one academic calendar for this syllabus.

If / Then:
- If user changes dropdown:
- Then `updatedAcademicCalendarId()` runs.
- If validation passes:
- Then save `academic_calendar_id` to syllabus.
- Then emit `syllabus-step-saved`.
- Then emit `syllabus-calendar-updated` (weekly coverage listens and reloads).

Validation:
- Required.
- Must exist in `academic_calendars`.
- Must be unique for same course in `syllabi.academic_calendar_id` (excluding current syllabus).

## 2) Course Components Step

Purpose:
- Capture lecture and laboratory teaching details.

If / Then:
- If first load and no LEC row exists:
- Then prefill LEC name/email/phone/office from logged-in user profile (if available).

- If course has lab (`has_lec_lab = true`):
- Then LAB section is shown and required.
- If course has no lab:
- Then LAB is not required.

- If any LEC/LAB field changes:
- Then mark step dirty.
- Then do not auto-save immediately.

Save behavior:
- On `syllabus-save-step`, validate completeness and save with `updateOrCreate`.
- Save LEC always.
- Save LAB only when course has lab.

## 3) Course Outcomes Step

Purpose:
- Create and maintain CO list for the syllabus.

If / Then:
- If user types in description:
- Then local Alpine value updates first.
- If textarea loses focus:
- Then Livewire receives one update (`$wire.set(...)`) and marks step dirty.

- If user clicks Add CO while any CO description is blank:
- Then block add and show warning.

- If user removes a CO row:
- Then resequence CO codes (`CO1`, `CO2`, ...), mark dirty.

Save behavior:
- Recompute sequential `co_code` before save.
- Delete DB rows removed in UI.
- Update changed existing rows.
- Insert new non-empty rows.

Critical condition:
- If DB already has CO rows and all submitted descriptions are blank:
- Then block save with error.

Step is marked clean when successful save confirms at least one valid CO still exists.

## 4) Weekly Coverage Step

Purpose:
- Generate weekly records and per-week teaching content for LEC/LAB.

Generation rules:
- If no academic calendar selected:
- Then block generation.

- If no LEC and no LAB component exists:
- Then block generation and tell user to complete Course Components first.

- If weeks already exist:
- Then `ensureWeeksGenerated()` skips creating duplicates.

- If weeks do not exist:
- Then create sequential week records from calendar start/end date in 7-day blocks.
- Then create default `WeekContent` rows for available components (LEC/LAB).

Locking rules (current implementation):
- Weeks are locked dynamically if that week contains event type `exam` or `non_teaching`.
- Locked week is visible but not editable.
- Locked week blocks:
- `saveWeek`
- `add/remove reference`
- `add/remove material`
- `saveWeeklyEntries` write path

Important note:
- Event type `non_teaching` is currently referenced in weekly coverage logic.
- Calendar event validation in events controller currently allows only:
- `holiday`, `exam`, `break`, `other`
- So `non_teaching` lock path may never trigger unless event rules are expanded.

Editing rules:
- Uses `weekInputs['w{week_no}']` key format to avoid PHP numeric key coercion issues.
- Collapsing one accordion week triggers save of previous week.
- Save All persists all unlocked weeks.
- `loadData()` is intentionally not called in save paths to avoid overwriting in-progress user edits.

LEC/LAB switching:
- If both components exist, user can switch tab.
- On switch:
- Save current component data silently.
- Change active component.
- Reload inputs for new component.

## 5) Review Step

Purpose:
- Show final summary before submit.

If / Then:
- If step changes to `review`:
- Then force reload all summary data.

- If any step emits `syllabus-step-saved` while review is loaded:
- Then force reload summary so review reflects latest saved state.

Summary includes:
- Selected academic calendar
- Lecture/Lab component details
- Course outcomes list
- Weekly coverage count and exam-type mapping summary

## Event Contract (Parent <-> Child)

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
6. Review everything.
7. Submit for review.

If something required is missing:
- The system blocks submit and tells the user what to fix.

If a week has an exam event:
- The week appears but is locked so faculty cannot encode coverage there.
