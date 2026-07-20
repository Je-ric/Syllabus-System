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

## Where old content went (dedup map)

- `AGENT_PERSONA.md` → 01, references updated to match this file set
- `PROJECT_RULES.md` → split: principles/standards → 02, Database section → 09
- `REFRACTORING_RULES.md` (Rule of Three) → merged into 02
- `CODE_REVIEW.md` → 03 (unchanged, this is the canonical checklist)
- `naming-conventions.md` → 04 (kept as canonical; it was a superset of the table in `development-notes.md`)
- `FRONTEND_UI.md` → 05, extended with the app-specific behaviors that were only in `development-notes.md`
- `security-guide.md` → 06 (kept as canonical; it was a superset of the security section in `development-notes.md`)
- `api-standards.md` → 07 (unchanged)
- `system-flow-template.md` + `development-notes.md` §1–3 (Foundation, Planning, Core Features) + audit-log requirement from §7 → 08
- `development-notes.md` §9 (Database Notes) + `PROJECT_RULES.md` Database section → merged into 09
- `debugging-guide.md` → 10 (kept as canonical; it was a superset of `development-notes.md` §10)
- `common-errors.md` → 11 (unchanged — keep appending new entries at the top)
- `development-notes.md` §11 (Deployment Notes) → 12

`development-notes.md`'s other §7 items (service classes, hashed passwords,
server-side validation, proper HTTP methods) were exact duplicates of rules
already stated in 02, 06, and 07 — they were not copied again.
