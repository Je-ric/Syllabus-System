# 05 — Integration Readiness Report

---

## 1. Current Architecture

```
┌─────────────────────────────────────────────────────┐
│                      CAIS / LMS                     │
│  users, colleges, departments, semesters,           │
│  class_schedules, teaching_loads, enrollments,      │
│  registrations, assessments, ...                    │
│  Auth: Spatie Permissions + Sanctum tokens          │
└─────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│                       CSMS                          │
│  users (own auth), colleges (copy), departments     │
│  (copy), programs, courses, syllabi, ...            │
│  Auth: Custom OTP + Admin Approval                  │
└─────────────────────────────────────────────────────┘

Current state: Two fully independent systems.
No connection. Institutional data duplicated manually in CSMS.
```

---

## 2. Recommended Architecture

```
┌─────────────────────────────────────────────────────┐
│                      CAIS / LMS                     │
│  Source of truth: users, colleges, departments,     │
│  semesters, class_schedules, teaching_loads         │
│  Exposes: REST API v1 with Bearer token auth        │
└──────────────────────┬──────────────────────────────┘
                       │  HTTP (Bearer Token)
                       │  Laravel HTTP Client
                       ▼
┌─────────────────────────────────────────────────────┐
│                       CSMS                          │
│  Owns: programs, courses (curriculum), syllabi,     │
│  PEOs, POs, goals, objectives, academic calendars   │
│  Stores: cais_user_id, cais_college_id,             │
│          cais_department_id, cais_semester_id       │
│  Caches: college list, dept list, faculty profiles, │
│          teaching loads (Laravel Cache, DB driver)  │
│  Auth: Own OTP + Admin Approval                     │
│        + CAIS email validation at registration      │
└─────────────────────────────────────────────────────┘
```

**Key principle:** CSMS stores only CAIS IDs for external entities, never the full record. Display names and details are fetched via API and cached.

---

## 3. Required API Endpoints (Summary)

| ID | Endpoint | Method | Cache TTL |
|---|---|---|---|
| AUTH-01 | `/auth/me` | GET | 1 hour per user |
| AUTH-02 | `/auth/validate-email` | POST | No cache |
| COLLEGE-01 | `/colleges` | GET | 24 hours |
| COLLEGE-02 | `/colleges/{id}` | GET | 24 hours |
| DEPT-01 | `/departments?college_id=` | GET | 24 hours |
| DEPT-02 | `/departments/{id}` | GET | 24 hours |
| USER-01 | `/users/{cais_user_id}` | GET | 1 hour |
| USER-02 | `/users?department_id=&role=faculty` | GET | 30 minutes |
| SEMESTER-01 | `/semesters` | GET | 6 hours |
| SEMESTER-02 | `/semesters/active` | GET | 30 minutes |
| TEACHING-01 | `/users/{id}/teaching-loads?semester_id=` | GET | 15 minutes |
| TEACHING-02 | `/teaching-loads/{id}` | GET | 15 minutes |
| SCHEDULE-01 | `/class-schedules/{id}` | GET | 15 minutes |

All endpoints require `Authorization: Bearer {token}`.

---

## 4. Required Authentication Flow

### Option A — System-Level Token (Recommended for Phase 1)

CSMS holds a single long-lived CAIS API token (stored in `.env` as `CAIS_API_TOKEN`). All CSMS-to-CAIS requests use this token. CAIS validates it as a trusted service account.

```
CSMS → CAIS: GET /colleges
Headers: Authorization: Bearer {CAIS_API_TOKEN}
```

**Pros:** Simple. No per-user token management.  
**Cons:** All CSMS requests appear as one actor in CAIS logs. Less granular audit trail.

### Option B — Per-User Token (Recommended for Phase 2)

At CSMS login, CSMS exchanges the user's CLSU credentials (or a CSMS-issued token) for a CAIS user token. Subsequent CAIS API calls use the user's own token.

**Pros:** Full per-user audit trail in CAIS. Proper authorization scoping.  
**Cons:** Requires CAIS to expose a token exchange endpoint. More complex session management.

### Recommended Approach

Start with Option A (system token) for Phase 1 to unblock development. Plan Option B for Phase 2 once CAIS API is stable.

### Token Storage in CSMS

```env
CAIS_API_BASE_URL=https://cais.clsu.edu.ph/api/v1
CAIS_API_TOKEN=your-sanctum-token-here
CAIS_API_TIMEOUT=10
```

Create a `CaisApiService` class in `app/Services/CaisApiService.php` that wraps all HTTP calls, handles caching, and standardizes error handling.

---

## 5. Database Changes (Migration Order)

Execute in this order to avoid FK violations:

1. Add `cais_user_id` to CSMS `users`
2. Add `cais_semester_id` to `academic_calendars`
3. Drop FK `college_goals.college_id` → add `cais_college_id`
4. Drop FK `department_objectives.department_id` → add `cais_department_id`
5. Drop FK `program_departments.department_id` → add `cais_department_id`
6. Drop FKs `user_assignments.college_id` + `user_assignments.department_id` → add `cais_college_id` + `cais_department_id`, update unique constraint
7. Drop `departments` table (all FKs removed in steps 4–6)
8. Drop `colleges` table (all FKs removed in step 3)
9. Add performance indexes (see `04-csms-database-changes.md`)

