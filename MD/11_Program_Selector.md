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

Reusable selector for:
- College
- Department
- Program

Supports optional auto-redirect after program selection.

## Public Properties

- `colleges`
- `departments`
- `programs`
- `collegeId`
- `departmentId`
- `programId`
- `redirectRoute` (nullable)
- `autoRedirect` (default `true`)

## Mount Parameters

```php
mount($programId = null, $redirectRoute = null, $autoRedirect = true)
```

## Conditions (If / Then)

### Mount Behavior

- On mount:
  - Then load all colleges ordered by name.
  - Then store redirect config.
- If `programId` is provided:
  - Then preselect `collegeId`, `departmentId`, `programId` based on that Program.
- If `programId` is not provided:
  - Then attempt preselect from user assignments.

### Assignment-Based Preselection

Priority:

1. Department assignment (`chair` first, then `faculty`)
2. College assignment (`dean`)

Rules:
- If user has a department assignment:
  - Then preselect college + department.
  - Then load department programs.
  - If exactly one program exists:
    - Then auto-select it.
- If user has a college assignment:
  - Then preselect college.
  - If exactly one department exists:
    - Then auto-select it.
    - If that department has exactly one program:
      - Then auto-select it.

### Redirect Behavior (`updatedProgramId()`)

- When `programId` changes:
  - Then always dispatch `programSelected` with selected program id.
  - If `autoRedirect = true` and `redirectRoute` is set:
    - Then redirect using the special rules below.

Special route handling:
- If `redirectRoute = courses.index`:
  - Then redirect with query: `?program_id={id}`.
- If `redirectRoute = syllabus.create`:
  - Then redirect with query: `?program_id={id}`.
- Else:
  - Then redirect using route param: `route($redirectRoute, $programId)`.

Mount-time redirect helper also supports:
- If `redirectRoute = programs.show`:
  - Then redirect via `route('programs.show', ['program' => $id])`.

## Sequences

### `updatedCollegeId()`

1. Load departments by selected college.
2. Reset `departmentId` and `programId`.
3. Dispatch `programSelected` with `null`.

### `updatedDepartmentId()`

1. Load programs by selected department.
2. Reset `programId`.
3. Dispatch `programSelected` with `null`.

### `updatedProgramId()`

1. Dispatch `programSelected` with selected id.
2. Redirect if enabled/configured.

## Event Dispatched

Event:

```text
programSelected
```

Payload:
- `programId` (int|null)

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

## Notes

- Prefer listening to `programSelected` for integration.
- Keep route names valid; invalid route names will fail redirect.
- Avoid relying on hardcoded DOM selectors when multiple selector instances exist.
