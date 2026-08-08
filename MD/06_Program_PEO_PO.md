# Program, PEO, and PO

Rules for program-level PEO/PO management, code sequencing, and PO↔PEO mapping.

## Files Used (Source of Truth)

- Controllers
  - `app/Http/Controllers/University/UniversityStructureController.php` — academic structure CRUD (colleges, departments, programs)
  - `app/Http/Controllers/CQI/ProgramController.php` — program listing, show, PEO/PO delete routes
- Livewire
  - `app/Livewire/Programs/ManagePeos.php` — PEO CRUD and reordering
  - `app/Livewire/Programs/ManagePos.php` — PO CRUD, PEO mapping, and reordering
  - `app/Livewire/Programs/PeoDisplay.php` — Read-only PEO display
  - `app/Livewire/Programs/MatrixView.php` — PO↔PEO matrix view
  - `app/Livewire/Programs/ProgramSelector.php` — Program selector component
- Models
  - `app/Models/Program.php`
  - `app/Models/ProgramEducationalObjective.php` (table: `program_eos`)
  - `app/Models/ProgramOutcome.php`
- Mapping pivots
  - `program_outcome_peo` (PO ↔ PEO, foreign keys: `program_outcome_id`, `program_eo_id`)
  - `course_curriculum_maps` (PO ↔ Course, with `ied` column)
- Helper
  - `app/Helpers/ProgramCodeHelper.php`
- Views (controller-rendered)
  - `resources/views/Programs/index.blade.php` — Programs listing
  - `resources/views/UniversityStructure/index.blade.php` — Academic structure management (colleges, departments, programs)
  - `resources/views/UniversityStructure/modals/` — Add/update/delete modals for colleges, departments, and programs
- Views (Livewire)
  - `resources/views/livewire/programs/manage-peos.blade.php`
  - `resources/views/livewire/programs/manage-pos.blade.php`
  - `resources/views/livewire/programs/peo-display.blade.php`
  - `resources/views/livewire/programs/matrix-view.blade.php`
  - `resources/views/livewire/programs/program-selector.blade.php`
  - `resources/views/livewire/programs/partials/`
- Routes
  - `routes/web.php` (program routes — `role:admin,chair`)
    - `GET /programs` — index
    - `GET /programs/{program}` — show
    - `DELETE /programs/peo/{peo}` — delete PEO
    - `DELETE /programs/po/{po}` — delete PO
  - `routes/web.php` (academic structure routes — `role:admin`)
    - `GET /university-structure` — index
    - `POST /university-structure/colleges` — store college
    - `PUT /university-structure/colleges/{college}` — update college
    - `DELETE /university-structure/colleges/{college}` — delete college
    - `POST /university-structure/departments` — store department
    - `PUT /university-structure/departments/{department}` — update department
    - `DELETE /university-structure/departments/{department}` — delete department
    - `POST /university-structure/programs` — store program
    - `PUT /university-structure/programs/{program}` — update program
    - `DELETE /university-structure/programs/{program}` — delete program

## Key Concepts

- `ProgramEducationalObjective` uses the table name `program_eos` (not `program_educational_objectives`).
- PEO codes and PO codes are auto-generated letters (`a`, `b`, ..., `z`, `aa`, `ab`, ...) using `ProgramCodeHelper`.
- All codes are nulled first before resequencing to avoid unique constraint collisions during reorder.
- `resequencePeoCodes()` and `resequencePoCodes()` are deprecated — use `resequencePeoCodesOrdered()` and `resequencePoCodesOrdered()` instead.
- `PeoDisplay` and `MatrixView` are read-only reactive components that refresh when `peosUpdated` or `pos-saved` events are dispatched.

## Conditions (If / Then)

### Program Authorization (Mount Guard — ManagePeos and ManagePos)

- If the logged-in user is not admin:
  - Then check if the program belongs to the user's assigned department.
  - If it does not → flash warning toast and `$this->redirect(route('programs.index'))`.
