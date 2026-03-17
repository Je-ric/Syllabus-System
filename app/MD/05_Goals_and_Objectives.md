# College Goals and Department Objectives

Rules and flow for encoding College Goals and Department Objectives.

## Files Used (Source of Truth)

- Controllers
  - `app/Http/Controllers/GoalController.php`
  - `app/Http/Controllers/ObjectiveController.php`
- Models
  - `app/Models/College.php`
  - `app/Models/CollegeGoal.php`
  - `app/Models/Department.php`
  - `app/Models/DepartmentObjective.php`
- Routes
  - `routes/web.php` (goals + objectives routes)

## Conditions (If / Then)

### College Goals (Listing)

- If `college_id` is provided as a query param:
  - Then it becomes the selected college.
- If `college_id` is missing:
  - Then default to the user’s primary college assignment (when available).
- Colleges are loaded in name order.

### College Goals (Create)

- If creating a goal:
  - Then `college_id` is required and must exist.
  - Then `goal_text` is required string.
- If create succeeds:
  - Then `college_goals_code` is auto-generated via `College::getNextGoalCode()`.

### College Goals (Update)

- If updating a goal:
  - Then `goal_text` is required string.

### College Goals (Delete)

- If deleting a goal:
  - Then the goal is removed.
  - Then remaining goal codes are resequenced via `College::resequenceGoalCodes()`.

### Department Objectives (Listing)

- If query params are provided:
  - Then `college_id` and `department_id` come from the query.
- If query params are missing:
  - Then defaults come from user’s primary department assignment.
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

### Department Objectives (Update)

- If updating an objective:
  - Then `objective_text` is required string.

### Department Objectives (Delete)

- If deleting an objective:
  - Then the objective is removed.
  - Then remaining codes are resequenced via `Department::resequenceObjectiveCodes()`.

## Sequences (Typical Flow)

### Add a College Goal

1. User chooses a college.
2. User encodes goal text.
3. System validates required fields.
4. System inserts a goal and assigns the next goal code.

### Add a Department Objective

1. User chooses a college, then a department.
2. User encodes objective text.
3. System validates department belongs to college.
4. System inserts objective and assigns the next objective code.
