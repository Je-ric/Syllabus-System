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
- Mapping pivot
  - `program_outcome_peo`
- Routes
  - `routes/web.php` (program + PEO + PO routes)

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
- If deleting a PEO from controller routes:
  - Then codes are resequenced after delete.

### PO Rules (Livewire)

- If any `po_text` is blank:
  - Then save is blocked with a warning toast.
- If saving POs:
  - Then existing POs are updated via `updateOrCreate`.
  - Then removed POs are deleted (diff vs submitted ids).
  - Then PO codes are resequenced using `ProgramCodeHelper::resequencePoCodes($programId)`.
- If deleting a PO from controller routes:
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
    - `/programs/peo/{id}`
    - `/programs/po/{id}`

## Sequences (Typical Flow)

### Save PEOs/POs

1. User edits rows in Livewire.
2. On Save, component validates all text fields are non-empty.
3. Component upserts rows (update existing + create new).
4. Component deletes removed rows.
5. Component resequences codes.

### Toggle PO↔PEO Mapping

1. User clicks a mapping cell (PO row × PEO column).
2. Component validates program ownership and row existence.
3. Component updates the pivot table for that PO.
