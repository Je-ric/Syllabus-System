# User Assignments (Dean, Chair, Faculty)

Rules for organizational assignments and what appears in hierarchy views.

## Files Used (Source of Truth)

- Controllers
  - `app/Http/Controllers/UserManagement/UserAssignmentsController.php` — User assignments management
- Services
  - `app/Services/UserAssignments/UserAssignmentsService.php` — Assignment business logic
  - `app/Services/UserAssignments/UserAssignmentsChecker.php` — Validation and authorization checks
- Models
  - `app/Models/User.php`
  - `app/Models/UserAssignment.php`
- Cleanup triggered by
  - `app/Services/Authentication/AccountApprovalService.php` (on status change and role removal)
  - `app/Services/University/UniversityStructureService.php` (on academic structure delete)
- Views
  - `resources/views/UserManagement/UserAssignments/colleges.blade.php`
  - `resources/views/UserManagement/UserAssignments/departments.blade.php`
  - `resources/views/UserManagement/UserAssignments/no-assignment.blade.php`
  - `resources/views/UserManagement/UserAssignments/modals/`
- Routes
  - `routes/web.php`
    - `GET /user-assignments/colleges` — `role:admin` (colleges index / dean assignment)
    - `GET /user-assignments/hierarchy` — `role:admin,dean,chair` (role-based entry point)
    - `GET /user-assignments/college/{collegeId}/departments` — `role:admin,dean,chair`
    - `POST /user-assignments/assign-dean` — `role:admin`
    - `POST /user-assignments/remove-dean` — `role:admin`
    - `POST /user-assignments/assign-chair` — `role:admin`
    - `POST /user-assignments/remove-chair` — `role:admin`
    - `POST /user-assignments/assign-faculty` — `role:admin`
    - `POST /user-assignments/remove-faculty` — `role:admin`

## Key Concepts

- Assignment contexts: `dean`, `chair`, `faculty`.
- One dean per college. One chair per department. Multiple faculty per department.
- Admin can assign and remove all roles. Deans and chairs can **view** the hierarchy but **cannot modify** it.
- All assignment and removal operations run inside a DB transaction.
- Admin users are excluded from all potential-assignee lists.

## Conditions (If / Then)

### Hierarchy View Entry Point (`hierarchyView`)

- If user is admin:
  - Then redirect to `user-assignments.colleges.index`.
- If user has a dean assignment:
  - Then redirect to `user-assignments.departments.index` for their assigned college.
- If user has a chair assignment:
  - Then redirect to `user-assignments.departments.index` for the college containing their department.
- If none of the above match:
  - Then show `UserAssignments/no-assignment` view.

### Colleges Index (Dean Assignment Screen)

- Visible to admin, dean, and chair (middleware: `role:admin,dean,chair`).
- Chair and dean can view but cannot assign or remove deans — only admin can.
- Data includes: all colleges with departments, potential dean list, current dean assignments grouped by `college_id`.

### Departments Index (Chair + Faculty Assignment Screen)

- If user is dean (not admin):
  - Then access is restricted to their assigned college — attempting another college's URL aborts with 403.
- If user is chair (not admin, not dean):
  - Then only their own department is shown within the college view.
  - Then attempting to access a college that does not contain their department aborts with 403.
- `canManageChair` and `canManageFaculty` are both `true` only for admin.
  - Dean and chair see the hierarchy but assign/remove controls are not rendered for them.

### Dean Assignment

- If assigning a dean:
  - Then user must have role `dean` (or `admin`).
  - Then user must not currently be assigned as chair anywhere — blocked with error toast.
  - Then user must not already be assigned as dean anywhere — a dean can only hold one college.
  - Then user must not already be dean of this specific college.
- If all checks pass:
  - Then create `user_assignments` row with `context = dean`, `college_id` set, `department_id = null`.
  - Then call `ensureFacultyRoleAndAssignment(college_id, null)` to give the dean a faculty assignment for their college.
  - Then notify user via `RoleAssignmentNotification`.
  - Then audit log recorded.

### Dean Removal

- If removing a dean:
  - Then delete the `user_assignments` row matching `(context = dean, college_id, user_id)`.
  - Then notify user via `RoleAssignmentNotification`.
  - Then audit log recorded.

### Chair Assignment

- If assigning a chair:
  - Then only admin can perform this action — non-admin gets an error toast.
  - Then user must have role `chair` (or `admin`).
  - Then user must not currently be assigned as dean anywhere — blocked with error toast.
  - Then user must not already be assigned as chair anywhere — a chair can only hold one department.
  - Then user must not already be chair of this specific department.
