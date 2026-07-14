# 01 — Database Analysis

## CAIS (clsuedu_lms) Tables

| Table | Purpose |
|---|---|
| `users` | Faculty, students, staff. Split name (first/middle/last), avatar, 2FA, contact_no, external_id for sync. |
| `colleges` | Institutional colleges. Has `external_id`, `synced_at` — already synced from an upstream source. |
| `departments` | Departments under colleges. Has `external_id`, `external_college_id`, `synced_at`. |
| `courses` | Subject offerings (not curriculum courses — these are class-level subjects). Has `external_id`, `external_college_id`. |
| `semesters` | Academic semesters with year, number, status, grades_deadline. Has `external_id`. |
| `class_schedules` | A specific class section for a subject in a semester. Holds time, room, section, units, department. |
| `teaching_loads` | Links a faculty user to a class_schedule for a semester. This is the faculty teaching assignment. |
| `enrollments` | Links a student user to a course in a semester with section. |
| `registrations` | Links a student to a specific class_schedule via an enrollment. Tracks RA status, forced drop. |
| `assessments` | Quizzes, assignments, exams created per class_schedule. |
| `question_types` | Lookup table for question types (MCQ, essay, etc.). |
| `assessment_questions` | Questions belonging to an assessment. |
| `assessment_question_options` | Answer options for a question. |
| `assessment_attempts` | A student's attempt at an assessment. |
| `assessment_attempt_answers` | Per-question answers within an attempt. |
| `assessment_attempt_answer_options` | Selected options within a multi-select answer. |
| `permissions` | Spatie permission package — permissions list. |
| `roles` | Spatie permission package — roles list. |
| `model_has_permissions` | Spatie pivot — user/model to permission. |
| `model_has_roles` | Spatie pivot — user/model to role. |
| `role_has_permissions` | Spatie pivot — role to permission. |
| `telescope_entries` | Laravel Telescope debug/monitoring entries. |
| `telescope_entries_tags` | Tags for telescope entries. |
| `telescope_monitoring` | Telescope monitored tags. |
| `notifications` | Laravel notifications table. |
| `activity_log` | Spatie activity log. |
| `media` | Spatie media library. |

---

## CSMS Tables

| Table | Purpose |
|---|---|
| `users` | CSMS-local faculty accounts. Stores name (single field), email, password, account_status, phone_number, office. |
| `roles` | Simple custom roles: admin, dean, chair, faculty. |
| `user_roles` | Pivot: user ↔ role. |
| `user_assignments` | Assigns users to college/department with context (dean, chair, faculty). |
| `user_otps` | OTP records for email verification and password reset. |
| `user_consultation_hours` | Per-user consultation schedule (day + time slots). |
| `colleges` | Local copy of colleges (name only). |
| `departments` | Local copy of departments (name + college_id). |
| `programs` | Academic programs (degree programs). Not in CAIS. |
| `program_departments` | Links programs to departments with role (primary/supporting). |
| `program_eos` | Program Educational Objectives (PEOs) per program. |
| `program_outcomes` | Program Outcomes (POs) per program. |
| `program_outcome_peo` | Mapping of POs to PEOs. |
| `courses` | Curriculum courses (course_code, title, credit_units, year_level, semester, prerequisites). |
| `course_curriculum_maps` | Maps a course to a PO with IED level (Introduced/Emphasized/Demonstrated). |
| `academic_calendars` | Semester calendar with start/end dates. |
| `academic_calendar_events` | Events within a calendar (holiday, exam, break, non_teaching, other). |
| `syllabi` | Core syllabus record linking course + calendar + preparer. |
| `syllabus_revisions` | Revision history per syllabus. |
| `course_components` | LEC/LAB component of a syllabus. Links to user (instructor). |
| `course_component_schedules` | Day/time schedule rows for a course component. |
| `course_outcomes` | Course Outcomes (COs) per syllabus. |
| `syllabus_weeks` | Auto-generated weekly rows per syllabus. |
| `week_contents` | LEC/LAB content per week (topics, outcomes, TLA, assessment task). |
| `syllabus_evaluation_items` | Evaluation weights per week_content row. |
| `syllabus_references` | Reference citations per syllabus/week. |
| `syllabus_materials` | Resource links per syllabus/week. |
| `syllabus_reviewers` | Users assigned to review a syllabus. |
| `complete_syllabi` | Finalized PDF snapshots of approved syllabi. |
| `audit_logs` | CSMS-local audit trail. |
| `sessions` | Laravel DB sessions. |
| `cache` | Laravel DB cache. |
| `jobs` | Laravel DB queue jobs. |

---

## Key Structural Observations

### CAIS `courses` ≠ CSMS `courses`
These are **completely different entities** despite sharing the name:

| | CAIS `courses` | CSMS `courses` |
|---|---|---|
| What it is | A subject/class offering (LMS course room) | A curriculum course definition |
| Has | name, type, status, college_id, external_id | course_code, title, credit_units, year_level, semester, prerequisites |
| Linked to | class_schedules, enrollments | programs, syllabi, PO maps |

This is the single most important naming collision in the integration. They must never be conflated.

### CAIS already syncs from an upstream source
CAIS `colleges`, `departments`, `courses`, `semesters`, `users` all have `external_id` + `synced_at` columns, meaning CAIS itself is already a consumer of some upstream institutional system. CSMS will be a second-level consumer of CAIS data.

### CSMS `users` is a standalone auth system
CSMS has its own registration, OTP verification, and admin approval flow. It does not currently store a `cais_user_id` / `external_id`. This is the primary linkage gap that must be resolved.

### CSMS has no concept of semesters from CAIS
CSMS manages its own `academic_calendars` (start/end dates, events). CAIS has `semesters` (year, number, status, grades_deadline). These overlap in purpose but are not the same structure.

### Programs exist only in CSMS
CAIS has no `programs` table. Programs (degree programs like BSCS, BSEE) are purely a CSMS concept used to organize curriculum courses and PEOs/POs.

### Teaching assignments exist only in CAIS
CAIS `teaching_loads` links faculty to class sections. CSMS has no equivalent — it only knows which user prepared a syllabus. After integration, CSMS should be able to read teaching loads from CAIS to pre-fill syllabus instructor details.
