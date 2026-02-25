# Syllabus Wizard (Beginner-Friendly Guide)

This guide explains what the syllabus wizard does now, using simple terms for both technical and non-technical readers.

## Source of Truth

- `app/Livewire/Syllabus/SyllabusWizard.php`
- `app/Livewire/Syllabus/Steps/AcademicCalendarStep.php`
- `app/Livewire/Syllabus/Steps/ComponentsStep.php`
- `app/Livewire/Syllabus/Steps/CourseOutcomesStep.php`
- `app/Livewire/Syllabus/Steps/WeeklyCoverageStep.php`
- `app/Livewire/Syllabus/Steps/ReviewStep.php`

## Big Picture

- The wizard is a multi-step form for building one syllabus.
- A syllabus is saved as a `draft` first.
- Users move step-by-step until they submit for review.
- Saving is event-driven between parent component and step components.

## Step Order

From `Syllabus::getWizardSteps()`:

1. `academic_calendar`
2. `course_components`
3. `course_outcomes`
4. `co_po_mapping`
5. `weekly_coverage`
6. `review`

## Start Flow (Conditional)

- If URL has `syllabusId`:
- Existing syllabus is loaded.
- If current user is not the preparer, access is denied (`403`).
- Current step is restored from DB if valid.
- If URL has `courseId` (and no `syllabusId`):
- New draft syllabus is created with:
- `status = draft`
- `current_step = academic_calendar`
- `academic_calendar_id = null`
- If neither is given:
- Request is rejected (`404`).

## Navigation Rules

- `setStep()` changes current step and persists it.
- `goNextStep()` and `goPreviousStep()` compute step sequence from wizard definitions.
- `navigateToStep(from, to)` guards against invalid transitions.
- Important condition:
- If leaving `course_outcomes` while it has unsaved changes, step change is blocked.

## Save Flow (How Components Talk)

- Parent wizard broadcasts: `syllabus-save-step`.
- Active step listens and saves itself.
- On success, step emits: `syllabus-step-saved`.
- Parent marks that step as clean (`stepDirty[step] = false`).

## Required-Field Checks Before Submit

When user clicks submit:

- If `academic_calendar` missing, submission is blocked.
- If required lecture/lab component fields are incomplete, blocked.
- If no non-empty course outcome exists, blocked.
- If no week records exist, blocked.
- If Course Outcomes step is still dirty, blocked.
- If all checks pass:
- Syllabus is updated to `status = under_review`.
- `current_step = review`.

## Per-Step Behavior (Simple)

## 1) Academic Calendar

- User selects an academic calendar entry.
- Validation checks:
- Must exist.
- Must be unique per course in `syllabi.academic_calendar_id` (excluding current syllabus).
- On valid save, syllabus `academic_calendar_id` is updated.

## 2) Course Components

- LEC fields are required before save.
- LAB fields are required only if course has lab (`has_lec_lab = true`).
- Fields auto-save on change when complete.
- If no LEC row yet, component pre-fills from logged-in user profile (`name`, `email`, `phone`, `office`).

## 3) Course Outcomes

- Outcomes are edited in Livewire state first (fast typing, fewer DB writes).
- Codes are resequenced as `CO1`, `CO2`, `CO3`, ... in current order.
- Blank row rule:
- You cannot add another new row while an existing row is blank.
- Save rule:
- If existing outcomes are all removed/blank, save is blocked (at least one description required).
- Save behavior:
- Deleted rows are removed from DB.
- Existing rows are updated only if changed.
- New non-empty rows are inserted.

## 4) CO-PO Mapping

- Mapping is tied to saved CO rows.
- If COs are newly created, mapping references are resolved during save flow.

## 5) Weekly Coverage

- Weeks are generated only when needed (or when user explicitly generates).
- Week generation uses academic calendar start/end dates.
- Exam assignment supports:
- `first_term`
- `second_term`
- `final_term`
- Re-assigning one exam type clears previous week for that same type.
- Week content can save:
- course outcome link
- learning outcomes
- assessment task
- topic
- TLA
- references
- online materials

## 6) Review

- Aggregates saved data from earlier steps.
- Reloads when step changes to review or when any step confirms save.

## Events Used (Technical Quick List)

- `syllabus-step-dirty`
- `syllabus-step-saved`
- `syllabus-save-step`
- `syllabus-step-changed`
- `syllabus-calendar-updated`
- `syllabus-course-outcomes-updated`

## Non-Technical Mental Model

- Think of it like a guided checklist.
- Green path:
- Fill required fields.
- Save each part.
- Fix warnings if shown.
- Submit only when all sections are complete.
- The system protects against submitting incomplete or unsaved critical sections.
