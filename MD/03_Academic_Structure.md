# Academic Structure (Colleges, Departments, Programs)

Practical reference for how CSMS manages the academic structure hierarchy.

## Files Used (Source of Truth)

- Controllers
  - `app/Http/Controllers/University/UniversityStructureController.php` — Academic structure CRUD
- Services
  - `app/Services/University/UniversityStructureService.php` — Academic structure business logic
- Models
  - `app/Models/College.php`
  - `app/Models/Department.php`
  - `app/Models/Program.php`
  - `app/Models/Course.php`
  - `app/Models/UserAssignment.php`
- Pivot
  - `program_departments` (program-to-department link with `role = primary` or `role = supporting`)
- Views
  - `resources/views/University/UniversityStructure/index.blade.php`
  - `resources/views/University/UniversityStructure/modals/`
- Routes
  - `routes/web.php` (academic structure routes — `role:admin` only)
    - `GET /university-structure` — index
    - `POST /university-structure/colleges` — store college
    - `PUT /university-structure/colleges/{college}` — update college
    - `DELETE /university-structure/colleges/{college}` — delete college
    - `POST /university-structure/departments` — store department
    - `PUT /university-structure/departments/{department}` — update department
    - `DELETE /university-structure/departments/{department}` — delete department
    - `POST /university-structure/programs` — store program
    - `PUT /university-structure/programs/{program}` — update program
    - `DELETE /university-structure/programs/{program}` — delete program

## Key Concepts

- Colleges contain Departments. Programs belong to Departments via the `program_departments` pivot with `role = primary` (one) and optional `role = supporting` (many).
- All structure routes are restricted to `role:admin`.
- Delete modals show cascade warnings and item counts before confirming.
- All destructive operations run inside a DB transaction via `UniversityStructureService`.
- If a service call throws, the controller catches it and returns a generic error toast.
- **Security**: All text inputs are validated using `NoInjectionRule` to prevent script, SQL, and code injection attempts.
- **Validation**: Names use regex patterns to ensure only allowed characters (letters, spaces, basic punctuation).
- **BOR Approval**: Programs include BOR approval number and date validation with cross-field requirements.

## Security Implementation

### Input Validation & Injection Prevention
- **Server-Side Validation**: All academic structure forms use `NoInjectionRule` to detect and block script, SQL, and code injection attempts.
- **Regex Patterns**: Text fields validated with specific character patterns:
  - College/Department names: Letters, spaces, and basic punctuation only
  - Program names: Letters, numbers, spaces, and basic punctuation only
  - BOR approval numbers: Letters, numbers, hyphens, slashes, and periods only
- **Character Restrictions**: All name fields prevent special characters that could be used for injection attacks.

### Authorization
- **Role-Based Access Control**: All academic structure routes protected by `role:admin` middleware.
- **Transaction Safety**: All destructive operations run inside DB transactions to prevent partial updates.

### Data Integrity
- **Uniqueness Validation**: Names must be unique within their respective tables.
- **Foreign Key Validation**: Department and college references validated against existing records.
- **Cross-Field Validation**: BOR approval number requires corresponding date; dates cannot be in the future.

### Cascade Protection
- **Course Blocking**: Structure deletion blocked if courses exist under affected programs/departments.
- **Assignment Cleanup**: User assignments automatically removed when departments/colleges are deleted.
- **Objective Cleanup**: Department objectives and college goals removed during cascade deletions.

### Rate Limiting
- **Current Status**: Rate limiting is not currently implemented on academic structure endpoints.
- **Recommended Enhancement**: Add rate limiting to structure creation endpoints to prevent automated abuse.

## Conditions (If / Then)

### Colleges (Create)

- If creating a college:
  - Then `name` is required, min 2 chars, max 255 chars, letters/spaces/basic punctuation only, no injection attempts.
  - Then `name` must be unique across `colleges`.

### Colleges (Update)

- If updating a college:
  - Then the college must exist (route model binding).
  - Then `name` is required, min 2 chars, max 255 chars, letters/spaces/basic punctuation only, no injection attempts.
  - Then `name` must be unique, ignoring the current college's own id.

### Colleges (Delete)

- If deleting a college:
  - If any course exists under any program under any department of this college:
    - Then delete is blocked with an error toast showing the course count.
  - If no courses exist:
    - Then for each department under the college:
      - Then delete all `user_assignments` where `department_id = department.id`.
      - Then detach all programs from the department (`program_departments`).
      - Then delete all department objectives.
      - Then delete the department.
    - Then delete all `user_assignments` where `college_id = college.id` (dean assignments).
    - Then delete all college goals.
    - Then delete the college.
    - Then all operations run inside a DB transaction.
  - If a database error occurs:
    - Then transaction is rolled back and an error toast is shown.

