# Livewire in CSMS (Beginner-Friendly)

Plain-language explanation of how Livewire is used in CSMS.

## Files Used (Examples)

- Components (examples)
  - `app/Livewire/AccountApproval/ManageQueue.php`
  - `app/Livewire/Programs/ProgramSelector.php`
  - `app/Livewire/Programs/ManagePeos.php`
  - `app/Livewire/Programs/ManagePos.php`
  - `app/Livewire/Programs/PeoDisplay.php`
  - `app/Livewire/Syllabus/SyllabusWizard.php`
  - `app/Livewire/Syllabus/Steps/*`
- Views (examples)
  - `resources/views/livewire/programs/*`
  - `resources/views/livewire/syllabus/*`

Related docs:
- `app/MD/10_Syllabus_Wizard.md`

## What Livewire Is

- Livewire lets pages feel interactive without writing a lot of custom JavaScript.
- You write PHP component classes + Blade views.
- When users click/type/change inputs, Livewire sends small requests and updates only needed parts of the page.

## Basic Pattern in CSMS

Each component usually has:

1. Public properties (state used by the UI).
2. `mount()` for initial load.
3. Action methods (save/add/remove/toggle).
4. `render()` returning Blade view.
5. Event listeners using `#[On('event-name')]`.

## Common Livewire Syntax

### In Blade (View)

- `wire:model="field"`
  - Two-way bind input and component property.
- `wire:model.live`
  - More immediate syncing behavior.
- `wire:click="method"`
  - Call PHP method on click.
- `wire:submit.prevent="method"`
  - Prevent normal submit; call method instead.
- `wire:loading`
  - Show/hide loading indicator while a request is running.
- `wire:key="unique-id"`
  - Helps Livewire track repeated rows correctly.

### In Component Class (PHP)

- `public $field`
  - Property that the view can read/write.
- `mount(...)`
  - Runs once when component starts.
- `updatedFieldName()`
  - Auto-called when a specific property changes.
- `updated($property)`
  - Generic hook for any property update.
- `#[On('event-name')]`
  - Listen for an event dispatched by another Livewire component.
- `$this->dispatch('event-name', key: value)`
  - Dispatch an event to other components/browser listeners.

## Conditions (Common Patterns)

Typical guard clauses used in CSMS Livewire components:

- If a step is not active:
  - Then ignore incoming save events.
- If required fields are incomplete:
  - Then stop and show a toast.
- If data is unchanged:
  - Then skip unnecessary writes.
- If user tries to leave CO step with unsaved changes:
  - Then block navigation.
- If selected ids are invalid:
  - Then ignore action safely.

## Sequences (How a Livewire Interaction Works)

### Typical request cycle

1. User interacts (click/type/change input).
2. Livewire sends an AJAX request with the changed data/action.
3. Component method runs on the server (validate, compute, save).
4. Component re-renders.
5. Browser patches only the relevant DOM updates.

### Event-driven wizard example (CSMS Syllabus)

1. Parent dispatches `syllabus-save-step`.
2. Step listens, validates, saves.
3. Step dispatches `syllabus-step-saved`.
4. Parent updates UI state (dirty flags, timestamps, toasts) and switches the active step.

## Non-Technical Interpretation

- Component = one mini-screen with its own memory.
- Properties = fields currently shown on screen.
- Methods = what happens when user interacts.
- Dispatch/listen = components sending short messages to each other.

## Why CSMS Uses Livewire

- Faster development with Laravel Blade/PHP.
- Less custom JavaScript needed for forms/wizards.
- Cleaner server-side validation.
- Easier maintenance for form-heavy academic workflows.

## Quick Developer Tips

- Keep DB writes inside explicit save methods when possible.
- Use guard clauses (`if (...) return;`) to reduce bugs.
- Use `wire:key` on loops to avoid row-mismatch UI bugs.
- Keep event names consistent and meaningful.
