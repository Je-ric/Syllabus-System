# Syllabus CRUD (Index, Create Entry, Delete)

Rules for the syllabus listing page, creation entry point, and deletion. For wizard internals see `MD/10_Syllabus_Wizard.md`.

## Files Used (Source of Truth)

- Controller
  - `app/Http/Controllers/Syllabus/SyllabusController.php` — index, create, wizard, show, edit, update, destroy, previews, downloads
- Services
  - `app/Services/Syllabus/Snapshots/SyllabusPreviewService.php` — builds view data for preview variants
  - `app/Services/Syllabus/Snapshots/SyllabusSnapshotService.php` — generates self-contained HTML snapshots and serves saved-version files
  - `app/Services/Syllabus/SyllabusDeleteService.php` — cascade-deletes a syllabus and all child records/disk files
- Models
  - `app/Models/Syllabus.php`
  - `app/Models/CompleteSyllabus.php`
  - `app/Models/Course.php`
- Views
  - `resources/views/Syllabus/index.blade.php`
  - `resources/views/Syllabus/create/selectCourse.blade.php`
  - `resources/views/Syllabus/preview/complete.blade.php`
  - `resources/views/Syllabus/preview/abridged.blade.php`
  - `resources/views/Syllabus/preview/assessment.blade.php`
  - `resources/views/Syllabus/preview/_versions_drawer.blade.php`
- Routes
  - `routes/web.php` (syllabus routes — `role:admin,faculty,ovpaa`)

## Security Implementation

### Authorization
- **Role-Based Access Control**: Syllabus routes protected by `role:admin,faculty,ovpaa` middleware.
- **Self-Only Access**: Index listing shows only syllabi where `prepared_by = Auth::id()` (users see only their own work).
- **Ownership Validation**: Wizard operations verify `prepared_by == Auth::id()` before allowing edits.
- **Access Control Method**: `authorizeSyllabusAccess()` throws `AuthorizationException` for unauthorized access attempts.

### Input Validation
- **Parameter Validation**: All IDs (courseId, syllabusId) are validated to ensure they exist in the database.
- **PO Mapping Validation**: Course selection blocked if course has no PO mappings (prevents incomplete syllabus creation).
- **Duplicate Prevention**: System checks for existing syllabus by user for course before creating new ones.

### Business Logic Security
- **Academic Record Protection**: Faculty cannot delete syllabi directly (design choice to prevent accidental data loss).
- **Status-Based Access Control**: Syllabus editing restricted to draft and for_revision statuses only.
- **Admin Override**: Admin users have access to all syllabi regardless of `prepared_by` field.

### Transaction Safety
- **Database Transactions**: Syllabus deletion operations run inside DB transactions via `SyllabusDeleteService`.
- **Cascade Deletion**: Complete syllabus deletion includes all related records and disk files atomically.

### File Security
- **Disk File Management**: Syllabus deletion includes secure removal of associated PDF files.
- **Snapshot Management**: Preview snapshots handled securely with proper file access controls.

### Rate Limiting
- **Current Status**: Rate limiting is not currently implemented on syllabus endpoints.
- **Recommended Enhancement**: Add rate limiting to syllabus creation endpoints to prevent automated syllabus generation.

## Conditions (If / Then)

### Index (Listing)

- If the user opens the syllabus index:
  - Then verify user has appropriate role (admin, faculty, or ovpaa).
  - Then only syllabi where `prepared_by = Auth::id()` are shown (admin/faculty/ovpaa only).
  - Then syllabi are grouped into **four tabs**:
    - **Draft** — `status = draft`
    - **Under Review** — `status = under_review`
    - **For Revision** — `status = for_revision`
    - **Approved** — `status = approved`
  - Each tab has its own:
    - Color scheme (amber/blue/rose/emerald)
    - Action button: **Continue** for draft/for_revision, **View** for under_review/approved
    - A **Preview** button (always shown)
    - Empty state with different copy
  - **Draft cards** show a progress bar (current step vs total steps).
  - **Academic year** shown if calendar is set.
  - **Program name** shown for context.
  - No delete button in the UI (prevents accidental academic record deletion).

### Create Entry Point (Course Selection)

- Route: `GET /syllabus/create` → `SyllabusController::create`

- If a faculty user opens the course selection page:
  - Then a program selector is shown (college → department → program).
  - If a program is selected:
    - Then courses are loaded via `Program::getCoursesGroupedByYearAndSemester()`.
    - Then courses are grouped by year level and semester.
    - For each course:
      - If the course has no PO mappings (`course_curriculum_maps` is empty):
        - Then the "Create Syllabus" button is replaced with an amber "No PO mapped" badge.
        - Then the user cannot create a syllabus for that course.
      - If the course has PO mappings:
        - Then the "Create Syllabus" button is shown.
        - Then any faculty user can create a syllabus for any course regardless of department assignment.