### Departments (Create)

- If creating a department:
  - Then `name` is required, min 2 chars, max 255 chars, letters/spaces/basic punctuation only, no injection attempts.
  - Then `name` must be unique across `departments`.
  - Then `college_id` is required and must exist.

### Departments (Update)

- If updating a department:
  - Then the department must exist (route model binding).
  - Then `name` is required, min 2 chars, max 255 chars, letters/spaces/basic punctuation only, no injection attempts.
  - Then `name` must be unique, ignoring the current department's own id.
  - Then `college_id` is required and must exist.

### Departments (Delete)

- If deleting a department:
  - If any course exists under any program of this department:
    - Then delete is blocked with an error toast showing the course count.
  - If no courses exist:
    - Then delete all `user_assignments` where `department_id = department.id` (chair + faculty assignments).
    - Then detach all programs from the department (`program_departments`).
    - Then delete all department objectives.
    - Then delete the department.
    - Then all operations run inside a DB transaction.
  - If a database error occurs:
    - Then transaction is rolled back and an error toast is shown.

### Programs (Create)

- If creating a program:
  - Then `name` is required, min 2 chars, max 255 chars, letters/numbers/spaces/basic punctuation only, no injection attempts.
  - Then `name` must be unique across `programs`.
  - Then `primary_department_id` is required and must exist.
  - Then `supporting_department_ids` is optional array; each entry must exist in `departments`.
  - Then `bor_approval_no` is optional string, max 255 chars, specific format only, no injection attempts.
  - Then `bor_approval_date` is optional date.
  - If `bor_approval_no` is provided, then `bor_approval_date` is required.
  - If `bor_approval_date` is provided, then it cannot be in the future.
- If create succeeds:
  - Then `programs` row is inserted.
  - Then `program_departments` pivot row is inserted with `role = primary` for the primary department.
  - Then additional `program_departments` rows are inserted with `role = supporting` for each supporting department (skipping any that duplicate the primary).
  - Then all in a DB transaction.

### Programs (Update)

- If updating a program:
  - Then the program must exist (route model binding).
  - Then `name` is required, min 2 chars, max 255 chars, letters/numbers/spaces/basic punctuation only, no injection attempts.
  - Then `name` must be unique, ignoring the current program's own id.
  - Then `primary_department_id` is required and must exist.
  - Then `supporting_department_ids` is optional array.
  - Then `bor_approval_no` is optional string, max 255 chars, specific format only, no injection attempts.
  - Then `bor_approval_date` is optional date.
  - If `bor_approval_no` is provided, then `bor_approval_date` is required.
  - If `bor_approval_date` is being changed, then it cannot be in the future.
  - If `primary_department_id` is changing AND the program has any courses:
    - Then update is blocked with an error toast.
    - Then primary department change is only allowed when the program has no courses.
  - If department is not changing or program has no courses:
    - Then program fields are updated.
    - Then `program_departments` pivot is synced to the new primary + supporting departments.
    - Then all in a DB transaction.
  - If a database error occurs:
    - Then transaction is rolled back and generic error message is returned.

### Programs (Delete)

- If deleting a program:
  - If any course is assigned to this program:
    - Then delete is blocked with an error toast showing the course count.
  - If no courses exist:
    - Then for each PO under the program:
      - Then detach all PEO mappings (`program_outcome_peo`).
      - Then delete the PO.
    - Then delete all PEOs for the program.
    - Then detach the program from all departments (`program_departments`).
    - Then delete the program.
    - Then all operations run inside a DB transaction.
  - If a database error occurs:
    - Then transaction is rolled back and an error toast is shown.

## Sequences (Typical Flow)

### Create Program

1. Admin enters program name, selects primary department, optionally selects supporting departments.
2. System validates name uniqueness and department existence.
3. System inserts `programs` row.
4. System inserts `program_departments` row with `role = primary` and any `role = supporting` rows.

### Update Program Department Link

1. Admin selects a new primary department for the Program.
2. System checks if the program has any courses.
3. If courses exist → blocked with an error toast.
4. If no courses → system updates `programs` and syncs `program_departments`.

### Delete College (Full Cascade)

1. Admin opens delete modal (shows department count and cascade warning).
2. Admin confirms.
3. System checks for courses under all programs in all departments.
4. If courses exist → blocked with an error toast showing the count.
5. If clear → system deletes assignments, objectives, program detachments, departments, goals, college in a transaction.