- If all checks pass:
  - Then create `user_assignments` row with `context = chair`, `department_id` set, `college_id = null`.
  - Then call `ensureFacultyRoleAndAssignment(null, department_id)` to give the chair a faculty assignment for their department.
  - Then notify user via `RoleAssignmentNotification`.
  - Then audit log recorded.

### Chair Removal

- If removing a chair:
  - Then only admin can perform this action — non-admin gets an error toast.
  - Then delete the `user_assignments` row matching `(context = chair, department_id, user_id)`.
  - Then notify user via `RoleAssignmentNotification`.
  - Then audit log recorded.

### Faculty Assignment

- If assigning a faculty member:
  - Then only admin can perform this action — non-admin gets an error toast.
  - Then user must have role `faculty` (or `admin`).
  - Then duplicate faculty assignment to the same department returns an info toast (not an error — not blocked).
- If all checks pass:
  - Then create `user_assignments` row with `context = faculty`, `department_id` set, `college_id = null`.
  - Then notify user via `RoleAssignmentNotification`.
  - Then audit log recorded.

### Faculty Removal

- If removing a faculty member:
  - Then only admin can perform this action — non-admin gets an error toast.
  - Then delete the `user_assignments` row matching `(context = faculty, department_id, user_id)`.
  - Then notify user via `RoleAssignmentNotification`.
  - Then audit log recorded.

### Dean + Chair Mutual Exclusivity

- A user cannot be both dean and chair simultaneously.
- If assigning as dean: blocked if user is currently a chair anywhere.
- If assigning as chair: blocked if user is currently a dean anywhere.
- Both checks are enforced by `UserAssignmentsChecker` before any DB write.

### Assignment Cleanup (Account Status Changes)

- If a user is `rejected` or `disabled`:
  - Then all `user_assignments` for that user are deleted (all contexts).
- If a user is `restored` to `pending`:
  - Then no assignment cleanup occurs (user has no active assignments at this point).

### Assignment Cleanup (Role Removal via assignRoles)

- If `dean` role is removed from user:
  - Then all `user_assignments` where `context = dean` are deleted for that user.
- If `chair` role is removed from user:
  - Then all `user_assignments` where `context = chair` are deleted for that user.
- If `faculty` role is removed from user:
  - Then all `user_assignments` where `context = faculty` are deleted for that user.

### Assignment Cleanup (Structure Delete)

- If a college is deleted (`UniversityStructureService`):
  - Then all `user_assignments` where `college_id = college.id` are deleted (dean assignments).
  - Then for each department under the college:
    - Then all `user_assignments` where `department_id = department.id` are deleted (chair + faculty).
- If a department is deleted (`UniversityStructureService`):
  - Then all `user_assignments` where `department_id = department.id` are deleted (chair + faculty).

## Potential Assignee Lists

All lists exclude admin users and only include `active` accounts. Built by `UserAssignmentsChecker::getPotentialUsers()`.

| Context | Roles included | Exclusion |
|---|---|---|
| Dean | `dean` | Users already assigned as dean anywhere |
| Chair | `chair` | Users already assigned as chair anywhere |
| Faculty | `faculty` | None (duplicates give info toast) |

## Sequences (Typical Flow)

### Assign Dean

1. Admin navigates to colleges index.
2. Admin selects a college and picks a user from the potential dean list.
3. System validates role, dean/chair exclusivity, and single-college constraint via `UserAssignmentsChecker`.
4. System creates the `user_assignments` dean row inside a transaction.
5. System calls `ensureFacultyRoleAndAssignment` for the college.
6. System notifies the user and records an audit log.

### Assign Chair / Faculty

1. Admin navigates to the departments index for a college.
2. Admin selects a department and picks a user from the filtered list.
3. System validates actor is admin, role check, and exclusivity (chair only) via `UserAssignmentsChecker`.
4. System creates the `user_assignments` row inside a transaction.
5. For chair: system calls `ensureFacultyRoleAndAssignment` for the department.
6. System notifies the user and records an audit log.

### Disable User → Hierarchy Cleanup

1. Admin disables a user via Account Approval.
2. System sets `account_status = disabled`.
3. `AccountApprovalService` deletes all `user_assignments` for that user.
4. User disappears from all dean/chair/faculty positions in the hierarchy.

### Dean/Chair Views Hierarchy (Read-Only)

1. Dean navigates to hierarchy → redirected to departments index for their college.
2. Chair navigates to hierarchy → redirected to departments index for the college containing their department.
3. Both see current assignments but assign/remove controls are hidden (admin only).
