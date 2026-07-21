# CSMS — Complete System Reference

Central Luzon State University — Course Syllabus Management System.
This document is the single source of truth for understanding the full system: what it does, who can do what, and critical rules to remember.

---

## What Is CSMS?

CSMS is CLSU's official platform for creating and reviewing course syllabi across all colleges and departments. It enforces a structured workflow: faculty create syllabi, chairs review them, deans approve them, and admins manage the entire structure.

---

## Why CAIS + Admin Approval (Critical Design Decisions)

### Why CAIS Login?

- The system integrates with the CLSU CAIS (LMS) API for primary authentication.
- Faculty can log in with their existing CLSU credentials.
- If CAIS responds, the user is authenticated via CAIS and a local user row is auto-created if needed.
- If CAIS is unavailable or rejects credentials, the system falls back to local password authentication.

### Why No OTP Email Verification?

- Registration is restricted to `@clsu.edu.ph` and `@clsu2.edu.ph` email addresses only.
- OTP email verification was previously used but removed — the system now sets `email_verified_at = now()` directly on registration.
- Admin approval remains the human gate that confirms the registrant is an actual faculty member (not a student).
- Only after admin approval does the account become `active` and receive the `Faculty` role.

---

## Roles and What Each Can Do

### Admin

Full system access. No restrictions.

- Manage all users (approve, reject, restore, disable, edit)
- Assign and remove roles (admin, dean, chair, faculty)
- Assign deans to colleges, chairs to departments, faculty to departments
- Manage academic structure (colleges, departments, programs)
- Manage academic calendars and events
- Manage college goals (all colleges)
- Manage department objectives (all departments)
- Manage PEOs and POs (all programs)
- Manage courses (all programs)
- View and access all syllabi (regardless of preparer)
- Delete courses (admin-only)
- View audit logs
- Cannot be assigned in organizational hierarchy selects (admin is excluded from dean/chair/faculty dropdowns)

### Dean

Scoped to their assigned college.

- View organizational hierarchy for their college (read-only — cannot assign/remove chairs or faculty; only admin can)
- Manage college goals for their assigned college only
- Cannot manage goals for other colleges
- Cannot manage department objectives, PEOs, POs, or courses directly
- Cannot create syllabi (no faculty route access unless also assigned faculty role)
- Sidebar shows: College Goals

### Chair

Scoped to their assigned department.

- View organizational hierarchy for their department (read-only — cannot assign/remove faculty; only admin can)
- Manage department objectives for their assigned department only
- Manage PEOs and POs for programs in their assigned department only
- Manage courses for programs in their assigned department only
- Delete courses for programs in their assigned department
- Cannot manage objectives, PEOs, POs, or courses for other departments
- Sidebar shows: Department Objectives, PEOs & POs, Courses

### Faculty

Scoped to their own syllabi.

- Create syllabi for any course (regardless of department assignment — any faculty can create for any course)
- Edit their own draft syllabi only
- Delete their own draft syllabi only
- View their own syllabi (draft + approved tabs)
- Cannot access approved syllabi of other faculty (unless admin)
- Cannot manage goals, objectives, PEOs, POs, or courses
- Sidebar shows: Syllabi

### OVPAA

Scoped to syllabus view access (read-only).

- View all syllabi across all programs/departments
- Preview and download syllabus PDFs and saved versions
- Cannot create, edit, or delete syllabi
- Sidebar shows: Syllabi (same as Faculty, but sees all)

### Notes on Role Combinations

- A user always has `Faculty` role — it is forced on every active account.
- Dean + Chair cannot be assigned simultaneously (blocked at both role assignment and hierarchy assignment level).
- When a user is assigned as dean, they automatically get a faculty assignment for that college.
- When a user is assigned as chair, they automatically get a faculty assignment for that department.
- Disabling or rejecting a user removes all their organizational assignments immediately.

---

## Authentication Flow

### Registration

1. User fills name, phone, office, CLSU email, password.
2. Email must end with `@clsu.edu.ph` or `@clsu2.edu.ph` — others are rejected.
3. System creates account with `status = pending`, `email_verified_at = now()`.
4. User is redirected to the waiting-approval page (no OTP step).

### Login (CAIS-first)

