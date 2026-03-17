# Academic Structure (Colleges, Departments, Programs)

Practical reference for how CSMS manages the academic structure hierarchy.

## Files Used (Source of Truth)

- Controller
  - `app/Http/Controllers/AcademicStructureController.php`
- Models
  - `app/Models/College.php`
  - `app/Models/Department.php`
  - `app/Models/Program.php`
- Pivot / relationships
  - `program_departments` (program-to-department link with `role=primary`)
- Routes
  - `routes/web.php` (Academic Structure routes)

## Key Concepts

- Colleges contain Departments.
- Programs belong to Departments through a pivot (`program_departments`) with a `primary` role.

## Conditions (If / Then)

### Colleges

- If creating a college:
  - Then `name` is required and must be unique.
- If updating a college:
  - Then the college must exist.
  - Then `name` is required.
  - Then `name` must be unique (excluding the current college id).

### Departments

- If creating a department:
  - Then `name` is required.
  - Then `college_id` is required and must exist.
- If updating a department:
  - Then the department must exist.
  - Then `name` is required.
  - Then `college_id` is required and must exist.

### Programs (structure-level registration)

- If creating a program:
  - Then `name` is required and must be unique.
  - Then `department_id` is required and must exist.
  - Then BOR approval number is optional.
  - If BOR approval date is provided:
    - Then it must be a valid date.
- If updating a program:
  - Then the program must exist.
  - Then `name` is required and must be unique (excluding current program id).
  - Then `department_id` is required and must exist.
  - BOR fields keep the same optional rules.

## Sequences (Typical Flow)

### Create Program

1. User selects a Department and enters Program details.
2. System validates name uniqueness and department existence.
3. System inserts `programs` row.
4. System inserts `program_departments` row:
   - `sync([$departmentId => ['role' => 'primary']])`

### Update Program Department Link

1. User selects a new Department for the Program.
2. System validates department existence.
3. System updates `programs`.
4. System syncs `program_departments` back to a single primary department.
