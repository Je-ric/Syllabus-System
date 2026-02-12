# Organizational Hierarchy Rules

This document summarizes assignment conditions for dean, chair, and faculty across colleges/departments.

## Source Controller

- `app/Http/Controllers/OrganizationalHierarchyController.php`

## General Assignment Preconditions

- Target user must exist.
- Target college/department must exist.
- User account must be active to appear in potential-assignee lists.

## Dean Assignment Conditions

- User must have `dean` or `admin` role.
- User must not currently be assigned as chair.
- User must not already be assigned as dean of any college.
- Duplicate dean assignment to same college is blocked.

## Dean Assignment Behavior

- Creates `user_assignments` row with:
- `context = dean`
- `college_id` set
- `department_id = null`
- Automatically ensures faculty role and corresponding faculty assignment.

## Dean Removal

- Removes dean assignment by `(college_id, user_id, context=dean)`.

## Chair Assignment Conditions

- User must have `chair` or `admin` role.
- User must not currently be assigned as dean.
- User must not already be assigned as chair of any department.
- Duplicate chair assignment to same department is blocked.

## Chair Assignment Behavior

- Creates `user_assignments` row with:
- `context = chair`
- `department_id` set
- `college_id = null`
- Automatically ensures faculty role and corresponding faculty assignment.

## Chair Removal

- Removes chair assignment by `(department_id, user_id, context=chair)`.

## Faculty Assignment Conditions

- User must have `faculty` or `admin` role.
- Duplicate faculty assignment to same department is blocked.

## Faculty Assignment Behavior

- Creates `user_assignments` row with:
- `context = faculty`
- `department_id` set
- `college_id = null`

## Faculty Removal

- Removes faculty assignment by `(department_id, user_id, context=faculty)`.

## Potential User Lists Logic

- Potential dean list: users with role `admin` or `dean`, active accounts, excluding users already assigned as dean.
- Potential chair list: users with role `admin` or `chair`, active accounts, excluding users already assigned as chair.
- Potential faculty list: users with role `admin` or `faculty`, active accounts.

## Hierarchy View Conditions

- If logged-in user is dean:
- Show dean's assigned college.
- Show each department with its chair and faculty.
- If logged-in user is chair:
- Show chair's department and its faculty list.
- If neither has matching assignment:
- Show no-assignment view.

