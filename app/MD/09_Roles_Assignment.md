# Roles and Assignments

How user roles and organizational assignments work together in CSMS.

## Files Used (Source of Truth)

- Controllers
  - `app/Http/Controllers/AccountApprovalController.php`
  - `app/Http/Controllers/OrganizationalHierarchyController.php`
- Service
  - `app/Services/AccountApprovalService.php`
- Models
  - `app/Models/User.php`
  - `app/Models/UserRole.php`
  - `app/Models/UserAssignment.php`
- Routes
  - `routes/web.php` (approval, roles, and hierarchy routes)

## Core Concept

The system uses two layers:

1. Roles (`user_roles`) control permissions.
2. Assignments (`user_assignments`) control organizational scope.

Both layers are required for consistent authorization.

## Conditions (If / Then)

### Roles (AccountApprovalService::assignRoles)

- If assigning roles:
  - Then `user_id` must exist.
  - Then `roles` must be an array.
  - Then allowed values are: `admin`, `chair`, `dean`, `faculty`.
  - Then user account must be `active` before roles can be assigned.
  - If the new role set contains both `dean` and `chair`:
    - Then assignment is blocked with a 422 error.
    - Then a user cannot hold both Dean and Chair roles simultaneously.

- If roles are synced:
  - Then `faculty` is always forced into the role set.
  - Then roles are synced via `roles()->sync(...)` (unselected roles are removed).

- If `dean` role is removed:
  - Then delete all `user_assignments` where `context = dean` for that user.
- If `chair` role is removed:
  - Then delete all `user_assignments` where `context = chair` for that user.
- If `faculty` role is removed (future scenario):
  - Then delete all `user_assignments` where `context = faculty` for that user.

### Account Status Actions (AccountApprovalService)

- If `approve` is performed:
  - Then set `account_status = active`.
  - Then ensure faculty role exists and is attached.
  - Then send status email.
- If `reject` is performed:
  - Then set `account_status = rejected`.
  - Then delete all `user_assignments` for that user (all contexts).
  - Then send status email.
- If `restore` is performed:
  - Then set `account_status = pending`.
  - Then no assignment cleanup.
- If `disable` is performed:
  - Then set `account_status = disabled`.
  - Then delete all `user_assignments` for that user (all contexts).
  - Then send status email.

### Assignments (OrganizationalHierarchyController)

- If assigning dean:
  - Then `college_id` and `user_id` must exist.
  - Then user must have role `dean` or `admin`.
  - Then user must not be assigned as chair (mutual exclusivity).
  - Then user must not already be dean of any college.
  - If assignment succeeds:
    - Then create `user_assignments` with `context = dean`, `college_id`, `department_id = null`.
    - Then call `ensureFacultyRoleAndAssignment($collegeId, null)`.

- If assigning chair:
  - Then `department_id` and `user_id` must exist.
  - Then user must have role `chair` or `admin`.
  - Then user must not be assigned as dean.
  - Then user must not already be chair of any department.
  - If assignment succeeds:
    - Then create `user_assignments` with `context = chair`, `department_id`, `college_id = null`.
    - Then call `ensureFacultyRoleAndAssignment(null, $departmentId)`.

- If assigning faculty:
  - Then `department_id` and `user_id` must exist.
  - Then user must have role `faculty` or `admin`.
  - Then duplicate faculty assignment to the same department is blocked.
  - If assignment succeeds:
    - Then create `user_assignments` with `context = faculty`, `department_id`, `college_id = null`.

## Important Constraints (As Implemented)

- A user cannot be both dean and chair at the same time (blocked at both assignment and role-sync level).
- A user can only have one dean assignment (any college).
- A user can only have one chair assignment (any department).
- Faculty assignments can coexist with dean/chair context.
- Disabling or rejecting a user removes all their assignments immediately.

## User Model Helper Behavior

From `User.php`:

- `getPrimaryDepartmentAssignment()` prioritizes chair, then faculty.
- `getPrimaryCollegeAssignment()` returns dean assignment.
- `isAssignedAsDean()` and `isAssignedAsChair()` are global checks.
- `ensureFacultyRoleAndAssignment()`:
  - Ensures faculty role exists and is attached.
  - Creates faculty assignment if missing for given college/department pair.

## Sequences (Typical Flow)

### Approve Account → Assign Roles → Assign Scope

1. Admin approves the user (account becomes `active`, faculty role attached).
2. Admin assigns additional roles (faculty always included; dean+chair combination blocked).
3. Admin assigns organizational scope (dean/chair/faculty assignments), subject to exclusivity rules.

### Disable Account → Cleanup

1. Admin disables the user.
2. System sets `account_status = disabled`.
3. System deletes all `user_assignments` for that user.
4. User is removed from all hierarchy positions.
