# Project Principles & Coding Standards

# Software Design Principles

## KISS (Keep It Simple, Stupid)
- Always implement the simplest solution that correctly solves today's problem.
- Prefer straightforward code over clever or highly abstract solutions.
- Only introduce additional layers (services, repositories, interfaces, factories, strategies, etc.) when they solve a real, existing problem.
- Avoid premature optimization and speculative architecture.
- Readability is more valuable than cleverness.

## YAGNI (You Aren't Gonna Need It)
- Build only for current requirements.
- Do not add extension points, interfaces, or configuration solely because they "might be useful later."
- Introduce abstractions only after a second legitimate use case exists.

## DRY (Don't Repeat Yourself)
- Eliminate duplicated business logic.
- Extract repeated validation, calculations, transformations, and queries into reusable methods or services.
- Do NOT extract code simply because two snippets look similar — only when they represent the same knowledge or business rule.
- Prefer small reusable methods over copy-paste.

## Rule of Three (when to actually refactor)
- First occurrence: write the code.
- Second occurrence: notice the similarity.
- Third occurrence: refactor into a reusable method, service, or component.
- Do not extract abstractions after only one occurrence.

## SOLID
- Apply SOLID where it improves maintainability.
- Do not force every class behind an interface.
- Favor composition over inheritance.
- Single Responsibility Principle takes priority over unnecessary abstractions.

---

# Coding Standards

## Functions & Methods

### Create a new method when:
- A method exceeds ~50 lines.
- A logical block has a single responsibility.
- Logic is repeated.
- The block can be named clearly.
- The extracted method improves readability.
- The extracted logic can be independently tested.

### Do NOT create a new method when:
- It only wraps one line with no added meaning.
- It would require jumping through many tiny methods just to understand one workflow.
- The extraction makes the code harder to follow.

### Naming
- Method names should describe exactly what they do.
- Use verbs.
- Avoid vague names like: `process()`, `handle()`, `data()`, `execute()`, `helper()`, `utils()`.
- Prefer names like: `saveDraft()`, `synchronizeTeachingLoads()`, `calculateConsultationHours()`, `validateCourseOutcomes()`.

(See `04-naming-conventions.md` for casing/format rules across the whole stack.)

---

## Classes

Create a new class when:
- It has its own responsibility.
- Multiple components need the same behavior.
- Business logic becomes too large for one component.
- It represents a real domain concept.

Avoid creating classes that only forward calls.

Bad:
```
Controller → Manager → Service → Repository → Helper → Model
```

Good:
```
Controller / Livewire → Service (optional) → Model
```

---

## Services

Create a service when:
- Logic is shared by multiple components.
- The logic represents business rules.
- External APIs are involved.
- Transactions span multiple models.
- The logic should be unit tested independently.

Do NOT create a service for simple CRUD.

---

## Interfaces

Create interfaces ONLY when:
- Multiple implementations exist today.
- Different implementations are actively expected.
- Dependency inversion provides immediate value.

Do NOT create interfaces "just because SOLID."

Bad: `UserServiceInterface` → `UserService`
Good: `UserService`

---

## Helpers

Use helpers only for: pure utility functions, formatting, parsing, date conversions, string transformations.

Helpers must:
- Have no side effects.
- Never perform database writes.
- Never contain business logic.

---

## Livewire

Business logic belongs inside Livewire components or dedicated services.

Blade templates should only:
- Display data.
- Loop.
- Render conditionals.
- Trigger actions.

Never:
- Query the database.
- Mutate data.
- Perform calculations that belong in PHP.

---

## Frontend State Management (Alpine.js / reactive components)

- Prefer local component state (`x-data`) by default; reach for a global store only when multiple independent components genuinely need to read or react to the same state.
- When several components must all complete an action before a parent step can proceed (e.g. "save all before continuing"), use an explicit register/run pattern (each component registers a handler, the parent runs them via `Promise.all()`) rather than ad-hoc event chains — ad-hoc chains are where race conditions creep in.
- Watch for self-triggered update loops: an auto-save that listens for the same event it fires after saving will echo forever. If a component both listens for and emits the same signal, break the loop with a guard flag or restructure to a one-directional (deferred push) pattern.
- Debounce or defer rapid-fire saves (typing, drag, autosave) rather than firing a request per keystroke/event.

---

## Error Handling

Never swallow exceptions.

Every catch block must:
- Handle it,
- Log it,
- Or rethrow it with context.

Never leave an empty catch block.

---

## Performance

Prefer solving performance issues with, in order:
1. Better queries.
2. Eager loading.
3. Caching.
4. Computed properties.
5. Batch operations.

Only optimize after identifying a real bottleneck.

---

## Readability

Optimize for the next developer. Good code should read almost like English.

Prefer:
```php
if ($syllabus->isArchived()) {
    return;
}
```
Instead of:
```php
if (!$syllabus->isArchived() == false) {
    ...
}
```

---

## Comments

Write comments explaining "why," not "what."

Bad: `// Increment counter` \ `$count++;`
Good: `// Reserve slot to prevent concurrent submissions.` \ `$count++;`

Remove commented-out code. Git remembers history.

---

## Code Review Mindset

Before submitting code, ask:
- Is this the simplest solution? (KISS)
- Am I solving a real requirement? (YAGNI)
- Am I duplicating knowledge? (DRY)
- Does each class have one responsibility? (SRP)
- Can another developer understand this in five minutes?
- Can this fail safely?
- Is the code easy to modify?
- Is the abstraction justified today?

See `03-code-review-checklist.md` for the full pre-ship checklist, and `09-database-guide.md` for schema-level rules.