1. System first attempts CAIS API authentication.
2. If CAIS responds: user authenticated, local user created/updated from CAIS profile, redirected to syllabus index.
3. If CAIS unavailable: fall back to local password check.
4. If local auth fails → error toast on auth page.
5. If account pending → info toast, redirected to waiting-approval page.
6. If account rejected → error toast on auth page.
7. If account disabled → error toast on auth page.
8. If account active → redirected to syllabus index.

### Admin Approval

- Admin sees all users in User Management with account status badges.
- Admin approves → account becomes `active`, `faculty` role attached, email sent.
- Admin rejects → account becomes `rejected`, all assignments deleted, email sent.
- Admin disables → account becomes `disabled`, all assignments deleted, email sent.
- Admin can restore rejected accounts back to `pending`.

---

## Organizational Hierarchy

```
College
  └── Dean (1 per college, assigned by admin)
  └── Department
        └── Chair (1 per department, assigned by admin)
        └── Faculty (multiple per department, assigned by admin)
```

- Only admin can assign/remove deans, chairs, and faculty.
- Dean and Chair views are read-only (they see their hierarchy but cannot modify it).
- Admin users are excluded from all assignment dropdowns.
- Dean cannot be chair and vice versa (mutual exclusivity enforced at both role and assignment level).

---

## Academic Structure Hierarchy

```
College → Department → Program → Course → Syllabus
```

- All structure management (colleges, departments, programs) is admin-only.
- Deleting a college cascades: assignments → objectives → departments → goals → college.
- Deleting a department cascades: assignments → objectives → programs (detach) → department.
- Deleting a program cascades: PO mappings → PEOs → POs → program.
- Deleting a course cascades: PO mappings → syllabi (full cascade) → course.
- Structure deletes are blocked if courses exist under the scope being deleted.

---

## Academic Calendar

- One academic year = two `academic_calendars` rows (1st semester + 2nd semester).
- Events per semester: `holiday`, `exam`, `break`, `non_teaching`, `other`.
- `exam` and `non_teaching` events lock the corresponding syllabus week (no coverage can be entered).
- `break` events cause the week to be skipped entirely during week generation (no week row created).
- Deleting a calendar is blocked if any syllabus is linked to it.
- Changing calendar dates when syllabi have generated weeks shows a stale-weeks warning.

---

## Goals and Objectives

| Item | Managed By | Scope |
|---|---|---|
| College Goals | Admin, Dean | Dean's assigned college only |
| Department Objectives | Admin, Chair | Chair's assigned department only |

- Codes are auto-generated (a, b, c… aa, ab…) and resequenced on delete.
- Non-admin users only see their assigned college/department in the selector.

---

## PEOs, POs, and Courses

| Item | Managed By | Scope |
|---|---|---|
| PEOs | Admin, Chair | Chair's assigned department programs only |
| POs | Admin, Chair | Chair's assigned department programs only |
| Courses | Admin, Chair | Chair's assigned department programs only |
| Course Delete | Admin, Chair | Admin always; Chair only for their department's programs |

- ProgramSelector in PEOs/POs/Courses pages is scoped to the user's assigned department.
- ProgramSelector in Syllabus pages is NOT scoped — any faculty can select any program/course.
- PO deletion is blocked if the PO is mapped in any existing syllabus course outcomes.

---

## Syllabus Workflow

```
Draft → Under Review → Approved
```

- Only the preparer can edit or delete their own syllabus.
- Only draft syllabi can be deleted.
- Admin can view any syllabus regardless of preparer.
- Any faculty can create a syllabus for any course (no department restriction).
- A course must have PO mappings before a syllabus can be created for it.
- One syllabus per course per faculty user (duplicate creation redirects to existing).

### Syllabus Wizard Steps

1. **Academic Calendar** — Select semester calendar.
2. **Course Components** — LEC/LAB instructor details, schedule, hours.
3. **Course Outcomes** — Batch save (Add CO + Save All). Delete is individual.
4. **Weekly Coverage** — Auto-generated weeks. Exam/non-teaching weeks are locked. Week 1 is always MVGO.
5. **Course Evaluation** — Weights per assessment task. LEC = 67%, LAB = 33% (if applicable).
6. **Review** — Signatories, reviewers, preview links, submit for review.

### Submit Gate (All Must Pass)

- Academic calendar selected.
- Course components complete.
- At least one non-blank course outcome saved.
- At least one syllabus week exists.
- Course evaluation complete (all assessable weeks have weights).

---

## Course Outcomes — Why Batch Save (Not Individual)

