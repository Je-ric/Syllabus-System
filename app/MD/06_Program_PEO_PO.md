# Program, PEO, and PO Rules

This document summarizes conditions for program-level PEO/PO management and code sequencing.

## Source Files

- `app/Http/Controllers/ProgramController.php`
- `app/Livewire/Programs/ManagePeos.php`
- `app/Livewire/Programs/ManagePos.php`
- `resources/views/livewire/programs/manage-peos.blade.php`
- `resources/views/livewire/programs/manage-pos.blade.php`

## Program Selection and Display

- Program page can open without selected program.
- When `program_id` query is present, selected program is loaded.
- Program `show` route renders same page with explicit program binding.

## PEO Rules (Livewire)

- If any `peo_text` is blank, save is blocked with warning toast.
- Existing PEOs are updated via `updateOrCreate`.
- Removed PEOs are deleted (diff between existing and submitted ids).
- PEO codes are resequenced after save using:
- `ProgramCodeHelper::resequencePeoCodes($programId)`
- Deleting PEO from controller also resequences codes.

## PO Rules (Livewire)

- If any `po_text` is blank, save is blocked with warning toast.
- Existing POs are updated via `updateOrCreate`.
- Removed POs are deleted (diff between existing and submitted ids).
- PO codes are resequenced after save using:
- `ProgramCodeHelper::resequencePoCodes($programId)`
- Deleting PO from controller also resequences codes.

## PO-PEO Mapping Rules

- PO-to-PEO links are stored through many-to-many sync (`program_outcome_peo`).
- Mapping is applied per saved PO id.
- If PEOs are updated, PO mapping is refreshed.
- Mapping cleanup removes references to deleted PEO ids.
- `toggleMapping()` checks:
- PO must already exist for the selected program.
- PEO id must belong to the same program.

## UI Conditions in Program Views

- Add PEO/PO button blocks insertion when a blank row already exists.
- Unsaved rows can be removed client-side.
- Saved rows use delete routes:
- `/programs/peo/{id}`
- `/programs/po/{id}`
