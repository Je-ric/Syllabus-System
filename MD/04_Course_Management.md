# Course Management

Rules for creating, updating, deleting, and PO mapping of Courses.

## Files Used (Source of Truth)

- Controller
  - `app/Http/Controllers/CourseController.php`
- Service
  - `app/Services/CourseService.php`
  - `app/Services/Syllabus/SyllabusDeleteService.php` — cascade-delete per syllabus (shared with `SyllabusController`)
- Models
  - `app/Models/Course.php`
  - `app/Models/CourseCurriculumMap.php` (PO mapping pivot)
  - `app/Models/ProgramOutcome.php`
  - `app/Models/Syllabus.php`
  - `app/Models/CompleteSyllabus.php`
- Views
  - `resources/views/Course/index.blade.php`
  - `resources/views/Course/form.blade.php`
  - `resources/views/Course/modals/deleteCourseModal.blade.php`
- Routes
  - `routes/web.php` (course routes — `role:admin,chair`)

## Conditions (If / Then)

### Listing

- If `program_id` query param is provided:
  - Then courses are filtered by that `program_id`.
  - Then courses are grouped by year level and semester.
- If `program_id` is missing:
  - Then listing renders with empty grouped courses.

### Create Form

- If `program_id` is provided:
  - Then Program is loaded.
  - Then Program Outcomes are loaded in `po_code` order for mapping UI.

### Create Validation

- If creating a course:
  - Then `confirmed_submission` must be accepted (checkbox confirmation required).
  - Then `program_id` is required and must exist.
  - Then `code` is required and unique against `courses.course_code`.
  - Then `name` is required.
  - Then `description` is an optional string.
  - Then `credits` is required integer, minimum `1`.
  - Then `has_lec_lab` is an optional boolean.
  - Then `year_level` is an optional integer between `1` and `5`.
  - Then `semester` is an optional integer in `1,2`.
  - Then `prerequisite` is an optional string.
  - Then `corequisite` is an optional string.
  - Then `po_mapping` is an optional array.
  - If a `po_mapping` value is present:
    - Then it must be one of `I`, `E`, `D`.

### Create Behavior

- If validation passes:
  - Then store course with normalized fields.
  - Then set `created_by` to the current authenticated user id.
  - Then empty/null `prerequisite` or `corequisite` is normalized to `"None"`.
  - If `po_mapping` exists:
    - Then call `syncPoMappings()` to persist I/E/D levels.

### Edit / Update

- If editing a course:
  - Then course is loaded with `program` and `programOutcomes`.
- If updating a course:
  - Then validation is the same as Create.
  - Except `code` uniqueness ignores the current course id.
  - Except `program_id` is `sometimes` (not required on update).
- If `has_lec_lab` is changing AND the course already has syllabi:
  - Then update is blocked with a `RuntimeException`.
  - Then controller catches it and redirects back with a field-level error on `has_lec_lab`.
  - Then the form shows an amber "Locked" banner and disables the radio inputs.
  - Then a hidden input preserves the current value so the form still submits correctly.
- If update passes:
  - Then course fields are updated.
  - Then PO mappings are rebuilt using `sync` with only valid I/E/D entries.

### Delete

- If deleting a course:
  - Then the acting user must have role `admin` (explicit check via `abort(403)`).
  - Then `CourseService::deleteCourse()` runs inside a DB transaction:
    - Then detach all PO mappings (`course_curriculum_maps`).
    - Then for each syllabus, `SyllabusDeleteService::delete()` handles the full cascade:
      - Disk files for `pdf_path`, `abridged_path`, `evaluation_path` on each `CompleteSyllabus` snapshot.
      - `complete_syllabi`, `course_components`, `course_outcomes`, `syllabus_evaluation_items`, `week_contents`, `syllabus_weeks`, `references`, `online_materials`, `syllabus_revisions`, `syllabus_reviewers`, and the `syllabus` row.
    - Then delete the course.

### Syllabus Creation Gate (from Course Selection)

- If a faculty user tries to create a syllabus for a course:
  - If the course has no PO mappings (`course_curriculum_maps` is empty):
    - Then redirect back to course selection with an error toast.
    - Then syllabus creation is blocked (Course Outcomes step would be empty).
  - If the course has PO mappings:
    - Then proceed to create the syllabus draft.
  - Note: Any faculty user can create a syllabus for any course regardless of department assignment.

## PO Mapping Rules

- Only `I`, `E`, and `D` are persisted.
- Empty/invalid mapping values are ignored.
- Existing mapping not present in submitted data is removed by `sync`.

## Sequences (Typical Flow)

### Create Course

1. User selects Program and fills Course fields.
2. User checks the confirmation checkbox.
3. User optionally sets PO mapping (I/E/D per PO).
4. System validates input (including code uniqueness).
5. System saves `courses` row.
6. If mapping provided, system syncs curriculum map pivot rows.

### Update Course (has_lec_lab locked)

1. User tries to change Has Laboratory setting.
2. System detects existing syllabi for this course.
3. Form shows amber "Locked" banner; radio inputs are disabled.
4. User must delete all syllabi first before changing this setting.

### Delete Course (Admin Only)

1. Admin opens delete modal (shows syllabus count).
2. Admin confirms.
3. System cascades: disk files → snapshots → components → outcomes → weeks → contents → evaluations → references → materials → revisions → reviewers → syllabus → course.
