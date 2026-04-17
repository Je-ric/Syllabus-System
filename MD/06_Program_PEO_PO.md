# Program, PEO, and PO

Rules for program-level PEO/PO management, code sequencing, and PO↔PEO mapping.

## Files Used (Source of Truth)

- Program CRUD
  - `app/Http/Controllers/ProgramController.php`
  - `app/Models/Program.php`
- Livewire PEO
  - `app/Livewire/Programs/ManagePeos.php`
  - `resources/views/livewire/programs/manage-peos.blade.php`
- Livewire PO
  - `app/Livewire/Programs/ManagePos.php`
  - `resources/views/livewire/programs/manage-pos.blade.php`
- Models
  - `app/Models/ProgramEducationalObjective.php`
  - `app/Models/ProgramOutcome.php`
  - `app/Models/CourseOutcome.php`
- Mapping pivots
  - `program_outcome_peo` (PO ↔ PEO)
  - `course_curriculum_maps` (PO ↔ Course)
- Helper
  - `app/Helpers/ProgramCodeHelper.php`
- Routes
  - `routes/web.php` (program + PEO + PO routes — `role:admin,chair`)

## Conditions (If / Then)

### Program Selection

- If the page opens without a selected program:
  - Then it still renders, but lists/forms may be empty.
- If `program_id` is present in the query:
  - Then that program is loaded and becomes the selected program.
- Program `show` route renders the same page but with explicit program binding.

### PEO Rules (Livewire)

- If any `peo_text` is blank:
  - Then save is blocked with a warning toast.
- If saving PEOs:
  - Then existing PEOs are updated via `updateOrCreate`.
  - Then removed PEOs are deleted (diff vs submitted ids).
  - Then PEO codes are resequenced using `ProgramCodeHelper::resequencePeoCodes($programId)`.
- If deleting a PEO via controller route:
  - Then detach all PO mappings from `program_outcome_peo` before deleting.
  - Then delete the PEO.
  - Then codes are resequenced after delete.

### PO Rules (Livewire)

- If any `po_text` is blank:
  - Then save is blocked with a warning toast.
- If saving POs:
  - Then existing POs are updated via `updateOrCreate`.
  - Then removed POs are deleted (diff vs submitted ids).
  - Then PO codes are resequenced using `ProgramCodeHelper::resequencePoCodes($programId)`.
- If deleting a PO via controller route:
  - If the PO is mapped in any existing syllabus course outcomes:
    - Then delete is blocked with an error message showing the count of affected course outcomes.
  - If not mapped in any syllabus:
    - Then detach all PEO mappings from `program_outcome_peo`.
    - Then detach all course curriculum mappings from `course_curriculum_maps`.
    - Then delete the PO.
    - Then codes are resequenced after delete.

### PO ↔ PEO Mapping

- Mapping is stored via many-to-many sync on `program_outcome_peo`.
- If PEOs are updated:
  - Then PO mapping is refreshed.
  - Then mapping cleanup removes references to deleted PEO ids.
- If `toggleMapping()` is called:
  - Then the PO must already exist for the selected program.
  - Then the PEO id must belong to the same program.

### UI Guardrails

- If a blank row already exists:
  - Then Add PEO/PO blocks inserting another blank row.
- If a row is unsaved (no id yet):
  - Then it can be removed client-side.
- If a row is saved (has id):
  - Then it uses delete routes:
    - `DELETE /programs/peo/{id}`
    - `DELETE /programs/po/{id}`

## Sequences (Typical Flow)

### Save PEOs/POs

1. User edits rows in Livewire.
2. On Save, component validates all text fields are non-empty.
3. Component upserts rows (update existing + create new).
4. Component deletes removed rows.
5. Component resequences codes.

### Delete PEO

1. User clicks delete on a PEO row.
2. System detaches all PO mappings for that PEO.
3. System deletes the PEO.
4. System resequences remaining PEO codes.

### Delete PO

1. User clicks delete on a PO row.
2. System checks if the PO is mapped in any syllabus course outcomes.
3. If mapped → blocked with error.
4. If not mapped → system detaches PEO and course mappings, deletes PO, resequences codes.

### Toggle PO↔PEO Mapping

1. User clicks a mapping cell (PO row × PEO column).
2. Component validates program ownership and row existence.
3. Component updates the pivot table for that PO.
