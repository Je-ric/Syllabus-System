# Livewire in CSMS (Beginner-Friendly)

Plain-language explanation of how Livewire is used in CSMS.

## Files Used (Examples)

- Components (examples)
  - `app/Livewire/AccountApproval/ManageQueue.php`
  - `app/Livewire/Programs/ProgramSelector.php`
  - `app/Livewire/Programs/ManagePeos.php`
  - `app/Livewire/Programs/ManagePos.php`
  - `app/Livewire/Programs/PeoDisplay.php`
  - `app/Livewire/Syllabus/Wizard/SyllabusWizard.php`
  - `app/Livewire/Syllabus/Wizard/Steps/*`
- Views (examples)
  - `resources/views/livewire/programs/*`
  - `resources/views/livewire/syllabus/wizard/*`

Related docs:
- `MD/10_Syllabus_Wizard.md`
- `C:\csms\.amazonq\rules\16-Scripting-Security.md`

## Security Implementation

### Input Validation
- **Server-Side Validation**: All Livewire component methods should validate inputs using Laravel validation rules.
- **SecurityValidator**: Use `SecurityValidator::containsAnyInjection()` for free-text fields to detect injection attempts.
- **Client-Side Detection**: Implement JavaScript functions in Alpine.js to detect injection patterns and provide real-time feedback.
- **Block and Validate**: When injection is detected, block submission and require user to fix the input - never attempt to "clean" dangerous content.

### Authorization
- **Component-Level Authorization**: Livewire mount methods should verify user permissions before loading data.
- **Role-Based Access**: Use middleware on routes and check user roles in component methods.
- **Scope-Based Access**: Non-admin users should be restricted to their assigned scope (college/department/program).
- **Ownership Validation**: Verify users can only access records they own or are assigned to.

### Client-Side Security
- **Alpine.js Security**: Follow the scripting security guidelines in `16-Scripting-Security.md`.
- **XSS Prevention**: Use Alpine's `x-text` instead of `x-html` when rendering user content.
- **Event Validation**: Validate all event data from Alpine.js before processing in Livewire methods.
- **No JavaScript Injection**: Never execute untrusted JavaScript passed from client-side.

### State Management
- **Dirty Tracking**: Implement dirty tracking to prevent data loss when navigating away.
- **Save Before Navigate**: Use save-before-navigate patterns to auto-save unsaved changes.
- **Transaction Safety**: Wrap complex operations in DB transactions to ensure atomicity.

### Rate Limiting
- **Current Status**: Rate limiting is not currently implemented on Livewire component endpoints.
- **Recommended Enhancement**: Add rate limiting to frequently called Livewire methods to prevent automated abuse.

### Audit Logging
- **Action Logging**: Log all significant actions performed through Livewire components.
- **User Attribution**: Include authenticated user information in all audit logs.
- **Change Tracking**: Log record creation, updates, and deletions with context.

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
