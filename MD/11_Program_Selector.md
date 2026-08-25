# ProgramSelector (Livewire Component)

Current behavior of `ProgramSelector` used across pages (Courses, Programs, Syllabus creation).

## Files Used (Source of Truth)

- Component
  - `app/Livewire/Programs/ProgramSelector.php`
- View
  - `resources/views/livewire/programs/program-selector.blade.php`
- Models (loaded by the selector)
  - `app/Models/College.php`
  - `app/Models/Department.php`
  - `app/Models/Program.php`
  - `app/Models/User.php` (assignment-based defaults)

## Purpose

Reusable cascading selector for:
- College → Department → Program

Supports:
- Optional auto-redirect after program selection.
- **Locked mode** for non-admin users with scoped access.
- Breadcrumb step indicators.
- **Security**: Scope-based access control and authorization checks for non-admin users.

## Public Properties

- `colleges` — all colleges (or scoped for non-admins)
- `departments` — departments for selected college
- `programs` — programs for selected department
- `collegeId` — wire:model.live
- `departmentId` — wire:model.live
- `programId` — wire:model.live
- `redirectRoute` — nullable route name to redirect to
- `autoRedirect` — default `true`
- `locked` — `bool`, when true shows read-only locked UI

## Security Implementation

### Authorization
- **Role-Based Access Control**: 
  - Admin users: Full access to all colleges/departments/programs
  - Non-admin users: Scoped access based on their organizational assignments
- **Scope Validation**: 
  - For `programs.show` and `courses.index`: Colleges/departments restricted to user's assigned scope
  - For `syllabus.create`: Full access (no scope restriction)
  - Authorization checks prevent access to programs outside user's department assignment

### Input Validation
- **Parameter Validation**: All IDs (collegeId, departmentId, programId) are validated to ensure they exist in the database.
- **Type Safety**: Livewire wire.model ensures type safety for all properties.
- **Route Validation**: Redirect routes are validated to prevent open redirect vulnerabilities.

### Scope-Based Security
- **Assignment-Based Defaults**: Preselection logic uses user's department/college assignments for secure defaults
- **Program Ownership Verification**: When non-admin users access specific programs, system verifies program belongs to their department
- **Access Denial**: Unauthorized access attempts result in warning toasts and redirects to appropriate index pages

### UI Security
- **Locked Mode**: Non-admin users in scoped contexts see read-only locked UI with lock icons
- **Prevent Interaction**: Locked mode prevents users from modifying their assigned scope
- **Visual Indicators**: Clear visual feedback shows users their current access level

### Rate Limiting
- **Current Status**: Rate limiting is not currently implemented on program selector operations.
- **Recommended Enhancement**: Add rate limiting to program selection endpoints to prevent automated enumeration.

## Mount Parameters

```php
mount($programId = null, $redirectRoute = null, $autoRedirect = true)
```

## Conditions (If / Then)

### Mount Behavior

- On mount:
  - If user is `admin`:
    - Then load all colleges ordered by name.
  - If user is not admin AND `redirectRoute` is `programs.show` or `courses.index`:
    - Then scope colleges to user's assigned college/department only.
    - Then set `locked = true`.
  - Else (syllabus creation, other contexts):
    - Then load all colleges (same as admin).
  - Then store redirect config.

- If `programId` is provided (via query param, highest priority):
  - Then preselect college, department, program based on that program.
  - If user is not admin AND in scoped context:
    - Then verify the program belongs to user's department.
    - If not allowed: show warning toast and redirect to programs index.

- If `programId` is not provided:
  - Then attempt preselect from user assignments.
  - If a program was preselected AND `autoRedirect` is enabled AND `redirectRoute` is set:
    - Then redirect to the appropriate route with the program ID (prevents redirect loops by checking current `program_id`).

### Assignment-Based Preselection

Priority:
1. Department assignment (`chair` first, then `faculty`) — uses `getPrimaryDepartmentAssignment()`
2. College assignment (`dean`) — uses `getPrimaryCollegeAssignment()`

Rules for department assignment:
- If user has a department assignment:
  - Then preselect college + department.
  - Then load department programs.
  - If exactly one program exists in that department:
    - Then auto-select it (dispatch `programSelected`, return program ID).

Rules for college assignment (fallback):
- If user has a college assignment:
  - Then preselect college.
  - If exactly one department exists in that college:
    - Then auto-select it.
    - If that department has exactly one program:
      - Then auto-select it.

### Redirect Behavior (`updatedProgramId()`)

- When `programId` changes:
  - Then always dispatch `programSelected` with selected program id.
  - If `autoRedirect = true` and `redirectRoute` is set:
    - If `redirectRoute = courses.index`: redirect with `?program_id={id}`.
    - If `redirectRoute = syllabus.create`: redirect with `?program_id={id}`.
    - Else: redirect using `route($redirectRoute, $programId)`.

### Mount-time Redirect

`redirectWithProgramId()` handles mount-time redirects:
- `courses.index` → with query `?program_id={id}`
- `syllabus.create` → with query `?program_id={id}`
- `programs.show` → `route('programs.show', $programId)`
- Generic → tries with `program_id` query param

### Locked Mode (Non-Admin Users)

- If `locked = true`:
  - Then the view renders read-only display fields instead of select dropdowns.
  - Then each field shows a lock icon.
  - Then breadcrumb still shows all three steps as completed.
  - No interaction possible — user can only see their assigned context.

## Sequences

### `updatedCollegeId()`

1. Load departments by selected college.
2. If not admin and scoped: filter to user's assigned department only.
3. Reset `departmentId` and `programId`.
4. Dispatch `programSelected` with `null`.

### `updatedDepartmentId()`

1. Load programs by selected department (via pivot table `department_program`).
2. Reset `programId`.
3. Dispatch `programSelected` with `null`.

### `updatedProgramId()`

1. Dispatch `programSelected` with selected id.
2. Redirect if enabled/configured.

## Event Dispatched

```
programSelected
```

Payload: `programId` (int|null)

## View Layout

- **Breadcrumb** at top: 1. College → 2. Department → 3. Program (with completion styling).
- **3-column grid** of select dropdowns:
  - College: always shown, wire:model.live="collegeId"
  - Department: disabled until college selected
  - Program: disabled until department selected
- **Loading spinners**: each dropdown has loading indicator on wire:target updates.
- **Confirmation chip**: when a program is selected, shows a green chip with the program name.
- **Locked mode**: same grid layout but all fields are read-only `<div>` with lock icon.

## Usage Examples

### With redirect (Courses)

```blade
<livewire:programs.program-selector
    :program-id="request('program_id')"
    redirect-route="courses.index"
    :autoRedirect="true"
/>
```

### Without redirect (listen mode)

```blade
<livewire:programs.program-selector :autoRedirect="false" />
```

### Program page

```blade
<livewire:programs.program-selector
    :program-id="optional($program)->id"
    redirect-route="programs.show"
    :autoRedirect="true"
/>
```

### Syllabus creation (no lock, all colleges visible)

```blade
<livewire:programs.program-selector
    :program-id="optional($program)?->id"
    redirect-route="syllabus.create"
    :autoRedirect="true" />
```

## Notes

- Prefer listening to `programSelected` for integration when `autoRedirect=false`.
- Non-admin users in Programs/Courses pages are scoped to their assignment — locked mode.
- Admin users always see all colleges/departments/programs.
- Keep route names valid; invalid route names will fail redirect.
- **Security Note**: The component implements scope-based access control to prevent unauthorized access to programs outside a user's department assignment.
