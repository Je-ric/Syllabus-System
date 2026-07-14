# 03 — Proposed API Contract (CAIS → CSMS)

All endpoints are served by CAIS. CSMS consumes them via Laravel HTTP Client with a Bearer token.

Base URL: `https://cais.clsu.edu.ph/api/v1` (placeholder — confirm with CAIS team)

Authentication header on every request:
```
Authorization: Bearer {token}
Accept: application/json
```

---

## AUTH-01 — Verify Faculty Token / Whoami

**Why CSMS needs it:** When a CSMS user logs in or registers, CSMS must confirm the person is a real CLSU faculty member in CAIS, not just someone with a `@clsu.edu.ph` email.

**Consumed by:** Registration flow, admin approval flow, login cross-check.

**Fetch strategy:** Live — called once at login/approval. Result cached per user for 1 hour.

```
GET /auth/me
Authorization: Bearer {user_token}
```

**Sample Response:**
```json
{
  "data": {
    "id": 42,
    "first_name": "Maria",
    "middle_name": "Santos",
    "last_name": "Dela Cruz",
    "extension_name": null,
    "email": "mdelacruz@clsu.edu.ph",
    "contact_no": "09171234567",
    "status": "active",
    "is_active": true
  }
}
```

**Error Responses:**
```json
{ "message": "Unauthenticated." }           // 401
{ "message": "Account is inactive." }       // 403
```

---

## AUTH-02 — Validate CLSU Email Exists in CAIS

**Why CSMS needs it:** During CSMS registration, before issuing OTP, confirm the email belongs to an actual CAIS user (faculty, not student).

**Consumed by:** `AuthController@register`

**Fetch strategy:** Live — called once per registration attempt.

```
POST /auth/validate-email
Body: { "email": "mdelacruz@clsu.edu.ph" }
```

**Sample Response:**
```json
{
  "data": {
    "exists": true,
    "is_faculty": true,
    "cais_user_id": 42
  }
}
```

**Error Responses:**
```json
{ "data": { "exists": false } }             // 200 — email not found
{ "data": { "exists": true, "is_faculty": false } }  // 200 — student account
{ "message": "Validation error." }          // 422
```

---

## COLLEGE-01 — List All Colleges

**Why CSMS needs it:** College dropdowns in user assignments, college goals scoping, and organizational hierarchy display.

**Consumed by:** Admin user assignment, college goals page, org hierarchy page.

**Fetch strategy:** Cache 24 hours. Bust on CSMS admin request.

**Pagination:** No — college count is small (< 20).

```
GET /colleges
```

**Sample Response:**
```json
{
  "data": [
    { "id": 1, "name": "College of Engineering" },
    { "id": 2, "name": "College of Science" }
  ]
}
```

**Error Responses:**
```json
{ "message": "Unauthenticated." }           // 401
```

---

## COLLEGE-02 — Get Single College

**Why CSMS needs it:** Display college name on goals page, org hierarchy, and syllabus headers.

**Consumed by:** College goals page, org hierarchy, syllabus PDF header.

**Fetch strategy:** Cache per college ID, 24 hours.

```
GET /colleges/{id}
```

**Sample Response:**
```json
{
  "data": {
    "id": 1,
    "name": "College of Engineering",
    "description": "..."
  }
}
```

**Error Responses:**
```json
{ "message": "Not found." }                 // 404
```

---

## DEPT-01 — List Departments (optionally by college)

**Why CSMS needs it:** Department dropdowns in user assignments, department objectives scoping, program-department linking.

**Consumed by:** Admin user assignment, department objectives page, program management.

**Fetch strategy:** Cache 24 hours. Filter by `college_id` when scoped.

**Filtering:** `?college_id=1`

```
GET /departments?college_id=1
```

**Sample Response:**
```json
{
  "data": [
    { "id": 10, "name": "Department of Computer Science", "college_id": 1 },
    { "id": 11, "name": "Department of Electronics Engineering", "college_id": 1 }
  ]
}
```

