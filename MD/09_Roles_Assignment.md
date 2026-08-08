# Roles and Assignments

How user roles and organizational assignments work together in CSMS.

## Files Used (Source of Truth)

- Controllers
  - `app/Http/Controllers/Authentication/AccountApprovalController.php` — approval actions, role assignment, user editing
  - `app/Http/Controllers/UserManagement/UserAssignmentsController.php` — dean/chair/faculty assignment
- Services
  - `app/Services/AccountApprovalService.php` — approve, reject, restore, disable, assignRoles
  - `app/Services/UserAssignments/UserAssignmentsService.php` — assign/remove dean, chair, faculty
  - `app/Services/UserAssignments/UserAssignmentsChecker.php` — validation and authorization checks
- Models
  - `app/Models/User.php`
  - `app/Models/UserRole.php`
  - `app/Models/UserAssignment.php`
  - `app/Models/Role.php`
- Routes
  - `routes/web.php` (approval, roles, and hierarchy routes)

## Core Concept

The system uses two layers:

1. **Roles** (`user_roles`) control permissions — values: `admin`, `dean`, `chair`, `faculty`.
2. **Assignments** (`user_assignments`) control organizational scope — context: `dean`, `chair`, `faculty`.

Both layers are required for consistent authorization.

### Important Rules

- A user cannot be both **dean** and **chair** at the same time (blocked at both role-sync and assignment level).
- A user can only have **one dean** assignment (one college).
- A user can only have **one chair** assignment (one department).
- Faculty assignments can coexist with dean/chair.
- Disabling or rejecting a user removes all their assignments immediately.
- Account must be `active` before roles can be assigned.

## Conditions (If / Then)

### Approve Account (AccountApprovalService::approve)

- If approving a user:
  - Then set `account_status = active`.
  - Then ensure `faculty` role exists and is attached (if not already).
  - Then send status email via `AccountStatusUpdated` mail.
  - Then record AuditLog.

### Reject Account (AccountApprovalService::reject)

- If rejecting a user:
  - Then set `account_status = rejected`.
  - Then delete all `user_assignments` for that user (all contexts).
  - Then send status email.
  - Then record AuditLog.

### Restore Account (AccountApprovalService::restore)

- If restoring a user:
  - Then set `account_status = pending`.
  - Then no assignment cleanup — user keeps previous assignments.
  - Then record AuditLog.

### Disable Account (AccountApprovalService::disable)

- If disabling a user:
  - Then set `account_status = disabled`.
  - Then delete all `user_assignments` for that user (all contexts).
  - Then send status email.
  - Then record AuditLog.

### Assign Roles (AccountApprovalController::assignRole → AccountApprovalService::assignRoles)

- If assigning roles:
  - Then `user_id` must exist.
  - Then `roles` must be an array.
  - Then allowed values are: `admin`, `chair`, `dean`, `faculty`.
  - If the new role set contains both `dean` and `chair`:
    - Then controller returns error toast (client-side check).
    - Then service also checks and aborts with 422 (server-side safety net).
  - If user account is not `active`:
    - Then abort 403 "Roles can only be assigned to active accounts."

- If roles are synced:
  - Then `faculty` is always forced into the role set (pushed then uniqued).
  - Then roles are synced via `roles()->sync($roleIds)` — unselected roles are removed.

- If `dean` role is removed (was previously in old roles, missing in new):
  - Then delete all `user_assignments` where `context = dean` for that user.

- If `chair` role is removed:
  - Then delete all `user_assignments` where `context = chair` for that user.

- If `faculty` role is removed (edge case — faculty is always re-added):
  - Then delete all `user_assignments` where `context = faculty` for that user.

### Edit User (AccountApprovalController::editUser — Authentication)

- If editing a user as admin:
  - Then validate: `name` required, `email` required + unique (excluding self), `phone_number` optional, `office` optional.
  - Then update user record.
  - Then record AuditLog.
  - Route is admin-only.

### Assignments via UserAssignmentsService

All assignment methods run inside a DB transaction and record AuditLog.

#### Dean Assignment

