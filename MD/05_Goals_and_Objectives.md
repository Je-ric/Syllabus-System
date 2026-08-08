# College Goals and Department Objectives

Rules and flow for encoding College Goals and Department Objectives.

## Files Used (Source of Truth)

- Controllers
  - `app/Http/Controllers/CQI/GoalController.php` — College goals CRUD
  - `app/Http/Controllers/CQI/ObjectiveController.php` — Department objectives CRUD
  - `app/Http/Controllers/CQI/ProgramController.php` — Program listing for PEO/PO management
- Service
  - `app/Services/CQI/GoalObjectiveService.php` — Goal and objective authorization checks
- Models
  - `app/Models/College.php` (`getNextGoalCode()`, `resequenceGoalCodes()`)
  - `app/Models/CollegeGoal.php`
  - `app/Models/Department.php` (`getNextObjectiveCode()`, `resequenceObjectiveCodes()`)
  - `app/Models/DepartmentObjective.php`
- Views
  - `resources/views/CQI/GoalObjective/goal.blade.php` — College goals listing
  - `resources/views/CQI/GoalObjective/objective.blade.php` — Department objectives listing
  - `resources/views/CQI/GoalObjective/modals/` — Add/update/delete modals for goals and objectives
- Routes
  - `routes/web.php` (goals routes — `role:admin,dean`)
    - `GET /goals` — index
    - `POST /goals` — store
    - `PUT /goals/{goal}` — update
    - `DELETE /goals/{goal}` — delete
  - `routes/web.php` (objectives routes — `role:admin,chair`)
    - `GET /objectives` — index
    - `POST /objectives` — store
    - `PUT /objectives/{objective}` — update
    - `DELETE /objectives/{objective}` — delete

## Conditions (If / Then)

### College Goals (Listing)

- If `college_id` is provided as a query param:
  - Then it becomes the selected college.
  - If the user is not admin:
    - Then the selection is overridden back to their assigned college (non-admin cannot view other colleges).
- If `college_id` is missing:
  - If user is admin:
    - Then default to the first college alphabetically.
  - If user is not admin:
    - Then default to the user's primary college assignment.
- If user has role `dean` and has no college assignment:
  - Then a `noAssignment` flag is set and the view shows a no-assignment message.

### College Goals (Create)

- If creating a goal:
  - Then `college_id` is required and must exist.
  - Then `goal_text` is required string.
  - Then `GoalObjectiveService::canManageGoal()` is checked:
    - If user is admin → always allowed.
    - If user has a college assignment → only allowed if it matches the target college.
    - If user has role `dean` but no assignment → allowed (fallback).
    - Otherwise → redirect with a warning toast.
- If create passes:
  - Then `college_goals_code` is auto-generated via `College::getNextGoalCode()`.
  - Then redirect to goal index for that college with a success toast.

### College Goals (Update)

- If updating a goal:
  - Then `goal_text` is required string.
  - Then `canManageGoal()` is checked (same scope rules as create).
  - Then goal code is not changed on update.
  - Then redirect to goal index for that college with a success toast.

### College Goals (Delete)

- If deleting a goal:
  - Then `canManageGoal()` is checked (same scope rules as create).
  - If check fails → redirect with a warning toast.
  - If authorized:
    - Then the goal is deleted inside a DB transaction.
    - Then remaining goal codes are resequenced via `College::resequenceGoalCodes()`.
    - Then resequence uses `lockForUpdate()` to prevent race conditions on concurrent deletes.
    - Then resequence orders by `id` (stable order, not by code).
    - Then codes are reassigned from `a` upward using the overflow-safe logic.
  - If DB error occurs → redirect back with an error.

### Department Objectives (Listing)

- If `college_id` and `department_id` are in the query:
  - Then they become the selected college and department.
  - If user is not admin:
    - Then the selection is overridden back to their assigned department (non-admin cannot switch departments).
- If query params are missing:
  - If user is admin:
    - Then default to first college and first department under it alphabetically.
  - If user is not admin:
    - Then defaults come from user's primary department assignment.
- Departments shown are restricted to the selected college.
- If user has role `chair` and has no department assignment:
  - Then a `noAssignment` flag is set and the view shows a no-assignment message.

### Department Objectives (Create)

- If creating an objective:
  - Then `college_id` is required and must exist.
  - Then `department_id` is required.
  - Then the department must belong to the selected college (validated via `Rule::exists` with a `where` constraint).
  - Then `objective_text` is required string.
  - Then `GoalObjectiveService::canManageObjective()` is checked:
    - If user is admin → always allowed.
    - If user has a department assignment → only allowed if it matches the target department.
    - If user has role `chair` but no assignment → allowed (fallback).
    - Otherwise → redirect with a warning toast.
- If create passes:
  - Then `dept_obj_code` is auto-generated via `Department::getNextObjectiveCode()`.
  - Then redirect to objective index for that college + department with a success toast.

### Department Objectives (Update)

- If updating an objective:
  - Then `objective_text` is required string.
  - Then `canManageObjective()` is checked (same scope rules as create).
  - Then objective code is not changed on update.
  - Then redirect to objective index for that college + department with a success toast.

### Department Objectives (Delete)

- If deleting an objective:
  - Then `canManageObjective()` is checked (same scope rules as create).
  - If check fails → redirect with a warning toast.
  - If authorized:
    - Then the objective is deleted inside a DB transaction.
    - Then remaining codes are resequenced via `Department::resequenceObjectiveCodes()`.
    - Then resequence uses `lockForUpdate()` to prevent race conditions on concurrent deletes.
    - Then resequence orders by `id` (stable order).
    - Then codes are reassigned from `a` upward using the overflow-safe logic.
  - If DB error occurs → redirect back with an error.

## Code Generation Logic (Model Helpers)

Both `College::getNextGoalCode()` and `Department::getNextObjectiveCode()` use the same pattern:

- If `count < 26`:
  - Then `chr(ord('a') + count)` → single letter (`a`–`z`).
- If `count >= 26`:
  - Then `first = chr(ord('a') + intdiv(count, 26) - 1)`
  - Then `second = chr(ord('a') + (count % 26))`
  - Then code = `first . second` → two letters (`aa`, `ab`, …).

Resequencing uses the same formula, iterating over rows ordered by `id`, reassigning from index `0` upward.

## Sequences (Typical Flow)

### Add a College Goal

1. User selects a college (scoped to their assignment if non-admin).
2. User encodes goal text.
3. System validates required fields and scope authorization.
4. System inserts a goal and assigns the next goal code.

### Add a Department Objective

1. User selects a college, then a department (scoped to their assignment if non-admin).
2. User encodes objective text.
3. System validates department belongs to college and checks scope authorization.
4. System inserts objective and assigns the next objective code.

### Delete and Resequence

1. User deletes a goal or objective.
2. System verifies scope authorization.
3. System deletes the row inside a transaction.
4. System acquires a row-level lock on remaining rows (`lockForUpdate`).
5. System reassigns codes from `a` upward in stable `id` order.
