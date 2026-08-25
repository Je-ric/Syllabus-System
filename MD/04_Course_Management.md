# Course Management

Rules for creating, updating, archiving, restoring, deleting, and PO mapping of Courses.

## Files Used (Source of Truth)

- Controllers
  - `app/Http/Controllers/Academic/CourseController.php` — Course CRUD operations
- Services
  - `app/Services/Academic/CourseService.php` — Course business logic
  - `app/Services/Syllabus/SyllabusDeleteService.php` — cascade-delete per syllabus (shared with `SyllabusController`)
- Models
  - `app/Models/Course.php`
  - `app/Models/CourseCurriculumMap.php` (PO mapping pivot)
  - `app/Models/ProgramOutcome.php`
  - `app/Models/Syllabus.php`
  - `app/Models/CompleteSyllabus.php`
- Views
  - `resources/views/Academic/Course/index.blade.php` — Course listing by program
  - `resources/views/Academic/Course/form.blade.php` — Course create/edit form
  - `resources/views/Academic/Course/modals/` — Archive, confirm edit, delete, view, and confirm modals
  - `resources/views/Academic/Course/offcanvasReference.blade.php` — Reference offcanvas
- Routes
  - `routes/web.php` (course routes — `role:admin,chair`)
    - `GET /courses` — index
    - `GET /courses/create` — create form
    - `POST /courses` — store
    - `GET /courses/{course}` — show (redirects to index)
    - `GET /courses/{course}/edit` — edit form
    - `PUT /courses/{course}` — update
    - `POST /courses/{course}/archive` — archive
    - `POST /courses/{course}/restore` — restore
    - `DELETE /courses/{course}` — delete

## Security Implementation

### Input Validation & Injection Prevention
- **Server-Side Validation**: All course forms use `NoInjectionRule` to detect and block script, SQL, and code injection attempts.
- **Regex Patterns**: Text fields validated with specific character patterns:
  - Course code: Alphanumeric with hyphens, periods, and spaces only
  - Course name: Letters, numbers, spaces, and basic punctuation only, no injection attempts
  - Course description: Text content validated for injection attempts
  - Prerequisite/Corequisite: Letters, numbers, spaces, and basic punctuation only, no injection attempts
- **Confirmation Required**: `confirmed_submission` checkbox prevents accidental submissions.

### Authorization
- **Role-Based Access Control**: All course routes protected by `role:admin,chair` middleware.
- **Program Authorization**: Additional checks ensure chairs can only manage courses for programs in their assigned department.
- **Delete Authorization**: Chairs can only delete courses if their assigned department is the primary department of the course's program.

### Transaction Safety
- **Database Transactions**: All course operations (create, update, delete) run inside DB transactions.
- **Cascade Protection**: Course deletion blocked if syllabi exist; comprehensive cascade deletion when authorized.

### Audit Logging
- **Comprehensive Logging**: All course operations record AuditLog entries with user, action, and description.

### Rate Limiting
- **Current Status**: Rate limiting is not currently implemented on course endpoints.
- **Recommended Enhancement**: Add rate limiting to course creation/update endpoints to prevent automated abuse.

## Conditions (If / Then)

### Listing

- If `program_id` query param is provided:
  - Then the program is loaded and authorization is checked (see Program Authorization below).
  - Then courses are filtered by that `program_id`.
  - Then courses are grouped by year level and semester.
  - If `archived=true` query param is also present:
    - Then only archived courses are shown.
  - Otherwise:
    - Then only active courses are shown.
- If `program_id` is missing:
  - Then listing renders with empty grouped courses.

### Program Authorization

All create, edit, update, index, archive, and restore actions check program authorization:

- If user has role `admin`:
  - Then access is always allowed.
- If user has role `chair`:
  - Then access is only allowed if the program belongs to the chair's assigned department.
  - Otherwise, redirect to course index with a warning toast.

### Create Form

- If `program_id` query param is provided:
  - Then program is loaded and authorization is checked.
  - Then Program Outcomes are loaded in `po_code` order for the mapping UI.

### Create Validation

