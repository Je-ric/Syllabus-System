# Course Management

Rules for creating, updating, listing, and PO mapping of Courses.

## Files Used (Source of Truth)

- Controller
  - `app/Http/Controllers/CourseController.php`
- Models
  - `app/Models/Course.php`
  - `app/Models/CourseCurriculumMap.php` (PO mapping pivot)
  - `app/Models/ProgramOutcome.php`
- Views (common)
  - `resources/views/courses/*` (course list + forms)
- Routes
  - `routes/web.php` (course routes)

## Conditions (If / Then)

### Listing

- If `program_id` query param is provided:
  - Then courses are filtered by that `program_id`.
- If `program_id` is missing:
  - Then listing renders with empty grouped courses.

### Create Form

- If `program_id` is provided:
  - Then Program is loaded.
  - Then Program Outcomes are loaded in `po_code` order for mapping UI.

### Create Validation

- If creating a course:
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
  - Then store course with normalized fields (`course_code`, `course_title`, `course_description`, `credit_units`, etc.).
  - Then set `created_by` to the current authenticated user id.
  - If `po_mapping` exists:
    - Then call `syncPoMappings()` to persist I/E/D levels.

### Edit / Update

- If editing a course:
  - Then course is loaded with `program` and `programOutcomes`.
- If updating a course:
  - Then validation is the same as Create.
  - Except `code` uniqueness ignores the current course id.
- If update passes:
  - Then course fields are updated.
  - Then PO mappings are rebuilt using `sync` with only valid I/E/D entries.

### Delete (Current Status)

- Route `DELETE /courses/{course}` exists in `routes/web.php`.
- `CourseController::destroy()` is currently commented out.

Practical effect:
- Delete action is not implemented in controller logic.
- Treat delete UI as inactive until the controller method is restored.

## PO Mapping Rules

- Only `I`, `E`, and `D` are persisted.
- Empty/invalid mapping values are ignored.
- Existing mapping not present in submitted data is removed by `sync`.

## Sequences (Typical Flow)

### Create Course

1. User selects Program and fills Course fields.
2. User optionally sets PO mapping (I/E/D per PO).
3. System validates input (including code uniqueness).
4. System saves `courses` row.
5. If mapping provided, system syncs curriculum map pivot rows.

### Update Course

1. User edits Course fields and mapping.
2. System validates (code uniqueness ignores current row).
3. System updates course.
4. System syncs mapping pivot rows based on submitted I/E/D entries.
