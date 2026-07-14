# 02 — Overlap Analysis & Source of Truth

## Duplicated Data Between CAIS and CSMS

| Data | CAIS Table | CSMS Table | Verdict |
|---|---|---|---|
| Colleges | `colleges` | `colleges` | **Duplicate** — CAIS is source of truth |
| Departments | `departments` | `departments` | **Duplicate** — CAIS is source of truth |
| Users (faculty identity) | `users` | `users` | **Partial duplicate** — CSMS needs its own auth, but profile data duplicates CAIS |
| Semester / Academic Year | `semesters` | `academic_calendars` | **Overlap** — different structure, different purpose (see below) |
| Subject/Course name | `courses` (LMS subject) | `courses` (curriculum course) | **Not a duplicate** — different entities, same name |

---

## Source of Truth Decisions

### CAIS owns — CSMS must not duplicate

| Entity | CAIS Table | Recommendation for CSMS |
|---|---|---|
| College list | `colleges` | Drop CSMS `colleges` table. Store only `cais_college_id` where needed. Fetch via API. |
| Department list | `departments` | Drop CSMS `departments` table. Store only `cais_department_id` where needed. Fetch via API. |
| Faculty identity (name, email, contact) | `users` | Remove duplicated profile fields from CSMS `users`. Keep only auth fields + `cais_user_id`. |
| Teaching assignments | `teaching_loads` | Never store in CSMS. Fetch via API when building syllabus course components. |
| Class schedules | `class_schedules` | Never store in CSMS. Fetch via API for schedule pre-fill. |
| Semester metadata | `semesters` | Fetch via API for display. CSMS `academic_calendars` keeps its own start/end dates and events. |

### CSMS owns — CAIS has no equivalent

| Entity | CSMS Table | Notes |
|---|---|---|
| Programs (degree programs) | `programs`, `program_departments` | Purely CSMS. No CAIS equivalent. |
| PEOs | `program_eos` | Purely CSMS. |
| POs | `program_outcomes`, `program_outcome_peo` | Purely CSMS. |
| Curriculum course definitions | `courses` | Purely CSMS. Different from CAIS `courses`. |
| Course-PO curriculum maps | `course_curriculum_maps` | Purely CSMS. |
| Academic calendar events | `academic_calendar_events` | Purely CSMS. CAIS `semesters` has no event-level data. |
| Syllabi and all child tables | `syllabi` + all syllabus_* tables | Purely CSMS. Core domain. |
| College goals | `college_goals` | Purely CSMS. |
| Department objectives | `department_objectives` | Purely CSMS. |
| CSMS roles (admin/dean/chair/faculty) | `roles`, `user_roles` | Purely CSMS. CAIS uses Spatie which is a different role system. |
| User assignments (dean/chair/faculty scope) | `user_assignments` | Purely CSMS. |
| Audit logs | `audit_logs` | Purely CSMS. |
| OTPs | `user_otps` | Purely CSMS auth flow. |
| Consultation hours | `user_consultation_hours` | Purely CSMS. |

---

## The Semester vs Academic Calendar Problem

These two serve overlapping but distinct purposes:

| | CAIS `semesters` | CSMS `academic_calendars` |
|---|---|---|
| Purpose | Tracks the official semester record (grades deadline, status) | Defines the teaching calendar (start/end dates, events) |
| Has dates | `grades_deadline` only | `start_date`, `end_date` (full range) |
| Has events | No | Yes (holidays, exams, breaks) |
| Drives | Enrollment, teaching loads, grades | Syllabus week generation |

**Recommendation:** CSMS `academic_calendars` stays local because it drives week generation logic that CAIS has no concept of. However, CSMS should store a `cais_semester_id` on `academic_calendars` to link the two for display and filtering purposes.

---

## Foreign Keys That Reference Duplicated Data

These are the CSMS foreign keys that currently point to locally-duplicated tables that should become CAIS-owned:

### `colleges` references (currently local FK → should become CAIS ID)

| CSMS Table | Column | Current FK | After Integration |
|---|---|---|---|
| `departments` | `college_id` | `→ colleges.id` | Drop FK. Store `cais_college_id bigint` instead. |
| `college_goals` | `college_id` | `→ colleges.id` | Drop FK. Store `cais_college_id bigint` instead. |
| `user_assignments` | `college_id` | `→ colleges.id` | Drop FK. Store `cais_college_id bigint` instead. |

### `departments` references (currently local FK → should become CAIS ID)

| CSMS Table | Column | Current FK | After Integration |
|---|---|---|---|
| `departments` | `college_id` | `→ colleges.id` | (covered above) |
| `department_objectives` | `department_id` | `→ departments.id` | Drop FK. Store `cais_department_id bigint` instead. |
| `program_departments` | `department_id` | `→ departments.id` | Drop FK. Store `cais_department_id bigint` instead. |
| `user_assignments` | `department_id` | `→ departments.id` | Drop FK. Store `cais_department_id bigint` instead. |

### `users` references (profile data duplicated)

| CSMS Table | Column | Impact |
|---|---|---|
| `users` | `name`, `phone_number`, `office` | These duplicate CAIS `users` profile fields. After integration, fetch from CAIS API. Keep only auth fields locally. |
| All syllabus tables | `prepared_by`, `concurred_by`, `approved_by`, `user_id` | These FKs stay — they point to CSMS local user IDs which are valid. But CSMS must store `cais_user_id` on its `users` table to resolve identity back to CAIS. |

---

## What to Cache vs Fetch Live vs Store Locally

| Data | Strategy | Reason |
|---|---|---|
| College list | **Cache** (long TTL, e.g. 24h) | Changes rarely. Used in dropdowns everywhere. |
| Department list | **Cache** (long TTL, e.g. 24h) | Changes rarely. Used in dropdowns and scoping. |
| Faculty profile (name, email, office) | **Cache per user** (medium TTL, e.g. 1h) | Needed on every syllabus render. Changes infrequently. |
| Teaching loads for a faculty | **Cache** (short TTL, e.g. 15min) | Used to pre-fill course components. Changes per semester. |
| Class schedules for a section | **Cache** (short TTL, e.g. 15min) | Used to pre-fill schedule in course components. |
| Semester list | **Cache** (medium TTL, e.g. 6h) | Used in calendar linking. Changes per semester. |
| Current active semester | **Cache** (short TTL, e.g. 30min) | Needed for defaulting calendar selection. |
| User identity verification | **Live fetch** | Must be authoritative at login time. |
