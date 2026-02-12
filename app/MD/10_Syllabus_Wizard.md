# Syllabus Wizard (Current Flow)

This document reflects the current syllabus wizard behavior.

## Scope

Source of truth:
- `app/Livewire/Syllabus/SyllabusWizard.php`
- `app/Livewire/Syllabus/Concerns/*`
- `resources/views/livewire/syllabus/syllabus-wizard.blade.php`
- `resources/views/livewire/syllabus/steps/*`
- `app/Http/Controllers/SyllabusController.php`

## Wizard Steps

Defined in `Syllabus::getWizardSteps()`:
1. `academic_calendar`
2. `course_components`
3. `course_outcomes`
4. `co_po_mapping`
5. `weekly_coverage`
6. `review`

## Entry Flow

## New syllabus
- Route: `/syllabus/wizard?courseId={id}`
- `SyllabusWizard::mount()` creates draft syllabus with:
  - `status = draft`
  - `current_step = academic_calendar`
  - `academic_calendar_id = null`

## Existing syllabus
- Route: `/syllabus/wizard?syllabusId={id}`
- Wizard loads syllabus, checks preparer ownership, loads existing data.

## Step Save Behavior

All saving is routed through `saveCurrentStep()` and invoked by:
- `Next/Previous` navigation (`navigateToStep(...)`)
- `Save Draft`
- `Submit for Review` (final call before status update)

## Step-specific save logic
- `academic_calendar`: validates and saves `academic_calendar_id` with uniqueness guard per course.
- `course_components`: saves LEC/LAB via `saveComponents()`.
- `course_outcomes`: saves CO records via `saveCourseOutcomes()`.
- `co_po_mapping`: ensures COs are saved first, then syncs mappings.
- `weekly_coverage`: refreshes/generated week data.

## Course Outcomes (current behavior)

Important: CO input is now local-first and not auto-saved on each keystroke.

- Step view uses Alpine manager bound to `@entangle('courseOutcomes')`.
- Add/remove of CO rows is client-local (fast UI updates).
- Actual DB persistence happens when user triggers save flow:
  - Next
  - Save Draft
  - Submit for Review

### CO persistence details (`HandlesCourseOutcomes`)
- Reindexes outcomes and resequences `CO1..CON` before saving.
- Bulk-loads existing outcomes once for lower query count.
- Deletes removed rows in bulk.
- Updates existing rows with non-empty descriptions.
- Creates new rows with non-empty descriptions.
- Remaps CO-PO mapping keys from temp keys to DB IDs after create.

## CO-PO Mapping behavior

- Mapping data stored in `coPoMappings`.
- Saves via `saveCoPoMappings()`.
- Supports numeric CO IDs and temp-key CO rows (resolved to ID when available).
- Sync payload sets default pivot `ied = 'I'` for checked relationships.

## Navigation and UI state

- Wizard uses Alpine + Livewire.
- `currentStep` is entangled (`@entangle('currentStep').live`) for consistent progress indicator.
- Navigation uses single method `navigateToStep(from, to)` to avoid step bounce/race.

## Scroll behavior

- Wizard script uses `preserveScroll(action)` and restores scroll within `main.overflow-y-auto`.
- Applied to:
  - step navigation
  - save draft
  - submit

## Weekly Coverage

- Weeks are generated once per syllabus if missing.
- Generated range is based on selected academic calendar start/end.
- Exam assignment supports: `first_term`, `second_term`, `final_term`.

## Controller Integration

From `SyllabusController`:
- `create()` and `showCourses()` both feed `Syllabus.selectCourse` using shared helper.
- `showForm()` redirects to wizard route.
- `wizard()` creates draft if needed or routes to existing one.

## Current Practical Notes

- CO typing does not hit DB until save action.
- This is intentional for speed and reduced request noise.
- To avoid data loss, users should use Next/Save Draft after CO edits.