COs use "Save All" (same as PEOs/POs) because:
- COs are a flat ordered list with no inter-row dependencies.
- Users typically add several at once before saving — batch reduces round trips.
- Consistent with PEOs/POs pattern, reducing cognitive load.
- Delete remains individual because it has immediate DB consequences (re-sequencing codes).

---

## Weekly Coverage Rules

- Weeks are generated from the academic calendar start/end date in 7-day blocks.
- `break` events skip the week entirely (no week row, week numbers stay sequential).
- `exam` events lock the week as "Exam Week" — auto-fills assessment task.
- `non_teaching` events lock the week as "Non-Teaching Week" — auto-fills assessment task.
- Week 1 is always MVGO (Mission-Vision-Goals-Objectives) — CO selector replaced by MVGO badge.
- Regenerating weeks deletes all existing week content (confirmation required).
- Auto-save triggers when collapsing a week or clicking Save All.

---

## Storage and Files

- Syllabus PDF snapshots stored in `storage/app/private/`.
- Deleting a syllabus or course removes all associated disk files.
- Run `php artisan storage:link` to expose public storage URLs.

---

## Key Technical Notes

- Session driver: `database` (requires `sessions` table to exist on hosted server).
- Cache driver: `database` (requires `cache` table).
- Queue driver: `database` (requires `jobs` table).
- On Hostinger: if session/cache tables are missing, switch to `SESSION_DRIVER=file` and `CACHE_STORE=file` temporarily.
- `APP_URL` must match the actual deployed domain (not `http://localhost`) for CSRF and redirects to work.
- `AuditLog::record()` is wrapped in try/catch — it never crashes the app on failure.
- `OtpService::issueForUser()` returns `bool` — mail failure is logged but does not crash the password-change flow.
- All role checks use `$user->hasRole(string $role)` — requires `/** @var \App\Models\User $user */` docblock for Intelephense.
- Unauthenticated users are redirected to `route('auth.show')` (not `/login`) via `$middleware->redirectGuestsTo()` in `bootstrap/app.php`.

---

## Color Scheme

- Primary green: `#002a0c` → `#004d16` (sidebar gradient)
- Accent gold: `#ffd700` (brand icon)
- LEC accent: emerald
- LAB accent: blue
- Locked weeks: rose
- Exam weeks: amber
- Warnings: amber
- Errors: rose/red
- Success: emerald

---

## Route Access Summary

| Route Group | Middleware | Who |
|---|---|---|
| `/account-approval`, `/academic-structure`, `/academic-calendars`, `/organizational/colleges`, `/audit-logs` | `role:admin` | Admin only |
| `/organizational/hierarchy`, `/organizational/college/*/departments`, assign/remove chair/faculty | `role:admin,dean,chair` | Admin, Dean, Chair |
| `/college/goals` | `role:admin,dean` | Admin, Dean |
| `/department/objectives`, `/programs`, `/courses` | `role:admin,chair` | Admin, Chair |
| `/syllabus` | `role:admin,faculty,ovpaa` | Admin (full), Faculty (own), OVPAA (view-only) |
| `/profile` | `auth` | Any authenticated user |

---

## Files Quick Reference

| What | Where |
|---|---|
| Auth + CAIS | `app/Http/Controllers/AuthController.php`, `app/Services/CaisApiService.php` |
| Account approval | `app/Http/Controllers/AccountApprovalController.php`, `app/Services/AccountApprovalService.php` |
| Org hierarchy | `app/Services/OrganizationalHierarchy/` |
| Goals / Objectives | `app/Http/Controllers/GoalController.php`, `ObjectiveController.php` |
| PEOs / POs | `app/Http/Controllers/ProgramController.php`, `app/Livewire/Programs/ManagePeos.php`, `ManagePos.php` |
| Courses | `app/Http/Controllers/CourseController.php` |
| Syllabus wizard | `app/Livewire/Syllabus/SyllabusWizard.php`, `Steps/` |
| Syllabus CRUD | `app/Http/Controllers/SyllabusController.php` |
| Academic calendar | `app/Http/Controllers/AcademicCalendarController.php` |
| Academic structure | `app/Http/Controllers/AcademicStructureController.php` |
| OTP service | `app/Services/OtpService.php` |
| CAIS API | `app/Services/CaisApiService.php` |
| Audit log | `app/Models/AuditLog.php` |
| Role middleware | `app/Http/Middleware/RoleMiddleware.php` |
| Routes | `routes/web.php` |
