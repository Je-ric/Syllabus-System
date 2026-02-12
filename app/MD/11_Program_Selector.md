# ProgramSelector Component (Current Behavior)

This document reflects the current implementation of `ProgramSelector`.

## Source

- `app/Livewire/Programs/ProgramSelector.php`
- View: `resources/views/livewire/programs/program-selector.blade.php`

## Purpose

Reusable selector for:
- College
- Department
- Program

Used across pages (Courses, Programs, Syllabus creation) with optional auto-redirect.

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

Behavior:
- Loads all colleges ordered by name.
- Stores route/redirect config.
- If `programId` is provided: preselects college+department+program from that program.
- Else: attempts preselect from user assignments.

## Assignment-based Preselection

Priority used:
1. Department assignment (`chair` first, then `faculty`)
2. College assignment (`dean`)

Rules:
- If user has department assignment:
  - preselect college + department
  - load department programs
  - if exactly one program, auto-select it
- If user has college assignment:
  - preselect college
  - if exactly one department, auto-select it
  - if that department has exactly one program, auto-select it

## Redirect behavior

Program selection emits event first, then optional redirect.

On `updatedProgramId()`:
- Always dispatches `programSelected` with selected program id.
- Redirect only when:
  - `autoRedirect = true`
  - `redirectRoute` is set

Special route handling:
- `courses.index` -> redirects with query: `?program_id={id}`
- `syllabus.create` -> redirects with query: `?program_id={id}`
- Any other route -> redirects using route param (`route($redirectRoute, $programId)`)

Mount-time redirect (`redirectWithProgramId`) also handles:
- `programs.show` -> `route('programs.show', ['program' => $id])`

## Change Handlers

## `updatedCollegeId()`
- Loads departments by selected college.
- Resets department/program selections.
- Dispatches `programSelected` with `null`.

## `updatedDepartmentId()`
- Loads programs belonging to selected department.
- Resets `programId`.
- Dispatches `programSelected` with `null`.

## `updatedProgramId()`
- Dispatches selected program event.
- Performs redirect if enabled/configured.

## Event Dispatched

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

### Without redirect (AJAX/listen mode)
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

- Do not rely on hardcoded DOM selectors when using multiple selector instances.
- Prefer listening to the `programSelected` event for integration.
- Keep route names valid; invalid route names will fail redirect.