**Error Responses:**
```json
{ "message": "Unauthenticated." }           // 401
```

---

## DEPT-02 — Get Single Department

**Why CSMS needs it:** Display department name on objectives page, org hierarchy, syllabus headers.

**Consumed by:** Department objectives page, org hierarchy, syllabus PDF.

**Fetch strategy:** Cache per department ID, 24 hours.

```
GET /departments/{id}
```

**Sample Response:**
```json
{
  "data": {
    "id": 10,
    "name": "Department of Computer Science",
    "college_id": 1,
    "college": { "id": 1, "name": "College of Engineering" }
  }
}
```

---

## USER-01 — Get Faculty Profile

**Why CSMS needs it:** Display instructor name, email, office, contact on syllabus course components. CSMS `users` will no longer store these fields locally.

**Consumed by:** Syllabus wizard (course components step), syllabus PDF, user profile page.

**Fetch strategy:** Cache per `cais_user_id`, 1 hour.

```
GET /users/{cais_user_id}
```

**Sample Response:**
```json
{
  "data": {
    "id": 42,
    "first_name": "Maria",
    "middle_name": "Santos",
    "last_name": "Dela Cruz",
    "extension_name": null,
    "email": "mdelacruz@clsu.edu.ph",
    "contact_no": "09171234567",
    "status": "active"
  }
}
```

**Error Responses:**
```json
{ "message": "Not found." }                 // 404
```

---

## USER-02 — List Faculty by Department

**Why CSMS needs it:** Admin assigns faculty to departments in CSMS. The dropdown must show only CAIS-verified faculty belonging to that department.

**Consumed by:** Admin org hierarchy — assign faculty to department.

**Fetch strategy:** Cache per department, 30 minutes.

**Filtering:** `?department_id=10&role=faculty`

```
GET /users?department_id=10&role=faculty
```

**Sample Response:**
```json
{
  "data": [
    { "id": 42, "first_name": "Maria", "last_name": "Dela Cruz", "email": "mdelacruz@clsu.edu.ph" },
    { "id": 55, "first_name": "Jose", "last_name": "Reyes", "email": "jreyes@clsu.edu.ph" }
  ],
  "meta": { "total": 2 }
}
```

---

## SEMESTER-01 — List Semesters

**Why CSMS needs it:** When creating an academic calendar, CSMS should allow linking to the official CAIS semester for cross-system traceability.

**Consumed by:** Academic calendar creation/edit page.

**Fetch strategy:** Cache 6 hours.

**Filtering:** `?status=active`, `?year=2025-2026`

```
GET /semesters?status=active
```

**Sample Response:**
```json
{
  "data": [
    {
      "id": 8,
      "name": "1st Semester 2025-2026",
      "number": 1,
      "year": "2025-2026",
      "status": "active",
      "grades_deadline": "2026-01-15"
    }
  ]
}
```

---

## SEMESTER-02 — Get Active Semester

**Why CSMS needs it:** Default the academic calendar selector to the current semester when a faculty starts a new syllabus.

**Consumed by:** Syllabus wizard step 1 (academic calendar selection).

**Fetch strategy:** Cache 30 minutes.

```
GET /semesters/active
```

**Sample Response:**
```json
{
  "data": {
    "id": 8,
    "name": "1st Semester 2025-2026",
    "number": 1,
    "year": "2025-2026",
    "status": "active"
  }
}
```

**Error Responses:**
```json
{ "message": "No active semester found." }  // 404
```

---

## TEACHING-01 — Get Teaching Load for a Faculty

**Why CSMS needs it:** Pre-fill the course component (instructor details, schedule, class hours) in the syllabus wizard based on the faculty's actual teaching assignment in CAIS.

**Consumed by:** Syllabus wizard step 2 (course components). Also used to validate that the faculty is actually assigned to teach the course they are creating a syllabus for (optional enforcement).