- Table columns: Course code + title, Units, Type (LEC/LEC+LAB), Action.

### Wizard Entry (SyllabusController::wizard)

- If `syllabusId` is provided:
  - Then validate that syllabusId exists in database.
  - Then load the existing syllabus.
  - If `prepared_by != Auth::id()`:
    - Then abort with `403 Unauthorized`.
- If `courseId` is provided (new syllabus):
  - Then validate that courseId exists in database.
  - If the course has no PO mappings:
    - Then redirect back to course selection with an error toast.
    - Then no syllabus row is created.
  - If the course has PO mappings:
    - If a syllabus already exists for this course by this user:
      - Then redirect to the existing syllabus wizard with an info toast.
    - If no existing syllabus:
      - Then create a new `syllabi` row:
        - `status = draft`, `current_step = academic_calendar`, `prepared_by = Auth::id()`
        - `academic_calendar_id = null`
      - Then redirect to the wizard.
- If neither `syllabusId` nor `courseId` is provided:
  - Then abort with `404`.

### Edit / Update

- `edit()` redirects to the wizard with `syllabusId` query param.
- `update()` redirects to the wizard with info toast.
- Both check `isEditable()` (status must be `draft` or `for_revision`).
- **Security**: Status-based access control prevents editing of finalized academic records.

### Show (View)

- `show()` redirects to `previewComplete` (live OBTL preview).
- Access controlled via `authorizeSyllabusAccess`.

### Delete (SyllabusController::destroy)

- Syllabus deletion by faculty is **not allowed** — the destroy route returns a warning toast.
- Only admins can delete syllabi (via course deletion cascade or direct DB management via `SyllabusDeleteService`).
- Rationale: syllabi represent academic records; accidental deletion is prevented by design.
- **Security**: Admin-only deletion with transaction-based cascade ensures safe removal of all related records and files.

### Access Control (authorizeSyllabusAccess)

- Used by all preview and download routes.
- If the user is not authenticated: throw `AuthorizationException`.
- If `prepared_by != Auth::id()` AND user does not have role `admin`:
  - Then throw `AuthorizationException`.
- If user is admin: access granted regardless of `prepared_by`.
- **Security**: Ownership-based access control with admin override ensures users can only access their own syllabi.

## Preview & Download Routes

Three preview variants are available for each syllabus:

| Variant | Live Preview | Download | Saved Preview | Saved Download |
|---------|-------------|----------|---------------|----------------|
| Complete | `previewComplete` | `downloadComplete` | `previewSavedComplete` | `downloadSavedComplete` |
| Abridged | `previewAbridged` | `downloadAbridged` | `previewSavedAbridged` | `downloadSavedAbridged` |
| Assessment | `previewAssessment` | `downloadAssessment` | `previewSavedAssessment` | `downloadSavedAssessment` |

- Saved versions read from local disk with Google Drive fallback.
- `SyllabusSnapshotService` handles HTML generation and saved file reading.
- Saved previews inject a **versions drawer** via `injectVersionsDrawer()`.

## Syllabi Statuses

| Status | Meaning | Editable | Actions |
|--------|---------|----------|---------|
| `draft` | In progress | Yes | Continue, Preview |
| `under_review` | Submitted for approval | No | View, Preview |
| `for_revision` | Returned for changes | Yes | Continue, Preview |
| `approved` | Finalized | No | View, Preview |

## Sequences (Typical Flow)

### Create a New Syllabus

1. Faculty opens course selection page (`/syllabus/create`).
2. Faculty selects program → sees courses grouped by year/semester.
3. Faculty clicks "Create Syllabus" on a course with PO mappings.
4. `SyllabusController::showForm` redirects to `syllabus.wizard?courseId=X`.
5. Wizard checks for existing syllabus by this user for this course.
6. If exists → redirect to existing wizard with info toast.
7. If not → create draft row and open wizard.

### Delete a Draft Syllabus

- Faculty **cannot** delete syllabi. The delete button has been removed from the UI.
- The destroy route returns a warning toast if called directly.
- Syllabi are permanent academic records once created.

### View Saved Versions

1. Navigate to syllabus show page.
2. Versions drawer lists all `CompleteSyllabus` records.
3. Click any saved variant (complete/abridged/assessment) to view or download.
