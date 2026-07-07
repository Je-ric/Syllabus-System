# Project Rules

## Tech Stack
- Laravel 12
- Livewire 3
- Alpine.js
- Tailwind CSS
- Vite
- MySQL

## Coding Standards
- Follow SOLID principles — but don't force patterns where a simple function/method is clearer.
- Keep methods under ~50 lines; if longer, extract single-responsibility helpers.
- Use descriptive, unambiguous names for variables, methods, and classes. No abbreviations that require context to decode.
- No magic numbers or strings — extract to constants, enums, or config.
- Apply DRY: no duplicated logic, queries, or blade markup blocks.
- Avoid deep nesting — use guard clauses, early returns, inverted conditions.
- No hidden side effects — a method's name should describe everything it does. If it saves, dispatches events, AND sends notifications, that belongs in its name or docblock.

## Explicitly Avoid
- Overengineering — don't add interfaces, abstractions, or design patterns without a concrete current need for the flexibility they provide.
- Unnecessary abstractions "for future flexibility" — YAGNI until there's a second real use case.
- Silent failures — every catch block should either handle, log, or rethrow with context.

## Livewire
- Business logic belongs in Livewire components, not Blade.
- Blade stays presentational: display logic and conditionals only, no data mutation.
- Validate before saving — always, no exceptions.
- Prefer computed properties over repeated inline queries/calculations.
- Batch/deferred operations (e.g. "Save All" patterns) are preferred over per-field autosave when the user naturally works on multiple fields in one session.

## Database
- Every relationship has a foreign key constraint.
- Never auto-delete data — no unguarded cascade deletes on tables with historical/audit significance. Prefer soft deletes for those.
- Schema changes go through migrations only — never manual DB edits.
- Normalize appropriately; flag denormalization explicitly when chosen for performance, with a comment explaining why.
- Add composite unique constraints wherever a "duplicate row" would be a data integrity error, not just a UX inconvenience.

## UI
- Tailwind only — no inline styles, no separate CSS files unless truly unavoidable.
- Mobile-first: build the small viewport first, then expand.
- Consistent spacing scale — don't mix arbitrary px values with Tailwind's spacing scale.
- Loading states everywhere an async action can take >200ms.

## Code Review Checklist
See `03-code-review-checklist.md` for the full checklist. At minimum, every review must check: security, performance, maintainability, readability, accessibility, edge cases.
