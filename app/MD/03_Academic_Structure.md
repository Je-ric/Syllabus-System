# Academic Structure Rules

This document summarizes conditions for managing colleges, departments, and programs under Academic Structure.

## Source Controller

- `app/Http/Controllers/AcademicStructureController.php`

## Colleges

### Create Conditions

- College name is required and must be unique.

### Update Conditions

- College must exist.
- Updated name is required.
- Updated name must be unique except current college id.

## Departments

### Create Conditions

- Department name is required.
- `college_id` is required.
- `college_id` must exist in colleges table.

### Update Conditions

- Department must exist.
- Department name is required.
- `college_id` is required and must exist.

## Programs (Structure-level registration)

### Create Conditions

- Program name is required and unique.
- `department_id` is required and must exist.
- BOR approval number is optional.
- BOR approval date is optional and must be a valid date when provided.

### Create Behavior

- Program row is inserted into `programs`.
- Link to department is inserted in `program_departments` with role `primary`.

### Update Conditions

- Program must exist.
- Program name is required and unique except current program id.
- `department_id` is required and must exist.
- BOR fields keep same optional rules.

### Update Behavior

- Program row is updated.
- Program-department relationship is synced to a single primary department:
- `sync([$department_id => ['role' => 'primary']])`

