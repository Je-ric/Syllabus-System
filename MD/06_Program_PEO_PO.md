# Program, PEO, and PO

Rules for program-level PEO/PO management, code sequencing, and PO↔PEO mapping.

## Files Used (Source of Truth)

- Program CRUD
  - `app/Http/Controllers/AcademicStructureController.php` (program is managed under Academic Structure)
- Livewire PEO
  - `app/Livewire/Programs/ManagePeos.php`
  - `resources/views/livewire/programs/manage-peos.blade.php`
- Livewire PO
  - `app/Livewire/Programs/ManagePos.php`
  - `resources/views/livewire/programs/manage-pos.blade.php`
- Models
  - `app/Models/ProgramEducationalObjective.php`
  - `app/Models/ProgramOutcome.php`
- Mapping pivots
  - `program_outcome_peo` (PO ↔ PEO)
  - `course_curriculum_maps` (PO ↔ Course)
- Helper
  - `app/Helpers/ProgramCodeHelper.php`
- Routes
  - `routes/web.php` (PEO + PO routes — `role:admin,chair`)

## Conditions (If / Then)

### Program Authorization (Mount Guard)

- If the logged-in user is not admin:
  - Then check if the program belongs to the user's assigned department.
  - If it does not → flash warning toast and redirect to program index.
- This check applies to both `ManagePeos` and `ManagePos` on `mount()`.

### PEO Rules (Livewire — Save)

- If any `peo_text` in the submitted rows is blank:
  - Then a `RuntimeException` is thrown and a toast error is dispatched.
  - Then nothing is saved.
- If saving PEOs:
  - Then PEOs absent from the submission (by id) are deleted.
  - Then existing PEO rows are updated in place (text only; code is not updated here).
  - Then new rows (no id) are inserted with `peo_code = null`.
  - Then `ProgramCodeHelper::resequencePeoCodesOrdered()` is called with the final ordered id list (submitted ids first, then newly inserted ids).
  - Then codes are assigned in the visual order the user has arranged the rows.
  - Then a `peosUpdated` event is dispatched so `ManagePos` on the same page can refresh its PEO list.
  - Then a `peos-saved` event is dispatched with the updated PEO array.

### PO Rules (Livewire — Save)

- If any `po_text` in the submitted rows is blank:
  - Then a `RuntimeException` is thrown and a toast error is dispatched.
  - Then nothing is saved.
- If saving POs:
  - Then POs absent from the submission (by id) are deleted.
  - Then existing PO rows are updated in place (text only).
  - Then new rows (no id) are inserted with `po_code = null`.
  - Then `ProgramCodeHelper::resequencePoCodesOrdered()` is called with the final ordered id list.
  - Then codes are assigned in the visual order the user has arranged the rows.
  - Then PEO mappings for each existing PO are synced from the submitted `mappingData`.
  - Then new rows (no id yet during save) do not get mapping synced — mapping must be set after save.
  - Then a `pos-saved` event is dispatched with the updated PO and mapping arrays.

### PO Delete (Controller Route)

- If deleting a PO via `DELETE /programs/po/{id}`:
  - If the PO is mapped in any existing syllabus course outcomes:
    - Then delete is blocked with an error message showing the count of affected course outcomes.
  - If not mapped in any syllabus:
    - Then detach all PEO mappings from `program_outcome_peo`.
    - Then detach all course curriculum mappings from `course_curriculum_maps`.
    - Then delete the PO.
    - Then codes are resequenced after delete.

### PEO Delete (Controller Route)

- If deleting a PEO via `DELETE /programs/peo/{id}`:
  - Then detach all PO mappings from `program_outcome_peo` before deleting.
  - Then delete the PEO.
  - Then codes are resequenced after delete.

### PO ↔ PEO Toggle Mapping (Live)

- If `toggleMapping(poId, peoId, checked)` is called:
  - If the PO does not exist for the selected program:
    - Then dispatch warning toast: "Save PO row first before mapping."
  - If the PEO id does not belong to the same program:
    - Then silently return (no-op).
  - If `checked = true`:
    - Then add the PEO to the PO via `syncWithoutDetaching`.
  - If `checked = false`:
    - Then detach the PEO from the PO.
  - Then reload the mapping state.

### PEOs Updated — Cross-Component Sync

- When `ManagePeos` dispatches the `peosUpdated` event:
  - Then `ManagePos` receives it via `#[On('peosUpdated')]`.
  - Then `ManagePos` reloads its local PEO list.
  - Then `ManagePos` cleans up its in-memory mapping: any mapping referencing a deleted PEO id is removed.

### UI Guardrails

- If a blank row already exists in the list:
  - Then adding another blank row is blocked client-side.
- If a row has no id (unsaved):
  - Then it can be removed client-side without a server call.
- If a row has an id (saved):
  - Then removing it during the next save triggers a server-side delete.

## Sequences (Typical Flow)

### Save PEOs

1. User adds, edits, or removes rows in the Livewire component.
2. User clicks Save. Component validates all text fields are non-empty.
3. Component deletes removed rows, updates existing, inserts new.
4. Component resequences codes in the submitted visual order.
5. `peosUpdated` event fires; `ManagePos` refreshes its PEO columns and cleans up stale mappings.

### Save POs

1. User adds, edits, reorders, or removes rows.
2. User sets PEO mapping checkboxes per row.
3. User clicks Save. Component validates all text fields are non-empty.
4. Component deletes removed rows, updates existing, inserts new.
5. Component resequences codes in the submitted visual order.
6. Component syncs PEO mappings for existing PO rows from `mappingData`.

### Delete PEO

1. User clicks delete on a PEO row.
2. System detaches all PO mappings for that PEO.
3. System deletes the PEO.
4. System resequences remaining PEO codes.

### Delete PO

1. User clicks delete on a PO row.
2. System checks if the PO is mapped in any syllabus course outcomes.
3. If mapped → blocked with error showing count.
4. If not mapped → system detaches PEO and course mappings, deletes PO, resequences codes.

### Toggle PO↔PEO Mapping

1. User clicks a mapping checkbox (PO row × PEO column).
2. Component validates program ownership and PO existence.
3. Component adds or removes the mapping in `program_outcome_peo`.
4. Mapping state is reloaded and reflected in the UI.
