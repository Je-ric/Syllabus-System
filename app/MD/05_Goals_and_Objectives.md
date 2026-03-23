# College Goals and Department Objectives

Rules and flow for encoding College Goals and Department Objectives.

## Files Used (Source of Truth)

- Controllers
  - `app/Http/Controllers/GoalController.php`
  - `app/Http/Controllers/ObjectiveController.php`
- Models
  - `app/Models/College.php` (`getNextGoalCode()`, `resequenceGoalCodes()`)
  - `app/Models/CollegeGoal.php`
  - `app/Models/Department.php` (`getNextObjectiveCode()`, `resequenceObjectiveCodes()`)
  - `app/Models/DepartmentObjective.php`
- Routes
  - `routes/web.php` (goals routes — `role:admin,dean`; objectives routes — `role:admin,chair`)

## Conditions (If / Then)

### College Goals (Listing)

- If `college_id` is provided as a query param:
  - Then it becomes the selected college.
- If `college_id` is missing:
  - Then default to the user's primary college assignment (when available).
- Colleges are loaded in name order.

### College Goals (Create)

- If creating a goal:
  - Then `college_id` is required and must exist.
  - Then `goal_text` is required string.
- If create succeeds:
  - Then `college_goals_code` is auto-generated via `College::getNextGoalCode()`.
  - If the college has fewer than 26 goals:
    - Then code is a single letter: `a`, `b`, … `z`.
  - If the college has 26 or more goals:
    - Then code is two letters: `aa`, `ab`, … `az`, `ba`, … (overflow-safe).

### College Goals (Update)

- If updating a goal:
  - Then `goal_text` is required string.
  - Then goal code is not changed on update.

### College Goals (Delete)

- If deleting a goal:
  - Then the goal is removed.
  - Then remaining goal codes are resequenced via `College::resequenceGoalCodes()`.
  - Then resequence uses `lockForUpdate()` to prevent race conditions on concurrent deletes.
  - Then resequence orders by `id` (stable order, not by code).
  - Then codes are reassigned from `a` upward using the same overflow-safe logic.
  - Then all operations run inside a DB transaction.

### Department Objectives (Listing)

- If query params are provided:
  - Then `college_id` and `department_id` come from the query.
- If query params are missing:
  - Then defaults come from user's primary department assignment.
- Departments shown are restricted to the selected college.
- Objectives shown are restricted to the selected department.

### Department Objectives (Create)

- If creating an objective:
  - Then `college_id` is required and must exist.
  - Then `department_id` is required.
  - Then the department must belong to the selected college.
  - Then `objective_text` is required string.
- If create succeeds:
  - Then `dept_obj_code` is auto-generated via `Department::getNextObjectiveCode()`.
  - If the department has fewer than 26 objectives:
    - Then code is a single letter: `a`, `b`, … `z`.
  - If the department has 26 or more objectives:
    - Then code is two letters: `aa`, `ab`, … (overflow-safe, same logic as goals).

### Department Objectives (Update)

- If updating an objective:
  - Then `objective_text` is required string.
  - Then objective code is not changed on update.

### Department Objectives (Delete)

- If deleting an objective:
  - Then the objective is removed.
  - Then remaining codes are resequenced via `Department::resequenceObjectiveCodes()`.
  - Then resequence uses `lockForUpdate()` to prevent race conditions on concurrent deletes.
  - Then resequence orders by `id` (stable order).
  - Then codes are reassigned from `a` upward using the same overflow-safe logic.
  - Then all operations run inside a DB transaction.

## Code Generation Logic (Model Helpers)

Both `College::getNextGoalCode()` and `Department::getNextObjectiveCode()` use the same pattern:

- If `count < 26`:
  - Then `chr(ord('a') + count)` → single letter.
- If `count >= 26`:
  - Then `first = chr(ord('a') + intdiv(count, 26) - 1)`
  - Then `second = chr(ord('a') + (count % 26))`
  - Then code = `first . second` → two letters.

## Sequences (Typical Flow)

### Add a College Goal

1. User chooses a college.
2. User encodes goal text.
3. System validates required fields.
4. System inserts a goal and assigns the next goal code (overflow-safe).

### Add a Department Objective

1. User chooses a college, then a department.
2. User encodes objective text.
3. System validates department belongs to college.
4. System inserts objective and assigns the next objective code (overflow-safe).

### Delete and Resequence

1. User deletes a goal or objective.
2. System deletes the row inside a transaction.
3. System acquires a row-level lock on remaining rows (`lockForUpdate`).
4. System reassigns codes from `a` upward in stable `id` order.
