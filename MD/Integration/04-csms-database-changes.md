# 04 — CSMS Database Changes After Integration

These are the schema changes CSMS needs to make to support API integration. No code changes yet — this is the migration plan only.

---

## Tables to Drop

These tables become unnecessary because CAIS is the source of truth and CSMS will fetch them via API.

| Table | Reason |
|---|---|
| `colleges` | Fully duplicated from CAIS. All references will use `cais_college_id`. |
| `departments` | Fully duplicated from CAIS. All references will use `cais_department_id`. |

**Cascade impact of dropping `colleges`:**
- `departments.college_id` FK → removed (column replaced with `cais_college_id`)
- `college_goals.college_id` FK → removed (column replaced with `cais_college_id`)
- `user_assignments.college_id` FK → removed (column replaced with `cais_college_id`)

**Cascade impact of dropping `departments`:**
- `department_objectives.department_id` FK → removed (column replaced with `cais_department_id`)
- `program_departments.department_id` FK → removed (column replaced with `cais_department_id`)
- `user_assignments.department_id` FK → removed (column replaced with `cais_department_id`)

---

## Columns to Add

### `users` table — add CAIS linkage

```
cais_user_id  bigint unsigned  nullable  unique
```

This is the bridge between CSMS local auth and CAIS identity. Required for fetching faculty profile, teaching loads, and schedule data from CAIS.

---

### `academic_calendars` table — add CAIS semester linkage

```
cais_semester_id  bigint unsigned  nullable  index
```

Links a CSMS academic calendar to the official CAIS semester for cross-system traceability and display. Not a hard FK — CAIS ID only.

---

### `college_goals` table — replace local FK with CAIS ID

Remove:
```
college_id  bigint unsigned  FK → colleges.id
```

Add:
```
cais_college_id  bigint unsigned  not null  index
```

---

### `department_objectives` table — replace local FK with CAIS ID

Remove:
```
department_id  bigint unsigned  FK → departments.id
```

Add:
```
cais_department_id  bigint unsigned  not null  index
```

---

### `program_departments` table — replace local FK with CAIS ID

Remove:
```
department_id  bigint unsigned  FK → departments.id
```

Add:
```
cais_department_id  bigint unsigned  not null  index
```

---

### `user_assignments` table — replace local FKs with CAIS IDs

Remove:
```
college_id     bigint unsigned  nullable  FK → colleges.id
department_id  bigint unsigned  nullable  FK → departments.id
```

Add:
```
cais_college_id     bigint unsigned  nullable  index
cais_department_id  bigint unsigned  nullable  index
```

The composite unique constraint must be updated to use the new column names:
```
unique(user_id, cais_college_id, cais_department_id, context)
```

---

## Columns to Remove from `users`

After integration, CSMS `users` should not store profile data that CAIS owns. These will be fetched via API and cached.

| Column | Reason to Remove |
|---|---|
| `name` | CAIS stores `first_name`, `middle_name`, `last_name` separately. CSMS should assemble display name from API. |
| `phone_number` | Duplicates CAIS `users.contact_no`. |
| `office` | Not in CAIS schema currently — **flag for CAIS team**: does CAIS store office/room assignment? If not, keep this column in CSMS. |

> **Note on `name`:** This is a breaking change. Every place in CSMS that reads `$user->name` must switch to a computed accessor that calls the CAIS API or reads from cache. Evaluate carefully before removing — consider keeping `name` as a cached/denormalized column with a `cais_synced_at` timestamp instead of removing it entirely.

---

## Columns to Keep (Despite Overlap)

| Column | Table | Reason to Keep |
|---|---|---|
| `academic_year`, `semester` | `academic_calendars` | CSMS-specific calendar structure. Not replaced by CAIS semester. |
| `instructor_name` (removed in migration 2026_07_07) | `course_components` | Already replaced by `user_id` FK — correct direction. |
| `passing_mark`, `lec_class_hours`, `lab_class_hours` | `courses` | Curriculum-specific data. CAIS has no equivalent. Keep. |

---

## Normalization Improvements

### 1. `syllabi` — missing unique constraint after migration removal

Migration `2026_06_29` dropped the `unique(['course_id', 'academic_calendar_id'])` constraint. The README says "one syllabus per course per faculty user." The current schema has no unique constraint enforcing this. 

**Recommendation:** Add `unique(['course_id', 'prepared_by'])` or `unique(['course_id', 'academic_calendar_id', 'prepared_by'])` depending on the intended business rule.

### 2. `course_components` — `class_hours` column is a string

`class_hours` is stored as a string (e.g. "3 hours") but is used for calculation logic. After `lec_class_hours` / `lab_class_hours` were added to `courses`, the `class_hours` on `course_components` is redundant.

**Recommendation:** Evaluate whether `course_components.class_hours` can be dropped and derived from `courses.lec_class_hours` / `lab_class_hours`.

### 3. `complete_syllabi` — stores `academic_year` and `semester` as strings

These duplicate data already on `syllabi → academic_calendars`. 

**Recommendation:** These are intentional snapshot columns (PDF generation context). Keep them but document that they are denormalized snapshots, not live references.

### 4. `program_departments` — `role` enum (primary/supporting)

This is a good design. No change needed.

---

## Performance Improvements

### 1. Add index on `syllabi.prepared_by`

Currently no index. Every faculty's syllabus list query hits a full scan.

```sql
ALTER TABLE syllabi ADD INDEX idx_syllabi_prepared_by (prepared_by);
```

### 2. Add index on `user_assignments.context`

Queries like "find all chairs" or "find all deans" scan the full table without this.

```sql
ALTER TABLE user_assignments ADD INDEX idx_user_assignments_context (context);
```

### 3. Add index on `course_curriculum_maps.program_outcome_id`

The PO deletion block check queries this column. No index currently.

```sql
ALTER TABLE course_curriculum_maps ADD INDEX idx_ccm_po_id (program_outcome_id);
```

### 4. Add index on `week_contents.course_outcome_id`

CO deletion re-sequencing queries this. No index currently.

```sql
ALTER TABLE week_contents ADD INDEX idx_wc_co_id (course_outcome_id);
```

### 5. `audit_logs` — `reference_id` index exists but `module + reference_id` composite would be more useful

```sql
ALTER TABLE audit_logs ADD INDEX idx_audit_module_ref (module, reference_id);
```
