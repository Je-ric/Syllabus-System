# How Livewire Works in CSMS (Beginner-Friendly)

This guide explains Livewire in plain language for both technical and non-technical readers.

## What Livewire Is

- Livewire lets pages feel interactive without writing a lot of custom JavaScript.
- You write PHP component classes + Blade views.
- When users click/type/change inputs, Livewire sends small requests and updates only needed parts of the page.

## CSMS Livewire Components (Current)

- `app/Livewire/AccountApproval/ManageQueue.php`
- `app/Livewire/Programs/ProgramSelector.php`
- `app/Livewire/Programs/ManagePeos.php`
- `app/Livewire/Programs/ManagePos.php`
- `app/Livewire/Programs/PeoDisplay.php`
- `app/Livewire/Syllabus/SyllabusWizard.php`
- `app/Livewire/Syllabus/Steps/*`

## Basic Pattern in CSMS

Each component usually has:

1. Public properties (state used by the UI).
2. `mount()` for initial load.
3. Action methods (save/add/remove/toggle).
4. `render()` returning Blade view.
5. Event listeners using `#[On('event-name')]`.

## Common Livewire Syntax You See

## In Blade (View)

- `wire:model="field"`:
- Two-way bind input and component property.
- `wire:model.live`:
- More immediate syncing behavior.
- `wire:click="method"`:
- Call PHP method on click.
- `wire:submit.prevent="method"`:
- Prevent normal form submit, call Livewire method instead.
- `wire:loading`:
- Show/hide loading indicator while request is running.
- `wire:key="unique-id"`:
- Helps Livewire track repeated rows correctly.

## In Component Class (PHP)

- `public $field`: Property that view can read/write.
- `mount(...)`: Runs once when component starts.
- `updatedFieldName()`: Auto-called when specific property changes.
- `updated($property)`: Generic hook for any property update.
- `#[On('event-name')]`: Listen for event dispatched by another Livewire component.
- `$this->dispatch('event-name', key: value)`: Send event to other components/browser listeners.

## Event-Driven Flow in CSMS Wizard

- Parent wizard asks step to save via event: `syllabus-save-step`.
- Step component listens, validates, saves, then dispatches `syllabus-step-saved`.
- Parent receives it and updates UI state (`stepDirty`, timestamp, toast).

This is why components stay modular and still work together.

## Conditional Logic Style in Livewire Components

Typical conditions used in CSMS:

- If step is not active, ignore incoming save event.
- If required fields are incomplete, stop and show toast.
- If data is unchanged, skip unnecessary updates.
- If user tries to leave CO step with unsaved changes, block navigation.
- If selected ids are invalid, ignore action safely.

## Non-Technical Interpretation

- Component = one mini-screen with its own memory.
- Properties = fields currently shown on screen.
- Methods = what happens when user interacts.
- Dispatch/listen = components sending short messages to each other.

## Why CSMS Uses Livewire Here

- Faster development with Laravel Blade/PHP.
- Less custom JavaScript needed for forms/wizards.
- Cleaner server-side validation.
- Easier maintenance for form-heavy academic workflows.

## Quick Developer Tips

- Keep DB writes inside explicit save methods when possible.
- Use guard clauses (`if (...) return;`) to reduce bugs.
- Use `wire:key` on loops to avoid row-mismatch UI bugs.
- Keep event names consistent and meaningful.