- If creating a course:
  - Then `confirmed_submission` must be accepted (checkbox confirmation required).
  - Then `program_id` is required and must exist.
  - Then `code` is required, max 50 chars, alphanumeric with hyphens/periods/spaces only, unique against `courses.course_code`.
  - Then `name` is required, min 2 chars, max 255 chars, letters/numbers/spaces/basic punctuation only, no injection attempts.
  - Then `description` is optional string, max 5000 chars, no injection attempts.
  - Then `credits` is required integer, minimum `1`, maximum `5`.
  - Then `has_lec_lab` is optional boolean.
  - Then `passing_mark` is optional numeric between `0` and `100`.
  - Then `lec_class_hours` is optional string, max 50 chars.
  - Then `lab_class_hours` is optional string, max 50 chars.
  - Then `year_level` is optional integer between `1` and `5`.
  - Then `semester` is optional integer in `1,2`.
  - Then `prerequisite` is optional string, max 255 chars, letters/numbers/spaces/basic punctuation only, no injection attempts.
  - Then `corequisite` is optional string, max 255 chars, letters/numbers/spaces/basic punctuation only, no injection attempts.
  - Then `po_mapping` is optional array.
  - If a `po_mapping` value is present:
    - Then it must be one of `I`, `E`, `D`.

### Create Behavior

- If validation passes:
  - Then store course with normalized fields.
  - Then set `created_by` to the current authenticated user id.
  - Then empty/null `prerequisite` or `corequisite` is normalized to `"None"`.
  - Then `passing_mark` defaults to `60.00` if not provided.
  - Then `lec_class_hours` defaults to `"3 hr"` if not provided.
  - Then `lab_class_hours` is set to `"3 hr"` if `has_lec_lab = true` and not provided; set to `null` if `has_lec_lab = false`.
  - If `po_mapping` exists:
    - Then call `syncPoMappings()` to persist I/E/D levels.
  - Then redirect to course index for the program with a success toast.

### Show

- The `show` route does not render a detail page.
- If a user navigates to a course's show URL:
  - Then they are redirected to the course index for that program with an info toast.
  - (Course details are presented via modal in the listing view.)

### Edit / Update

- If editing a course:
  - Then course is loaded with `program` and `programOutcomes`.
  - Then program authorization is checked.
- If updating a course:
  - Then validation rules are the same as Create with injection prevention.
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
  - Then redirect to course index for the program with a success toast.

### Archive / Restore

- If archiving a course:
  - Then program authorization is checked.
  - Then `status` is set to `archived`.
  - Then redirect to course index with a success toast.
- If restoring a course:
  - Then program authorization is checked.
  - Then `status` is set to `active`.
  - Then redirect to course index with a success toast.

### Delete

- If deleting a course:
  - If user has role `admin`:
    - Then deletion is allowed.
  - If user has role `chair`:
    - Then check if the chair's assigned department is the primary department of the course's program.
    - If yes → deletion is allowed.
    - If no → redirect to course index with a warning toast.
  - If neither condition is met:
    - Then redirect to course index with a warning toast.
  - If authorized:
    - Then `CourseService::deleteCourse()` runs inside a DB transaction:
      - Then detach all PO mappings (`course_curriculum_maps`).
      - Then for each syllabus, `SyllabusDeleteService::delete()` handles the full cascade:
        - Disk files for `pdf_path`, `abridged_path`, `evaluation_path` on each `CompleteSyllabus` snapshot.
        - `complete_syllabi`, `course_components`, `course_outcomes`, `syllabus_evaluation_items`, `week_contents`, `syllabus_weeks`, `references`, `online_materials`, `syllabus_revisions`, `syllabus_reviewers`, and the `syllabus` row.
      - Then delete the course.
    - Then redirect to course index with a success toast.

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
- Empty or invalid mapping values are ignored.
- Existing mappings not present in submitted data are removed by `sync`.

## Sequences (Typical Flow)

### Create Course

1. User selects Program and fills Course fields.
2. User checks the confirmation checkbox.
3. User optionally sets PO mapping (I/E/D per PO).
4. System validates input (including code uniqueness).
5. System saves `courses` row with defaults for `passing_mark`, `lec_class_hours`, etc.
6. If mapping provided, system syncs curriculum map pivot rows.

### Update Course (has_lec_lab locked)

1. User tries to change the Has Laboratory setting.
2. System detects existing syllabi for this course.
3. Form shows amber "Locked" banner; radio inputs are disabled.
4. User must delete all syllabi for this course first before changing this setting.

### Archive / Restore Course

1. Admin or chair clicks archive/restore on a course.
2. System checks program authorization.
3. Course `status` is toggled between `active` and `archived`.
4. Course listing re-filters based on the active `archived` query param.

### Delete Course (Admin or Chair)

1. Admin or authorized chair opens the delete modal (shows syllabus count and cascade warning).
2. User confirms.
3. System cascades: disk files → snapshots → components → outcomes → weeks → contents → evaluations → references → materials → revisions → reviewers → syllabus → PO mappings → course.
