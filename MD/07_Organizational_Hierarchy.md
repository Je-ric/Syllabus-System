# Organizational Hierarchy (Dean, Chair, Faculty)

Rules for organizational assignments and what appears in hierarchy views.

## Files Used (Source of Truth)

- Controller
  - `app/Http/Controllers/OrganizationalHierarchyController.php`
- Services
  - `app/Services/OrganizationalHierarchy/OrganizationalHierarchyService.php`
  - `app/Services/OrganizationalHierarchy/OrganizationalHierarchyChecker.php`
- Models
  - `app/Models/User.php`
  - `app/Models/UserAssignment.php`
- Cleanup triggered by
  - `app/Services/AccountApprovalService.php` (on status change)
  - `app/Services/AcademicStructureService.php` (on structure delete)
- Routes
  - `routes/web.php` (organizational hierarchy routes)

## Key Concepts

- Assignment contexts: `dean`, `chair`, `faculty`.
- One dean per college. One chair per department. Multiple faculty per department.
- Admin can assign and remove all roles. Deans and chairs can **view** the hierarchy but **cannot modify** it.
- All assignment and removal operations run inside a DB transaction.
- Admin users are excluded from all potential-assignee lists.

## Conditions (If / Then)

### Hierarchy View Entry Point

- If user is admin:
  - Then redirect to colleges index.
- If user has a dean assignment:
  - Then redirect to departments index for their assigned college.
- If user has a chair assignment:
  - Then redirect to departments index for the college containing their department.
- If none of the above match:
  - Then show the no-assignment view.

### Colleges Index (Dean Assignment Screen)

- Visible to admin, dean, and chair (middleware: `role:admin,dean,chair`).
- Chair and dean can view but cannot assign or remove deans — only admin can.

### Departments Index (Chair + Faculty Assignment Screen)

- If user is dean (not admin):
  - Then access is restricted to their assigned college — attempting another college's URL aborts with 403.
- If user is chair (not admin, not dean):
  - Then only their own department is shown within the college view.
  - Then attempting to access a college that does not contain their department aborts with 403.
- `canManageChair` and `canManageFaculty` are both `true` only for admin.
  - Dean and chair see the hierarchy but the assign/remove controls are not rendered for them.

### Dean Assignment

- If assigning a dean:
  - Then user must have role `dean` (or `admin`).
  - Then user must not currently be assigned as chair anywhere — blocked with error toast.
  - Then user must not already be assigned as dean anywhere — a dean can only hold one college.
  - Then user must not already be dean of this specific college.
- If all checks pass:
  - Then create `user_assignments` row with `context = dean`, `college_id` set, `department_id = null`.
  - Then call `ensureFacultyRoleAndAssignment(college_id, null)` to give the dean a faculty assignment for their college.

### Dean Removal

- If removing a dean:
  - Then delete the `user_assignments` row matching `(context = dean, college_id, user_id)`.

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

### Chair Removal

- If removing a chair:
  - Then only admin can perform this action — non-admin gets an error toast.
  - Then delete the `user_assignments` row matching `(context = chair, department_id, user_id)`.

### Faculty Assignment

- If assigning a faculty member:
  - Then only admin can perform this action — non-admin gets an error toast.
  - Then user must have role `faculty` (or `admin`).
  - Then duplicate faculty assignment to the same department returns an info toast (not an error).
- If all checks pass:
  - Then create `user_assignments` row with `context = faculty`, `department_id` set, `college_id = null`.

### Faculty Removal

- If removing a faculty member:
  - Then only admin can perform this action — non-admin gets an error toast.
  - Then delete the `user_assignments` row matching `(context = faculty, department_id, user_id)`.

### Dean + Chair Mutual Exclusivity

- A user cannot be both dean and chair simultaneously.
- If assigning as dean: blocked if user is currently a chair anywhere.
- If assigning as chair: blocked if user is currently a dean anywhere.
- Both checks are enforced by `OrganizationalHierarchyChecker` before any DB write.

### Assignment Cleanup (Account Status)

- If a user is `rejected` or `disabled`:
  - Then all `user_assignments` for that user are deleted (all contexts).
- If a user is `restored` to `pending`:
  - Then no assignment cleanup occurs (user has no active assignments at this point).

### Assignment Cleanup (Role Removal via assignRoles)

- If `dean` role is removed:
  - Then all `user_assignments` where `context = dean` are deleted for that user.
- If `chair` role is removed:
  - Then all `user_assignments` where `context = chair` are deleted for that user.
- If `faculty` role is removed:
  - Then all `user_assignments` where `context = faculty` are deleted for that user.

### Assignment Cleanup (Structure Delete)

- If a college is deleted:
  - Then all `user_assignments` where `college_id = college.id` are deleted (dean assignments).
  - Then for each department under the college:
    - Then all `user_assignments` where `department_id = department.id` are deleted (chair + faculty).
- If a department is deleted:
  - Then all `user_assignments` where `department_id = department.id` are deleted (chair + faculty).

## Potential Assignee Lists

All lists exclude admin users and only include `active` accounts.

- Potential dean list:
  - Users with role `dean` (not admin)
  - Active accounts
  - Excluding users already assigned as dean anywhere
- Potential chair list:
  - Users with role `chair` (not admin)
  - Active accounts
  - Excluding users already assigned as chair anywhere
- Potential faculty list:
  - Users with role `faculty` (not admin)
  - Active accounts
  - No exclusion for existing assignments (duplicate gives info toast, not blocked)

## Sequences (Typical Flow)

### Assign Dean

1. Admin selects a college and picks a user from the potential dean list.
2. System validates role, dean/chair exclusivity, and single-college constraint.
3. System creates the `user_assignments` dean row.
4. System calls `ensureFacultyRoleAndAssignment` for the college.

### Assign Chair / Faculty

1. Admin selects a department and picks a user from the filtered list.
2. System validates actor is admin, role check, and exclusivity (chair only).
3. System creates the `user_assignments` row for the context.
4. For chair: system calls `ensureFacultyRoleAndAssignment` for the department.

### Disable User → Hierarchy Cleanup

1. Admin disables a user.
2. System sets `account_status = disabled`.
3. System deletes all `user_assignments` for that user.
4. User disappears from all dean/chair/faculty positions in the hierarchy.

### Dean/Chair Views Hierarchy (Read-Only)

1. Dean navigates to hierarchy → redirected to departments index for their college.
2. Chair navigates to hierarchy → redirected to departments index for the college containing their department.
3. Both see assignments but assign/remove controls are hidden (admin only).
