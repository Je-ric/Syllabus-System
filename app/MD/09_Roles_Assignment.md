# Roles and Assignments (Current Implementation)

This document describes how user roles and organizational assignments currently work in CSMS.

## Scope

Source of truth:
- `app/Http/Controllers/AccountApprovalController.php`
- `app/Http/Controllers/OrganizationalHierarchyController.php`
- `app/Models/User.php`

## Core Concept

The system uses two layers:
1. Roles (`user_roles`) control permissions.
2. Assignments (`user_assignments`) control organizational scope.

## Role Rules (AccountApprovalController)

## Assign role conditions
- `user_id` must exist.
- `roles` must be an array.
- Allowed values: `admin`, `chair`, `dean`, `faculty`.
- User account must be `active` before roles can be assigned.

## Automatic behavior
- `faculty` is always forced into the assigned role set.
- Roles are synced (`roles()->sync(...)`), so unselected roles are removed.

## Cleanup behavior on role removal
- If `dean` role is removed, all `user_assignments` with `context=dean` for that user are deleted.
- If `chair` role is removed, all `user_assignments` with `context=chair` for that user are deleted.

## Account status actions
- `approve`: sets `account_status=active`, ensures faculty role exists, sends status email.
- `reject`: sets `account_status=rejected`, sends status email.
- `restore`: sets `account_status=pending`.
- `disable`: sets `account_status=disabled`, sends status email.

## Assignment Rules (OrganizationalHierarchyController)

## Dean assignment
Conditions:
- `college_id` and `user_id` must exist.
- User must have role `dean` or `admin`.
- User must not already be assigned as chair (mutual exclusivity).
- User must not already be dean of any college.

Behavior:
- Creates `user_assignments` row with `context=dean`, `college_id`, `department_id=null`.
- Calls `ensureFacultyRoleAndAssignment($collegeId, null)`.

## Chair assignment
Conditions:
- `department_id` and `user_id` must exist.
- User must have role `chair` or `admin`.
- User must not already be assigned as dean.
- User must not already be chair of any department.

Behavior:
- Creates `user_assignments` row with `context=chair`, `department_id`, `college_id=null`.
- Calls `ensureFacultyRoleAndAssignment(null, $departmentId)`.

## Faculty assignment
Conditions:
- `department_id` and `user_id` must exist.
- User must have role `faculty` or `admin`.
- Duplicate faculty assignment to same department is blocked.

Behavior:
- Creates `user_assignments` row with `context=faculty`, `department_id`, `college_id=null`.

## Important constraints (as implemented)
- A user cannot be both dean and chair at the same time.
- A user can only have one dean assignment (any college).
- A user can only have one chair assignment (any department).
- Faculty assignments can coexist with dean/chair context.

## User model helper behavior

From `User.php`:
- `getPrimaryDepartmentAssignment()` prioritizes chair, then faculty.
- `getPrimaryCollegeAssignment()` returns dean assignment.
- `isAssignedAsDean()` and `isAssignedAsChair()` are global checks.
- `ensureFacultyRoleAndAssignment()`:
  - Ensures faculty role exists and is attached.
  - Creates faculty assignment if missing for given college/department pair.

## Notes
- Permissions should not be inferred from assignments alone.
- Visibility/scope should not be inferred from roles alone.
- Both layers are required for consistent authorization.