- If assigning dean:
  - Then `college_id` and `user_id` must exist.
  - If actor is not admin:
    - Then checks are delegated to `UserAssignmentsChecker`.
  - Then checks run in order:
    1. User must have role `dean` or `admin`.
    2. User must not be assigned as chair (mutual exclusivity).
    3. User must not already be dean of any college.
    4. User must not already be dean of this specific college.
  - If checks pass:
    - Then create `user_assignments` with `context = dean`, `college_id`, `department_id = null`.
    - Then call `ensureFacultyRoleAndAssignment($college->id, null)` — also attaches faculty role.
    - Then record AuditLog.

#### Chair Assignment

- If assigning chair:
  - Then `department_id` and `user_id` must exist.
  - If actor is not admin: blocked (admin-only).
  - Then checks run in order:
    1. User must have role `chair` or `admin`.
    2. User must not be assigned as dean.
    3. User must not already be chair of any department.
    4. User must not already be chair of this specific department.
  - If checks pass:
    - Then create `user_assignments` with `context = chair`, `department_id`, `college_id = null`.
    - Then call `ensureFacultyRoleAndAssignment(null, $department->id)` — also attaches faculty role.
    - Then record AuditLog.

#### Faculty Assignment

- If assigning faculty:
  - Then `department_id` and `user_id` must exist.
  - If actor is not admin: blocked (admin-only).
  - Then checks run in order:
    1. User must have role `faculty` or `admin`.
    2. Duplicate faculty assignment to the same department is blocked (returns info toast — not an error).
  - If checks pass:
    - Then create `user_assignments` with `context = faculty`, `department_id`, `college_id = null`.
    - Then record AuditLog (no automatic faculty role attachment — role must already exist).

#### Removal methods (removeDean, removeChair, removeFaculty)

- Remove dean: admin-only, deletes the `user_assignments` row and audits.
- Remove chair: admin-only (checked via `checkActorCanManageChair`), deletes and audits.
- Remove faculty: admin-only (checked via `checkActorCanManageFaculty`), deletes and audits.

### Hierarchy View Routing (UserAssignmentsController::hierarchyView)

- If user is `admin`:
  - Then redirect to `user-assignments.colleges.index` (full college list).
- If user has `dean` assignment:
  - Then redirect to `user-assignments.departments.index` with their college ID.
- If user has `chair` assignment:
  - Then redirect to `user-assignments.departments.index` with their department's college ID.
- If none of the above:
  - Then show `UserAssignments.no-assignment` view.

### Departments Index Scoping (UserAssignmentsChecker)

- If viewing `departmentsIndex`:
  - **Admin**: sees all departments, can manage chair and faculty.
  - **Dean**: scoped to their college; can view but not manage chair/faculty.
  - **Chair**: scoped to their own department only; can view but not manage chair/faculty.

## User Model Helper Behavior (User.php)

- `hasRole(string $role): bool` — checks if user has a specific role by name.
- `getPrimaryDepartmentAssignment()` — returns `chair` assignment first, then `faculty`.
- `getPrimaryCollegeAssignment()` — returns `dean` assignment.
- `isDean(): bool` — whether user has any `dean` assignment.
- `isAssignedAsDean(): bool` — same as `isDean()`.
- `isAssignedAsChair(): bool` — whether user has any `chair` assignment.
- `ensureFacultyRoleAndAssignment($collegeId, $departmentId)`:
  - Ensures `faculty` role exists and is attached.
  - Creates faculty assignment via `firstOrCreate` if missing for given college/department pair.

## UserAssignment Model

- `context` column: `'dean' | 'chair' | 'faculty'`
- Scopes: `dean()`, `chair()`, `faculty()`, `forCollege()`, `forDepartment()`, `forUser()`
- Static helpers: `findAssignment()`, `removeAssignment()`

## getPotentialUsers (UserAssignmentsChecker)

Used to populate dropdowns when assigning dean/chair/faculty:

- Filters by role(s).
- Excludes `admin` role users.
- Requires `account_status = active`.
- Optionally excludes users already assigned in a given context.

## Sequences (Typical Flow)

### Approve Account → Assign Roles → Assign Scope

1. Admin approves the user (account becomes `active`, faculty role attached, email sent).
2. Admin assigns additional roles via `assignRole` (faculty always forced; dean+chair blocked).
3. Admin navigates to organizational hierarchy → assigns scope (dean/chair/faculty) subject to exclusivity rules.

### Disable Account → Cleanup

1. Admin disables the user.
2. System sets `account_status = disabled`.
3. System deletes all `user_assignments`.
4. Status email sent to user.
