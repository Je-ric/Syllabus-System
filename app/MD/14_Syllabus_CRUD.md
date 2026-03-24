# Syllabus CRUD (Index, Create Entry, Delete)

Rules for the syllabus listing page, creation entry point, and deletion. For wizard internals see `app/MD/10_Syllabus_Wizard.md`.

## Files Used (Source of Truth)

- Controller
  - `app/Http/Controllers/SyllabusController.php`
- Services
  - `app/Services/Syllabus/SyllabusPreviewService.php` — builds view data for all preview variants
  - `app/Services/Syllabus/SyllabusSnapshotService.php` — generates self-contained HTML snapshots and serves saved-version files
- Models
  - `app/Models/Syllabus.php`
  - `app/Models/CompleteSyllabus.php`
  - `app/Models/Course.php`
- Views
  - `resources/views/Syllabus/index.blade.php`
  - `resources/views/Syllabus/selectCourse.blade.php`
- Routes
  - `routes/web.php` (syllabus routes — `role:admin,faculty`)

## Conditions (If / Then)

### Index (Listing)

- If the user opens the syllabus index:
  - Then only syllabi where `prepared_by = Auth::id()` are shown.
  - Then syllabi are split into two tabs:
    - Draft tab: all syllabi where `status != approved`.
    - Approved tab: all syllabi where `status = approved`.
  - Then each draft card shows a delete button.
  - Then approved cards do not show a delete button.

### Create Entry Point (Course Selection)

- If a faculty user opens the course selection page:
  - Then a program selector is shown (college → department → program).
  - If a program is selected:
    - Then courses are grouped by year level and semester.
    - For each course:
      - If the course has no PO mappings (`course_curriculum_maps` is empty):
        - Then the "Create Syllabus" button is replaced with an amber "No PO mapped" badge.
        - Then the user cannot create a syllabus for that course.
      - If the course has PO mappings:
        - Then the "Create Syllabus" button is shown.
        - Then any faculty user can create a syllabus for any course regardless of department assignment.

### Wizard Entry (SyllabusController::wizard)

- If `syllabusId` is provided:
  - Then load the existing syllabus.
  - If `prepared_by != Auth::id()`:
    - Then stop with `403 Unauthorized`.
- If `courseId` is provided (new syllabus):
  - If the course has no PO mappings:
    - Then redirect back to course selection with an error toast.
    - Then no syllabus row is created.
  - If the course has PO mappings:
    - If a syllabus already exists for this course by this user:
      - Then redirect to the existing syllabus wizard with an info toast.
    - If no existing syllabus:
      - Then create a new `syllabi` row:
        - `status = draft`
        - `current_step = academic_calendar`
        - `prepared_by = Auth::id()`
        - `academic_calendar_id = null`
      - Then redirect to the wizard.
- If neither `syllabusId` nor `courseId` is provided:
  - Then stop with `404`.

### Delete (SyllabusController::destroy)

- If deleting a syllabus:
  - If `prepared_by != Auth::id()`:
    - Then stop with `403 Unauthorized`.
  - If `status != draft`:
    - Then redirect back with error toast: "Only draft syllabi can be deleted."
  - If all checks pass:
    - Then run inside a DB transaction:
      - Then for each `CompleteSyllabus` snapshot:
        - Then delete disk files for `pdf_path`, `abridged_path`, `evaluation_path`.
        - Then delete the `complete_syllabi` DB row.
      - Then delete all `course_components`.
      - Then delete all `course_outcomes`.
      - Then for each `syllabus_week`:
        - Then for each `week_content`:
          - Then delete `syllabus_evaluation_items` for that content.
          - Then delete the `week_content`.
        - Then delete the `syllabus_week`.
      - Then delete all `references`.
      - Then delete all `online_materials`.
      - Then delete all `syllabus_revisions`.
      - Then delete all `syllabus_reviewers`.
      - Then delete the `syllabus`.
    - Then redirect to syllabus index with success toast.

### Access Control (authorizeSyllabusAccess)

- Used by all preview and download routes.
- If the user is not authenticated:
  - Then throw `AuthorizationException`.
- If `prepared_by != Auth::id()` AND user does not have role `admin`:
  - Then throw `AuthorizationException`.
- If user is admin:
  - Then access is granted regardless of `prepared_by`.

## Sequences (Typical Flow)

### Create a New Syllabus

1. Faculty opens course selection page.
2. Faculty selects program → sees courses.
3. Faculty clicks "Create Syllabus" on a course with PO mappings.
4. System checks for existing syllabus by this user for this course.
5. If exists → redirect to existing wizard.
6. If not → create draft row and open wizard.

### Delete a Draft Syllabus

1. Faculty clicks trash button on a draft card.
2. Browser shows native confirm dialog.
3. Faculty confirms.
4. System verifies ownership and draft status.
5. System cascades delete: disk files → snapshots → components → outcomes → weeks → contents → evaluations → references → materials → revisions → reviewers → syllabus.