---

## 6. Risks

| Risk | Severity | Mitigation |
|---|---|---|
| CAIS API does not exist yet | **Critical** | Confirm with CAIS team whether the API is built or needs to be built. All integration work is blocked until at least AUTH-01, COLLEGE-01, DEPT-01 are available. |
| CAIS `courses` ≠ CSMS `courses` naming collision | **High** | Document clearly in both teams. Never expose CAIS `courses` as "curriculum courses" in CSMS. Use "class offerings" or "LMS subjects" as the CAIS term. |
| CAIS has no `programs` table | **Medium** | Programs are CSMS-only. No risk of conflict, but CAIS team must understand that program-level data will never come from them. |
| CAIS `users` splits name into first/middle/last; CSMS uses single `name` | **Medium** | CSMS must assemble display name from CAIS API response. Update all `$user->name` references to use a computed accessor. |
| CAIS `office` field may not exist | **Medium** | CAIS `users` schema has no `office` column. CSMS currently stores `office` locally. Confirm with CAIS team — if they don't have it, keep `office` in CSMS `users`. |
| Cache staleness for college/department names | **Low** | 24-hour TTL is acceptable for rarely-changing data. Provide a manual cache-bust endpoint in CSMS admin panel. |
| CAIS API downtime | **Medium** | CSMS must degrade gracefully. If CAIS is unreachable, show cached data where available. Block only hard-dependency flows (registration email validation). |
| Token expiry / rotation | **Low** | Use a long-lived Sanctum token for the system account. Document rotation procedure. |
| `syllabi` missing unique constraint | **Medium** | See `04-csms-database-changes.md` §Normalization. Fix before integration to avoid duplicate syllabus bugs. |

---

## 7. Missing Information (Needs Confirmation from CAIS Team)

1. **Does CAIS have a REST API already?** If not, which endpoints can they build first?
2. **What is the base URL and versioning scheme?** (`/api/v1`? `/api`?)
3. **What authentication method does CAIS use for service-to-service calls?** Sanctum token? OAuth2? API key?
4. **Does CAIS `users` have an `office` or `room` field?** CSMS currently stores this locally.
5. **Does CAIS distinguish faculty from students in the `users` table?** The schema has `status` and `is_active` but no explicit `type` or `role` column visible in migrations. How does CSMS validate "is this person faculty, not a student"?
6. **Does CAIS `class_schedules.time` follow a parseable format?** Currently stored as a string (e.g. `"MWF 07:30-08:30"`). CSMS needs to parse this into day/time rows for `course_component_schedules`. Confirm format or request structured day/time fields.
7. **Is there a CAIS endpoint for the current active semester?** Or must CSMS infer it from the semester list?
8. **What is the rate limit on CAIS API?** CSMS needs to know to set appropriate cache TTLs and avoid hammering the API.
9. **Will CAIS emit webhooks or events when data changes?** (e.g. when a teaching load is updated) If yes, CSMS can use these to bust caches proactively instead of relying on TTL.
10. **Does CAIS `teaching_loads` include both LEC and LAB rows separately?** CSMS needs to know if a single teaching load covers both components or if they are separate records.

---

## 8. Recommended Implementation Order

### Phase 0 — Prerequisite (Before Any Code)
- [ ] Confirm CAIS API availability and base URL
- [ ] Obtain CAIS system-level Bearer token for CSMS
- [ ] Agree on API contract with CAIS team (share `03-api-contract.md`)
- [ ] Confirm answers to all items in §7 Missing Information

### Phase 1 — Foundation
- [ ] Create `CaisApiService` with HTTP client, base URL, token, timeout, error handling
- [ ] Add `cais_user_id` to CSMS `users` (migration)
- [ ] Implement COLLEGE-01 and DEPT-01 with caching
- [ ] Replace college/department dropdowns in CSMS admin to use API data
- [ ] Add `cais_college_id` / `cais_department_id` columns to affected tables (migrations)
- [ ] Drop `colleges` and `departments` tables after data migration

### Phase 2 — User Identity
- [ ] Implement AUTH-02 (validate email at registration)
- [ ] Implement USER-01 (faculty profile fetch + cache)
- [ ] Update CSMS `users` display to pull name/contact from CAIS cache
- [ ] Populate `cais_user_id` for all existing CSMS users (one-time data migration script)

### Phase 3 — Syllabus Pre-fill
- [ ] Implement SEMESTER-01 and SEMESTER-02
- [ ] Add `cais_semester_id` to `academic_calendars`
- [ ] Implement TEACHING-01 and TEACHING-02
- [ ] Implement SCHEDULE-01
- [ ] Update syllabus wizard step 2 to offer "import from teaching load" option

### Phase 4 — Hardening
- [ ] Add CAIS API health check to CSMS admin dashboard
- [ ] Implement graceful degradation (show cached data when CAIS is unreachable)
- [ ] Add manual cache-bust controls in CSMS admin
- [ ] Fix `syllabi` unique constraint (see §6 Risks)
- [ ] Add all performance indexes (see `04-csms-database-changes.md`)
- [ ] Evaluate Phase 2 per-user token auth with CAIS team
