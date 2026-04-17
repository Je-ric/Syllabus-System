# Academic Structure (Colleges, Departments, Programs)

Practical reference for how CSMS manages the academic structure hierarchy.

## Files Used (Source of Truth)

- Controller
  - `app/Http/Controllers/AcademicStructureController.php`
- Models
  - `app/Models/College.php`
  - `app/Models/Department.php`
  - `app/Models/Program.php`
  - `app/Models/Course.php`
  - `app/Models/UserAssignment.php`
- Pivot / relationships
  - `program_departments` (program-to-department link with `role=primary`)
- Views
  - `resources/views/AcademicStructure/index.blade.php`
  - `resources/views/AcademicStructure/modals/addCollegeModal.blade.php`
  - `resources/views/AcademicStructure/modals/addDepartmentModal.blade.php`
  - `resources/views/AcademicStructure/modals/addProgramModal.blade.php`
  - `resources/views/AcademicStructure/modals/deleteCollegeModal.blade.php`
  - `resources/views/AcademicStructure/modals/deleteDepartmentModal.blade.php`
  - `resources/views/AcademicStructure/modals/deleteProgramModal.blade.php`
- Routes
  - `routes/web.php` (Academic Structure routes — admin only)

## Key Concepts

- Colleges contain Departments.
- Programs belong to Departments through a pivot (`program_departments`) with a `primary` role.
- All structure routes are restricted to `role:admin`.
- Delete modals show cascade warnings and item counts before confirming.

## Conditions (If / Then)

### Colleges (Create)

- If creating a college:
  - Then `name` is required and must be unique.

### Colleges (Update)

- If updating a college:
  - Then the college must exist (route model binding).
  - Then `name` is required.

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

### Departments (Create)

- If creating a department:
  - Then `name` is required.
  - Then `college_id` is required and must exist.

### Departments (Update)

- If updating a department:
  - Then the department must exist (route model binding).
  - Then `name` is required.
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

### Programs (Create)

- If creating a program:
  - Then `name` is required.
  - Then `department_id` is required and must exist.
  - Then `bor_approval_no` is optional.
  - Then `bor_approval_date` is optional.

### Programs (Update)

- If updating a program:
  - Then the program must exist (route model binding).
  - Then `name` is required.
  - Then `department_id` is required and must exist.
  - If `department_id` is changing AND the program has any courses:
    - Then update is blocked with an error toast.
    - Then department change is only allowed when the program has no courses.
  - If department is not changing or program has no courses:
    - Then update proceeds and `program_departments` pivot is synced to the new department.

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

## Sequences (Typical Flow)

### Create Program

1. User selects a Department and enters Program details.
2. System validates name and department existence.
3. System inserts `programs` row.
4. System inserts `program_departments` row with `role = primary`.

### Update Program Department Link

1. User selects a new Department for the Program.
2. System checks if the program has any courses.
3. If courses exist → blocked.
4. If no courses → system updates `programs` and syncs `program_departments`.

### Delete College (Full Cascade)

1. Admin opens delete modal (shows department count).
2. Admin confirms.
3. System checks for courses under all programs.
4. If courses exist → blocked.
5. If clear → system deletes assignments, objectives, departments, goals, college in a transaction.
