# Organizational Hierarchy (Dean, Chair, Faculty)

Rules for organizational assignments and what appears in hierarchy views.

## Files Used (Source of Truth)

- Controller
  - `app/Http/Controllers/OrganizationalHierarchyController.php`
  - `app/Http/Controllers/AcademicStructureController.php` (assignment cleanup on structure delete)
  - `app/Services/AccountApprovalService.php` (assignment cleanup on status change)
- Services
  - `app/Services/OrganizationalHierarchy/OrganizationalHierarchyService.php`
  - `app/Services/OrganizationalHierarchy/OrganizationalHierarchyChecker.php`
- Models
  - `app/Models/User.php`
  - `app/Models/UserAssignment.php`
- Routes
  - `routes/web.php` (organizational hierarchy routes)

## Preconditions (Applies to All Assignments)

- If the target user does not exist:
  - Then assignment is blocked.
- If the target college/department does not exist:
  - Then assignment is blocked.
- If a user account is not `active`:
  - Then they should not appear in potential-assignee lists.

## Conditions (If / Then)

### Dean Assignment

- If assigning a dean:
  - Then user must have role `dean` or `admin`.
  - Then user must not currently be assigned as chair (mutual exclusivity — checked by `OrganizationalHierarchyChecker`).
  - Then user must not already be assigned as dean of any college.
  - Then duplicate dean assignment to the same college is blocked.

### Dean Assignment (Behavior)

- If dean assignment succeeds:
  - Then create `user_assignments` row:
    - `context = dean`
    - `college_id` set
    - `department_id = null`
  - Then ensure faculty role and corresponding faculty assignment via `ensureFacultyRoleAndAssignment()`.

### Chair Assignment

- If assigning a chair:
  - Then user must have role `chair` or `admin`.
  - Then user must not currently be assigned as dean (mutual exclusivity — checked by `OrganizationalHierarchyChecker`).
  - Then user must not already be assigned as chair of any department.
  - Then duplicate chair assignment to the same department is blocked.

### Chair Assignment (Behavior)

- If chair assignment succeeds:
  - Then create `user_assignments` row:
    - `context = chair`
    - `department_id` set
    - `college_id = null`
  - Then ensure faculty role and corresponding faculty assignment via `ensureFacultyRoleAndAssignment()`.

### Faculty Assignment

- If assigning a faculty member:
  - Then user must have role `faculty` or `admin`.
  - Then duplicate faculty assignment to the same department is blocked.

### Faculty Assignment (Behavior)

- If faculty assignment succeeds:
  - Then create `user_assignments` row:
    - `context = faculty`
    - `department_id` set
    - `college_id = null`

### Dean + Chair Mutual Exclusivity (Role Assignment Path)

- If `assignRoles()` is called with both `dean` and `chair` in the role set:
  - Then assignment is blocked with a 422 error.
  - Then this guard applies even if the hierarchy checker is bypassed.

### Assignment Cleanup (Account Status)

- If a user is `rejected`:
  - Then all `user_assignments` for that user are deleted (all contexts).
- If a user is `disabled`:
  - Then all `user_assignments` for that user are deleted (all contexts).
- If a user is `restored` to pending:
  - Then no assignment cleanup (user has no active assignments at this point).

### Assignment Cleanup (Role Removal)

- If `dean` role is removed via `assignRoles()`:
  - Then all `user_assignments` where `context = dean` are deleted for that user.
- If `chair` role is removed via `assignRoles()`:
  - Then all `user_assignments` where `context = chair` are deleted for that user.
- If `faculty` role is removed via `assignRoles()` (future scenario):
  - Then all `user_assignments` where `context = faculty` are deleted for that user.

### Assignment Cleanup (Structure Delete)

- If a college is deleted:
  - Then all `user_assignments` where `college_id = college.id` are deleted (dean assignments).
  - Then for each department under the college:
    - Then all `user_assignments` where `department_id = department.id` are deleted (chair + faculty).
- If a department is deleted:
  - Then all `user_assignments` where `department_id = department.id` are deleted (chair + faculty).

## Removal Rules

- Dean removal deletes assignment by `(college_id, user_id, context=dean)`.
- Chair removal deletes assignment by `(department_id, user_id, context=chair)`.
- Faculty removal deletes assignment by `(department_id, user_id, context=faculty)`.

## Potential Assignee Lists

- Potential dean list:
  - Users with role `admin` or `dean`
  - Account is active
  - Excluding users already assigned as dean
- Potential chair list:
  - Users with role `admin` or `chair`
  - Account is active
  - Excluding users already assigned as chair
- Potential faculty list:
  - Users with role `admin` or `faculty`
  - Account is active

## Hierarchy View Conditions

- If logged-in user is dean:
  - Then show dean's assigned college.
  - Then show each department with its chair and faculty.
- If logged-in user is chair:
  - Then show chair's department and its faculty list.
- If neither has a matching assignment:
  - Then show no-assignment view.

## Sequences (Typical Flow)

### Assign Dean/Chair/Faculty

1. Admin selects target scope (college or department).
2. Admin selects a user from the filtered potential list.
3. System validates role + exclusivity constraints.
4. System creates a `user_assignments` row for the context.
5. System ensures faculty role/assignment when required.

### Disable User → Hierarchy Cleanup

1. Admin disables a user.
2. System sets `account_status = disabled`.
3. System deletes all `user_assignments` for that user.
4. User disappears from all dean/chair/faculty positions in the hierarchy.