- This check runs in `mount()` of both `ManagePeos` and `ManagePos`.

### PEO Rules (Livewire — `savePeos`)

- If any `peo_text` in the submitted rows is blank:
  - Then `RuntimeException` thrown, caught, error toast dispatched.
  - Then nothing is saved.
- If all rows are non-empty:
  - Then runs inside a DB transaction:
    - PEOs absent from the submission (had an id, not in submitted list) are hard-deleted.
    - Existing rows are updated (text only).
    - New rows (no id) are inserted with `peo_code = null`.
    - `ProgramCodeHelper::resequencePeoCodesOrdered()` is called with submitted ids first, then new ids.
    - Codes are nulled globally first, then assigned in visual order.
  - Then AuditLog recorded.
  - Then `lw-toast` success dispatched.
  - Then `peosUpdated` event dispatched with `programId` — triggers `ManagePos`, `PeoDisplay`, and `MatrixView` to refresh.
  - Then `peos-saved` event dispatched with updated PEO array.

### PO Rules (Livewire — `savePos`)

- If any `po_text` in the submitted rows is blank:
  - Then `RuntimeException` thrown, caught, error toast dispatched.
  - Then nothing is saved.
- If all rows are non-empty:
  - Then runs inside a DB transaction:
    - POs absent from the submission are hard-deleted.
    - Existing rows are updated (text only).
    - New rows (no id) are inserted with `po_code = null`.
    - `ProgramCodeHelper::resequencePoCodesOrdered()` is called with submitted ids first, then new ids.
    - PEO mappings for each **existing** PO are synced from `mappingData` via `$po->peos()->sync()`.
    - New rows (no id at submission time) do not get mapping synced — must be set after next save.
  - Then AuditLog recorded.
  - Then `lw-toast` success dispatched.
  - Then `pos-saved` event dispatched with updated PO and mapping arrays — triggers `MatrixView` to refresh.

### PO ↔ PEO Toggle Mapping (Live — `toggleMapping`)

- If the PO does not exist for the selected program:
  - Then dispatch warning toast: "Save PO row first before mapping."
- If the PEO id does not belong to the same program:
  - Then silently return (no-op).
- If `checked = true`:
  - Then add the PEO to the PO via `$po->peos()->syncWithoutDetaching([$peoId])`.
- If `checked = false`:
  - Then detach the PEO via `$po->peos()->detach($peoId)`.
- Then AuditLog recorded (`mapped` or `unmapped`).
- Then `loadMapping()` reloads local mapping state.

### PEOs Updated — Cross-Component Sync

- When `ManagePeos` dispatches `peosUpdated`:
  - `ManagePos` (`#[On('peosUpdated')]` → `refreshPeos()`): reloads local PEO list; removes stale PEO ids from in-memory `$mapping`.
  - `PeoDisplay` (`#[On('peosUpdated')]` → `refreshPeos()`): reloads display list.
  - `MatrixView` (`#[On('peosUpdated')]` → `onPeosUpdated()`): reloads full matrix state.
- When `ManagePos` dispatches `pos-saved`:
  - `MatrixView` (`#[On('pos-saved')]` → `onPosSaved()`): reloads full matrix state.
- All listeners guard by `$this->program->id === $programId` before acting.

### PEO Delete (Controller — `deletePeo`)

- If deleting a PEO via `DELETE /programs/peo/{peo}`:
  - If user is not admin:
    - Then `abortIfNotAssignedToProgram()` checks department assignment.
    - If not assigned → redirect to `programs.index` with warning toast.
  - If authorized:
    - Then `$peo->outcomes()->detach()` removes all PO mappings from `program_outcome_peo`.
    - Then PEO is deleted.
    - Then `ProgramCodeHelper::resequencePeoCodes()` resequences remaining codes.
    - Then AuditLog recorded.
    - Then redirect to `programs.show` with success toast.
  - If any error: DB rolled back, redirect back with error toast.

