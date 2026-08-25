# AI Agent Guides — Index

This is the reorganized guide set for the coding agent. Content from the
original 12 files has been merged by topic and deduplicated — each fact now
lives in exactly one file. `development-notes.md` has been retired as a
document in its own right; its sections were folded into the topic files
below (see the map at the bottom).

| # | File | Covers |
|---|---|---|
| 01 | `01-agent-persona.md` | Who the agent is, how it should behave and push back |
| 02 | `02-project-principles-and-coding-standards.md` | KISS/YAGNI/DRY/SOLID, when to create methods/classes/services/interfaces/helpers, Livewire rules, error handling, Rule of Three |
| 03 | `03-code-review-checklist.md` | The checklist every piece of code is reviewed against before shipping |
| 04 | `04-naming-conventions.md` | Naming rules for variables, DB, Laravel classes, routes, frontend files |
| 05 | `05-frontend-ui-guidelines.md` | UI/UX stack, layout, forms, accessibility, and app-specific UI behaviors |
| 06 | `06-security-guide.md` | Output escaping, SQL injection, file uploads, auth, sensitive data, CSRF |
| 07 | `07-api-standards.md` | JSON response shape, HTTP codes/methods, route naming, validation rules |
| 08 | `08-system-planning-and-flow.md` | Foundational principles, planning checklist, core feature set, standard flows (login/CRUD/notifications/API), audit logging |
| 09 | `09-database-guide.md` | Schema, transactions, soft deletes, constraints, when to extract queries |
| 10 | `10-debugging-guide.md` | Step-by-step debugging process and tool reference |
| 11 | `11-common-errors-log.md` | Running log of specific errors hit and their fixes (add new entries here) |
| 12 | `12-deployment-checklist.md` | Pre-launch checklist |
| 13 | `13-condition-completeness-and-error-handling.md` | Method for making sure conditions are exhaustive and non-conflicting. Exhaustive condition coverage, conflicting conditions, all error possibilities, missing/null/empty data handling |
| 14 | `14-input-validation-guide.md` | What to validate, where, error response shape, frontend display rules, null/missing/wrong-type handling |
| 15 | `15-md-update.md` | — update documentation per module |
| 16 | `16-Scripting-Security.md` | Client-side injection prevention, JavaScript validation, Alpine.js/Livewire security, rate limiting, input sanitization vs validation, project-specific security patterns |