**Fetch strategy:** Cache per `cais_user_id` + semester, 15 minutes.

**Parameters:** `cais_user_id` (path), `semester_id` (query, optional — defaults to active semester)

```
GET /users/{cais_user_id}/teaching-loads?semester_id=8
```

**Sample Response:**
```json
{
  "data": [
    {
      "id": 301,
      "semester": { "id": 8, "name": "1st Semester 2025-2026" },
      "schedule": {
        "id": 150,
        "subject_code": "CS 311",
        "subject_title": "Data Structures and Algorithms",
        "section": "CS3A",
        "units": 3.0,
        "class_type": "LEC",
        "time": "MWF 07:30-08:30",
        "room": "ICT 201",
        "department": { "id": 10, "name": "Department of Computer Science" }
      }
    }
  ]
}
```

**Error Responses:**
```json
{ "message": "Not found." }                 // 404 — user has no loads this semester
```

---

## TEACHING-02 — Get Single Teaching Load Detail

**Why CSMS needs it:** When a faculty selects a specific teaching load to base their syllabus on, fetch full details including schedule days/times for the course component schedule pre-fill.

**Consumed by:** Syllabus wizard step 2 — after faculty selects a teaching load.

**Fetch strategy:** Cache per teaching load ID, 15 minutes.

```
GET /teaching-loads/{id}
```

**Sample Response:**
```json
{
  "data": {
    "id": 301,
    "user": { "id": 42, "first_name": "Maria", "last_name": "Dela Cruz" },
    "semester": { "id": 8, "name": "1st Semester 2025-2026" },
    "schedule": {
      "id": 150,
      "subject_code": "CS 311",
      "subject_title": "Data Structures and Algorithms",
      "section": "CS3A",
      "units": 3.0,
      "class_type": "LEC",
      "lab_type": null,
      "time": "MWF 07:30-08:30",
      "room": "ICT 201",
      "atl": null,
      "weight": null
    }
  }
}
```

---

## SCHEDULE-01 — Get Class Schedule Detail

**Why CSMS needs it:** Fetch schedule details (time, room, section) to pre-fill `course_component_schedules` in the syllabus wizard.

**Consumed by:** Syllabus wizard step 2 (course components — schedule sub-step).

**Fetch strategy:** Cache per schedule ID, 15 minutes.

```
GET /class-schedules/{id}
```

**Sample Response:**
```json
{
  "data": {
    "id": 150,
    "subject_code": "CS 311",
    "subject_title": "Data Structures and Algorithms",
    "section": "CS3A",
    "units": 3.0,
    "class_type": "LEC",
    "time": "MWF 07:30-08:30",
    "room": "ICT 201",
    "semester": { "id": 8, "name": "1st Semester 2025-2026" },
    "department": { "id": 10, "name": "Department of Computer Science" }
  }
}
```

---

## Error Response Standard (All Endpoints)

CAIS should return consistent error shapes. CSMS will expect:

```json
// 401 Unauthenticated
{ "message": "Unauthenticated." }

// 403 Forbidden
{ "message": "This action is unauthorized." }

// 404 Not Found
{ "message": "Not found." }

// 422 Validation Error
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."]
  }
}

// 429 Rate Limited
{ "message": "Too many requests." }

// 500 Server Error
{ "message": "Server error." }
```

---

## Endpoints CSMS Does NOT Need from CAIS

| CAIS Data | Reason Not Needed |
|---|---|
| `assessments` and all assessment tables | CAIS LMS assessments are separate from CSMS syllabus evaluation. No overlap. |
| `enrollments`, `registrations` | CSMS does not manage student enrollment. |
| `permissions`, `roles` (Spatie) | CSMS has its own role system. |
| `telescope_entries` | Internal CAIS debugging only. |
| `activity_log`, `media` | CAIS-internal. CSMS has its own audit_logs. |
| `notifications` | CSMS manages its own notifications. |