### PO Delete (Controller — `deletePo`)

- If deleting a PO via `DELETE /programs/po/{po}`:
  - If user is not admin:
    - Then `abortIfNotAssignedToProgram()` checks department assignment.
  - If authorized:
    - Then count syllabi for courses that map this PO (via `course_curriculum_maps`).
    - If `$syllabusCount > 0`:
      - Then delete is blocked with error toast showing the count.
      - Then redirect back.
    - If no syllabi:
      - Then `$po->peos()->detach()` removes PEO mappings.
      - Then `$po->courses()->detach()` removes course curriculum mappings.
      - Then PO is deleted.
      - Then `ProgramCodeHelper::resequencePoCodes()` resequences remaining codes.
      - Then AuditLog recorded.
      - Then redirect to `programs.show` with success toast.
  - If any error: DB rolled back, redirect back with error toast.

### ProgramSelector — Scoped Access

- If `redirectRoute` is `programs.show` or `courses.index`:
  - Then `locked = true` for non-admin users.
  - Then colleges list is restricted to the user's assigned college/department.
  - Then `noAssignment = true` if chair has no department assignment.
- If `redirectRoute` is `syllabus.create` or other:
  - Then all colleges are shown regardless of role.
- On `updatedProgramId()`:
  - If `autoRedirect = true` and `redirectRoute` is set: redirects to the route with `program_id`.
  - Special cases: `courses.index` uses `?program_id=`, `programs.show` uses route model binding, `syllabus.create` uses `?program_id=`.
- On `mount()` with no explicit `programId`:
  - `preselectFromUserAssignments()` preselects college + department from user's assignment.
  - If the department has exactly 1 program: that program is also preselected and a redirect fires (if `autoRedirect`).

## Code Generation Logic (ProgramCodeHelper)

```
numberToLetter(n):
  - n=1 → "a", n=2 → "b", ..., n=26 → "z"
  - n=27 → "aa", n=28 → "ab", ...
  - Overflow-safe: uses modulo arithmetic, not fixed 26-letter cap.
```

Resequencing pattern (both PEO and PO):
1. Null all codes for the program (`UPDATE ... SET code = null`) — avoids unique constraint conflicts.
2. Loop over the ordered ID list, assigning `numberToLetter(position + 1)` to each.

## Sequences (Typical Flow)

### Save PEOs

1. User adds, edits, reorders, or removes rows in `ManagePeos`.
2. User clicks Save — `savePeos(peosData)` called.
3. Component validates all text fields are non-empty.
4. Transaction: delete removed, update existing, insert new, resequence codes.
5. `peosUpdated` dispatched → `ManagePos`, `PeoDisplay`, `MatrixView` all refresh.

### Save POs

1. User adds, edits, reorders PO rows; sets PEO mapping checkboxes per row.
2. User clicks Save — `savePos(posData, mappingData)` called.
3. Component validates all text fields are non-empty.
4. Transaction: delete removed, update existing, insert new, resequence codes, sync PEO mappings for existing rows.
5. `pos-saved` dispatched → `MatrixView` refreshes.

### Toggle PO↔PEO Mapping (Live)

1. User clicks a mapping checkbox in `ManagePos`.
2. `toggleMapping(poId, peoId, checked)` validates program ownership.
3. Adds or removes the mapping in `program_outcome_peo`.
4. AuditLog recorded. Local mapping reloaded.

### Delete PEO (via Route)

1. User clicks delete on a PEO from the Programs page.
2. Controller checks authorization.
3. Detaches all PO mappings, deletes PEO, resequences codes.
4. Redirect to program show with success toast.

### Delete PO (via Route)

1. User clicks delete on a PO from the Programs page.
2. Controller checks authorization.
3. If any syllabus maps a course that uses this PO → blocked with count.
4. If clear: detach PEO mappings, detach course mappings, delete PO, resequence.
5. Redirect to program show with success toast.
