# CSMS — Full System Review
> Module-by-module review: MVC, Database, Routes, Middleware, Services, Controllers, Livewire.
> Categorized by severity. Each item: what's wrong → fix needed.

---

## SEVERITY LEVELS
- **CRITICAL** — Security risk, data loss, or system-breaking bug. Fix immediately.
- **HIGH** — Logic gap, missing guard, or broken feature. Fix before production.
- **MEDIUM** — Standards violation, missing best practice, or minor bug. Fix soon.
- **LOW** — Code quality, DRY, naming, minor UI issue. Fix when time allows.
- **RECOMMENDATION** — Additional features, missing files, or improvements to consider.

---

## SECURITY AUDIT

### SQL Injection
**Status: SAFE**
- All queries use Eloquent ORM or Query Builder with parameter binding.
- No raw SQL string concatenation found.
- `LIKE` searches in `ManageQueue.php` use parameterized `?` bindings via `$q->where('name', 'like', $term)` — safe.

### Cross-Site Scripting (XSS)
**Status: MOSTLY SAFE — 1 risk area**
- All Blade views use `{{ }}` escaped output by default — safe.
- `SyllabusSnapshotService::injectVersionsDrawer()` and saved HTML snapshot rendering returns raw HTML responses — this is intentional (it's the syllabus document itself), but user-supplied data embedded inside those HTML snapshots should be reviewed to confirm all values are escaped at generation time.
- Email Blade templates (`accountStatus.blade.php`, `otp.blade.php`) use `{{ }}` — safe.

### CSRF
**Status: SAFE**
- Laravel's CSRF middleware is active by default on all `web` routes.
- All POST/PUT/DELETE routes go through form submissions or Livewire (which handles CSRF internally).

### Authentication & Authorization
**Status: MOSTLY SAFE — 2 gaps noted below (see HIGH section)**

### Password Security
**Status: SAFE**
- Passwords are hashed using `bcrypt` via `'password' => 'hashed'` cast in `User` model.
- OTPs are hashed with `Hash::make()` and verified with `Hash::check()` — safe.

### File Uploads
**Status: N/A**
- No user file upload feature exists currently. Snapshots are system-generated HTML — safe.

---

## CRITICAL

*(None found at this time — no raw SQL, no plain-text passwords, no unguarded mass-assignment.)*

---

## HIGH

### 1. SyllabusController — Destroy method does nothing
**Status: ❌ NOT FIXED**
**File:** `app/Http/Controllers/SyllabusController.php` → `destroy()`
**What's wrong:** The `destroy()` method just returns a toast saying deletion is not allowed, instead of actually deleting the syllabus. The `SyllabusDeleteService` exists and is injected but is never called from this route. The README says "Only draft syllabi can be deleted" and "Only the preparer can delete."
**Fix needed:** Implement the delete logic — check `$syllabus->status === 'draft'`, check `$syllabus->prepared_by === Auth::id()`, then call `$this->deleteService->delete($syllabus)` inside a `DB::transaction()`. Return proper toast on success/failure.

---

### 2. SyllabusWizard — Duplicate syllabus created on page reload
**Status: ❌ NOT FIXED**
**File:** `app/Livewire/Syllabus/SyllabusWizard.php` → `mount()`
**What's wrong:** When `$syllabusId` is null and `$courseId` is provided, it immediately calls `Syllabus::create()` in `mount()`. If the user refreshes the page without a `syllabusId`, a second draft is created. The unique constraint `['course_id', 'academic_calendar_id']` won't catch it because `academic_calendar_id` is null at creation time, so duplicates can exist for the same `(course_id, null)` pair.
**Fix needed:** Before calling `Syllabus::create()` in mount, do a `firstOrCreate` check: `Syllabus::where('course_id', $courseId)->where('prepared_by', Auth::id())->where('status', 'draft')->first()`. If found, use it instead of creating a new one.

---

### 3. AccountApprovalController — `restore()` and `disable()` missing try/catch
**Status: ❌ NOT FIXED**
**File:** `app/Http/Controllers/AccountApprovalController.php` → `restore()`, `disable()`
**What's wrong:** `approve()` and `reject()` have try/catch blocks. `restore()` and `disable()` do not — any DB error will throw an unhandled exception to the user.
**Fix needed:** Wrap `restore()` and `disable()` calls in try/catch identical to `approve()` and `reject()`.

---

### 4. SyllabusDeleteService — Not wrapped in a DB transaction
**Status: ❌ NOT FIXED**
**File:** `app/Services/Syllabus/SyllabusDeleteService.php` → `delete()`
**What's wrong:** The method performs many deletes across multiple tables (components, outcomes, weeks, contents, evaluations, references, materials, revisions, reviewers, snapshots) but has no `DB::transaction()` around them. The docblock says "Must be called inside a DB transaction by the caller" — but in `SyllabusController::destroy()`, no transaction exists (the method doesn't delete anything anyway — see issue #1). In `CourseService::deleteCourse()`, it IS wrapped in a transaction, so that path is safe. The destroy controller path will not be safe once it's fixed.
**Fix needed:** Either wrap inside `SyllabusDeleteService::delete()` itself, or ensure every caller always wraps it.

---

### 5. OTPController — `verifyOTP()` accepts email from POST body (user-controlled)
**Status: ❌ NOT FIXED**
**File:** `app/Http/Controllers/OTPController.php` → `verifyOTP()`
**What's wrong:** `$email = $request->input('email') ?? session('verify_email')`. The email is taken from the POST body first. A user could supply any email address in the POST body and verify the OTP for a different account if they know that account's OTP.
**Fix needed:** Reverse the priority — use session first: `$email = session('verify_email') ?? $request->input('email')`. Session is server-controlled; POST body is user-controlled.

---

### 6. UserController — `update()` does not audit profile changes
**Status: ❌ NOT FIXED**
**File:** `app/Http/Controllers/UserController.php` → `update()`
**What's wrong:** Profile updates (name, email, phone, office) have no `AuditLog::record()` call. The standards require logging all updates.
**Fix needed:** Add `AuditLog::record(action: 'updated', module: 'Profile', referenceId: $user->id, description: "User updated their profile.")` after `$user->update($validated)`.

---

### 7. `ovpaa` role in routes but not defined anywhere
**File:** `routes/web.php`
**What's wrong:** Routes for academic calendars and syllabi use `role:admin,ovpaa` middleware — but `ovpaa` is not a defined role in `RoleSeeder`, `AccountApprovalService::assignRoles()` allowed list (`'roles.*' => 'in:admin,chair,dean,faculty'`), or the README. Any user with role `ovpaa` would have to be inserted manually in the DB.
**Fix needed:** Either add `ovpaa` to the allowed roles in `assignRole()` validation and the seeder, or remove it from the route middleware if it's unused. (OVPAA is responsible for the academic calendar and semester dates and events)

---

## MEDIUM

### 8. `ManageQueue::executeBulk()` — Silently skips failures, no user feedback
**File:** `app/Livewire/AccountApproval/ManageQueue.php` → `executeBulk()`
**What's wrong:** The `catch (\Throwable)` block continues silently. If some users fail to update, the admin has no idea which ones failed.
**Fix needed:** Collect failed IDs, then dispatch a toast with the count of failures. E.g., "3 of 5 users processed. 2 failed."

---

### 9. `GoalObjectiveService::storeGoal()` and `storeObjective()` — No transaction
**File:** `app/Services/GoalObjectiveService.php` → `storeGoal()`, `storeObjective()`
**What's wrong:** `destroyGoal` and `destroyObjective` are wrapped in transactions (correct), but `store` operations are not. If `AuditLog::record()` fails mid-way, the goal is saved without a log. Low risk since `AuditLog::record()` has its own try/catch, but the pattern is inconsistent.
**Fix needed:** Wrap in `DB::transaction()` for consistency, matching the destroy pattern.

---

### 10. `CourseController::courseRules()` — Validation field name mismatch
**File:** `app/Http/Controllers/CourseController.php` → `courseRules()`
**What's wrong:** The validation rule uses key `'code'` but `CourseService::createCourse()` reads `$data['code']` — this is consistent. However, the form field is likely named `code` in the HTML but the DB column is `course_code`. If ever a form changes, this silent mapping will break. The validation also has no `max:` length constraint on `name`, `description`, `prerequisite`, `corequisite`.
**Fix needed:** Add `max:255` to string fields. Consider using the DB column name consistently.

---

### 11. `AcademicCalendarController::store()` and `update()` — Delegated to Livewire but routes exist
**File:** `app/Http/Controllers/AcademicCalendarController.php`
**What's wrong:** The comment says "store / update are handled by Livewire AcademicCalendarForm component." Yet `store` and `update` routes exist in `web.php` pointing to this controller. The controller has no `store()` or `update()` methods — this will throw a `BadMethodCallException` if those routes are ever hit.
**Fix needed:** Either remove the routes or add the controller methods that redirect to the Livewire form.

---

### 12. `SyllabusWizard::saveAsDone()` — Storage paths contain user-supplied data (unsanitized)
**File:** `app/Livewire/Syllabus/SyllabusWizard.php` → `saveAsDone()`
**What's wrong:** `$collegeName`, `$departmentName`, `$programName`, `$facultyName`, `$courseCode` are used directly in filesystem path construction (`$baseDir`). If any of these contain special characters (`/`, `..`, `\`), it could result in path traversal or incorrect file placement.
**Fix needed:** Sanitize each segment before building the path: `preg_replace('/[^A-Za-z0-9 _\-\.]/', '_', $segment)` or use `Str::slug()`.

---

### 13. `Syllabus` model — No `SoftDeletes`
**File:** `app/Models/Syllabus.php`
**What's wrong:** Syllabi are important academic records. Deleting a syllabus is permanent. The standards doc requires soft deletes for important records.
**Fix needed:** Add `use SoftDeletes` to the `Syllabus` model, add `$table->softDeletes()` to the syllabi migration, and update `SyllabusDeleteService` to use `->delete()` (soft) or add a force-delete flow where needed.

---

### 14. `User` model — No `SoftDeletes`
**File:** `app/Models/User.php`
**What's wrong:** Users are never truly deleted (they have status: rejected/disabled), which acts as a soft delete by design. But if `User::forceDelete()` or direct DB deletion happens, all foreign key cascades (syllabi, audit logs nulled) fire permanently. The `UserController` and `AccountApprovalController` never delete users, so risk is low — but the pattern is missing.
**Fix needed:** Add `use SoftDeletes` as a safety net to prevent accidental permanent deletion.

---

### 15. `AuditLog` model — Has both `timestamp` and `created_at` (redundant field)
**File:** `app/Models/AuditLog.php`, migration `2026_02_20_000001_create_audit_logs_table.php`
**What's wrong:** The model has `public $timestamps = true` (so `created_at`/`updated_at` are set) AND a separate `timestamp` column that is also set to `now()`. This is redundant — `created_at` already serves this purpose.
**Fix needed:** Remove the `timestamp` column and use `created_at` instead for display. Update `AuditLog::record()` to remove the `'timestamp' => now()` line. Update the `AuditLog` Livewire component to sort by `created_at` instead of `timestamp`.

---

### 16. `ManageQueue` search uses `LIKE` with no minimum character length
**File:** `app/Livewire/AccountApproval/ManageQueue.php` → `buildQuery()`
**What's wrong:** A single-character search (e.g., `a`) will do a full table scan with `%a%`. On a large user table this becomes expensive.
**Fix needed:** Add a minimum search length: `when(strlen(trim($this->search)) >= 2, ...)`.

---

### 17. `CourseController::destroy()` — Authorization logic is in the controller, not a service
**File:** `app/Http/Controllers/CourseController.php` → `destroy()`
**What's wrong:** The chair scope-check (load program → get departments → check if chair's department is in the list) is business logic living in the controller. The standards say services handle business logic, controllers only handle HTTP.
**Fix needed:** Move the authorization check to `CourseService::canDelete(Course $course, User $user): bool`.

---

### 18. `UserController::update()` allows email change without re-verification
**File:** `app/Http/Controllers/UserController.php` → `update()`
**What's wrong:** A user can change their email address without any OTP re-verification. They could change it to a non-CLSU email since only the `unique:users,email` rule is applied — no domain restriction like in registration.
**Fix needed:** Add the same CLSU domain validation rule as in `AuthController::register()`. Also require OTP verification for email changes.

---

### 19. Constants/enums for hardcoded strings not used
**Files:** Multiple controllers and services
**What's wrong:** Strings like `'draft'`, `'under_review'`, `'for_revision'`, `'approved'`, `'active'`, `'pending'`, `'rejected'`, `'disabled'`, `'dean'`, `'chair'`, `'faculty'`, `'admin'`, `'LEC'`, `'LAB'` appear hardcoded across many files (controllers, services, models). If one string changes, all must be updated manually.
**Fix needed:** Create PHP Enums: `App\Enums\SyllabusStatus`, `App\Enums\AccountStatus`, `App\Enums\UserRole`, `App\Enums\ComponentType`. Use them everywhere.

---

## LOW

### 20. `GoalController` and `ObjectiveController` — Method naming violates PSR conventions
**Files:** `app/Http/Controllers/GoalController.php`, `ObjectiveController.php`
**What's wrong:** Methods are named `goal_store`, `goal_update`, `goal_destroy`, `objective_index` (snake_case). Laravel convention and PSR use camelCase for methods.
**Fix needed:** Rename to `store`, `update`, `destroy`, `index` — these are in their own dedicated controllers so there's no naming conflict.

---

### 21. `SyllabusController` — `show()` just redirects to preview
**File:** `app/Http/Controllers/SyllabusController.php` → `show()`
**What's wrong:** `show()` immediately redirects to `syllabus.preview.complete`. This is a wasted HTTP round-trip that the user sees as two requests.
**Fix needed:** Either remove the `show` route and link directly to `syllabus.preview.complete`, or render the view directly inside `show()`.

---

### 22. `AcademicStructureController::index()` — N+1 potential
**File:** `app/Http/Controllers/AcademicStructureController.php` → `index()`
**What's wrong:** `Program::all()->sortBy('name')` — `all()` loads every program then sorts in PHP. For larger datasets this is inefficient.
**Fix needed:** `Program::orderBy('name')->get()`.

---

### 23. `SyllabusController` — `store()` is effectively dead code
**File:** `app/Http/Controllers/SyllabusController.php` → `store()`
**What's wrong:** The `wizard()` method already creates the syllabus. `store()` creates a second one. In practice, all syllabus creation goes through `wizard()`. The `store()` method exists but the system never sends users to POST `/syllabus` directly.
**Fix needed:** Remove the `store()` method and its route, or clearly document what it's for.

---

### 24. Raw IDs exposed in URLs
**Files:** `routes/web.php`, multiple controllers
**What's wrong:** Raw database IDs are used in URLs (e.g., `/syllabus/{syllabus}`, `/courses/{course}`, `/academic-calendars/{academicYear}/events`). The standards say "never expose raw IDs."
**Fix needed:** Use route model binding with slugs or UUIDs, or at minimum add `{syllabus:uuid}` binding after adding UUIDs to models. For now, at least obfuscate with hashids.

---

### 25. `AuthController::login()` — No rate limiting
**File:** `app/Http/Controllers/AuthController.php` → `login()`
**What's wrong:** There is no rate limiting on login attempts. An attacker can brute-force passwords. Laravel's built-in `ThrottleRequests` or `RateLimiter` is not applied.
**Fix needed:** Add `->middleware('throttle:login')` to the login route, and configure a `login` rate limiter in `AppServiceProvider` or `bootstrap/app.php`: `RateLimiter::for('login', fn() => Limit::perMinute(5)->by(request()->ip()))`.

---

### 26. No password complexity rule on registration
**File:** `app/Http/Controllers/AuthController.php` → `register()`
**What's wrong:** Password only requires `min:6`. No uppercase, number, or symbol requirement. `UserController::changePassword()` correctly requires `min:8` — inconsistent.
**Fix needed:** Standardize to `min:8` with `Password::min(8)->letters()->numbers()` using Laravel's `Password` rule.

---

### 27. `SyllabusDeleteService` — Uses `local` disk but snapshots are on `syllabus_snapshots` disk
**File:** `app/Services/Syllabus/SyllabusDeleteService.php` → `delete()`
**What's wrong:** The loop iterates over `['local', 'google']` disks, but snapshot files are stored using `Storage::disk('syllabus_snapshots')` (see `SyllabusWizard::saveAsDone()`). The `local` disk may not find the files.
**Fix needed:** Change `'local'` to `'syllabus_snapshots'` in the disk loop.

---

### 28. Google Drive credentials committed to repo
**File:** `storage/app/csms-489705-0132f004c56b.json`, `storage/app/oauth-credentials.json`
**What's wrong:** Google Drive service account JSON keys are stored inside the repo's `storage/` directory and likely committed to version control. These are sensitive credentials.
**Fix needed:** Move these to `.env` variables or a secrets manager. Add `*.json` (except `composer.json`/`package.json`) to `.gitignore` in `storage/app/`.

---

### 29. `bootstrap/app.php` — `role:admin,ovpaa` on calendar routes instead of `role:admin`
**File:** `routes/web.php`
**What's wrong:** The README says academic calendars are admin-only. Routes use `role:admin,ovpaa` which introduces an undefined role (see issue #7).
**Fix needed:** Change to `role:admin` if `ovpaa` is not a real role.

---

## RECOMMENDED FILES TO ADD

### R1. `app/Enums/SyllabusStatus.php`
Enum for: `Draft`, `UnderReview`, `ForRevision`, `Approved`.
Eliminates hardcoded `'draft'`, `'under_review'` strings across all files.

### R2. `app/Enums/AccountStatus.php`
Enum for: `Pending`, `Active`, `Rejected`, `Disabled`.

### R3. `app/Enums/UserRole.php`
Enum for: `Admin`, `Dean`, `Chair`, `Faculty`.

### R4. `app/Enums/ComponentType.php`
Enum for: `LEC`, `LAB`.

### R5. `app/Policies/SyllabusPolicy.php`
Centralizes `view`, `update`, `delete` authorization for syllabi instead of manual `if ($syllabus->prepared_by !== Auth::id())` checks scattered in the controller and wizard. Register it in `AuthServiceProvider`.

### R6. `app/Policies/CoursePolicy.php`
Centralizes the chair-scope and admin check for course deletion instead of it living in `CourseController::destroy()`.

### R7. `tests/Feature/AuthTest.php`
Feature tests for: registration, OTP flow, login, failed login, pending/rejected/disabled account redirect, logout.

### R8. `tests/Feature/SyllabusTest.php`
Feature tests for: create, wizard navigation, submit for review, delete (draft only), authorization (another user can't edit).

### R9. `docs/security-guide.md` (referenced in improve.md but missing)
Formal security guide: CSRF, XSS, SQL injection, file upload rules, rate limiting, authorization checklist.

### R10. `docs/naming-conventions.md` (referenced in improve.md but missing)
Naming conventions: models, controllers, services, Livewire components, routes, DB tables, enums.

### R11. `checklists/database-checklist.md` (referenced in improve.md but missing)
Checklist: indexes, foreign keys, soft deletes, unique constraints, timestamps, transaction usage.

### R12. `checklists/deployment-checklist.md`
Pre-deployment: `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache`, `storage:link`, `.env` check, session/cache driver check, migration status.

---

## RECOMMENDED FEATURES TO ADD

### F1. Password Reset Flow
Currently there is no "Forgot Password" feature. Users who forget their password have no self-service recovery. Add a standard Laravel password reset with email link.

### F2. Admin Dashboard / Summary Page
No landing dashboard exists for admin. After login, admin is redirected straight to `/syllabus`. Add a summary dashboard showing: pending approvals count, active users count, syllabi by status, recent audit logs.

### F3. Notification System (In-App)
Email notifications exist (AccountStatusUpdated, OtpMail) but no in-app notification bell. Add a `notifications` table and real-time Livewire badge so users see: "Your syllabus was approved", "Admin approved your account", etc.

### F4. Syllabus Approval by Dean (Missing Workflow)
The `approved_by` field exists on the `Syllabus` model and `SyllabusApprovalService` exists — but there is no route or UI for a Dean to actually mark a syllabus as `approved` and set `approved_at`. The final approval step is incomplete.
**Fix needed:** Add a Dean-accessible approve action on a syllabus under review.

### F5. Chair Review / Concurrence Workflow
Similarly, `concurred_by` exists but there is no route or UI for a Chair to concur on a syllabus. This is a core part of the described workflow but has no implementation.
**Fix needed:** Add a Chair-accessible concur action.

### F6. Rate Limiting on OTP Resend
The `/resend-otp` POST endpoint has no rate limiting. A user can spam OTP emails indefinitely.
**Fix needed:** Add `throttle:5,1` (5 per minute) to the resend route.

### F7. Session Expiry Handling in Livewire
When a Livewire session expires, the page shows a cryptic error or silently fails. Add a global Livewire session expiry handler that redirects to login with a message.

### F8. Pagination on Syllabus Index
`SyllabusController::index()` loads ALL syllabi for the user with no pagination. A faculty member with many syllabi over many years will see performance issues.
**Fix needed:** Add `->paginate(15)` or `->simplePaginate(15)`.

### F9. Search / Filter on Syllabus Index
Currently the syllabus index shows all syllabi with no search or filter. Add filters for: academic year, status (draft/approved), course name.

### F10. Bulk Delete for Audit Logs (Admin)
Audit logs will grow indefinitely. Add an admin tool to archive or purge old audit logs (e.g., older than N months).

---

## SUMMARY TABLE

| # | Severity | Module | Issue |
|---|---|---|---|
| 1 | HIGH | SyllabusController | `destroy()` does nothing — delete never executes |
| 2 | HIGH | SyllabusWizard | Duplicate draft created on page reload |
| 3 | HIGH | AccountApprovalController | `restore()` / `disable()` missing try/catch |
| 4 | HIGH | SyllabusDeleteService | No DB transaction wrap in the service itself |
| 5 | HIGH | OTPController | Email read from POST body instead of session first |
| 6 | HIGH | UserController | Profile update not audited |
| 7 | HIGH | Routes | `ovpaa` role used but never defined |
| 8 | MEDIUM | ManageQueue | Bulk action silently ignores failures |
| 9 | MEDIUM | GoalObjectiveService | Store methods not in transaction |
| 10 | MEDIUM | CourseController | Validation missing `max:` on string fields |
| 11 | MEDIUM | AcademicCalendarController | `store`/`update` routes exist but methods don't |
| 12 | MEDIUM | SyllabusWizard | Unsanitized user data in storage file paths |
| 13 | MEDIUM | Syllabus model | No soft deletes on important academic records |
| 14 | MEDIUM | User model | No soft deletes |
| 15 | MEDIUM | AuditLog | Redundant `timestamp` + `created_at` columns |
| 16 | MEDIUM | ManageQueue | Search runs on any 1 character — no minimum |
| 17 | MEDIUM | CourseController | Auth logic in controller, not service |
| 18 | MEDIUM | UserController | Email change without domain validation or OTP |
| 19 | MEDIUM | System-wide | Hardcoded role/status strings — no enums |
| 20 | LOW | GoalController / ObjectiveController | Snake_case method names |
| 21 | LOW | SyllabusController | `show()` is an unnecessary redirect hop |
| 22 | LOW | AcademicStructureController | `Program::all()` — N+1 / inefficient sort |
| 23 | LOW | SyllabusController | `store()` is dead code |
| 24 | LOW | Routes | Raw DB IDs exposed in URLs |
| 25 | LOW | AuthController | No rate limiting on login |
| 26 | LOW | AuthController | Password min:6 on register vs min:8 on change |
| 27 | LOW | SyllabusDeleteService | Wrong disk name (`local` vs `syllabus_snapshots`) |
| 28 | LOW | Storage | Google Drive JSON credentials in repo |
| 29 | LOW | Routes | Calendar routes use `ovpaa` instead of `admin` only |

---

## ARCHITECTURE — `app/` STRUCTURE ANALYSIS

> Analysis of which folders from the proposed structure CSMS needs, which it doesn't, and why.
> The upcoming **syllabus approval workflow** (Dean approves, Chair concurs, Faculty submits) is factored into every decision.

---

### `app/Enums/`

**USE — High priority. Create these now.**

CSMS has at least 19 hardcoded string literals scattered across controllers, services, models, and Livewire components (see issue #19). Enums eliminate entire categories of typo-bugs and make future changes one-file edits.

| Enum | Values | Used in |
|---|---|---|
| `SyllabusStatus` | `Draft`, `UnderReview`, `ForRevision`, `Approved` | `Syllabus` model, `SyllabusController`, `SyllabusDeleteService`, `ReviewStep`, `SyllabusReviewService` |
| `AccountStatus` | `Pending`, `Active`, `Rejected`, `Disabled` | `User` model, `AccountApprovalService`, `AccountApprovalController`, `AuthController`, `ManageQueue` |
| `UserRole` | `Admin`, `Dean`, `Chair`, `Faculty` | `AccountApprovalService`, `OrganizationalHierarchyService`, `RoleMiddleware`, `ReviewStep`, `SyllabusController` |
| `ComponentType` | `Lec`, `Lab` | `CourseComponent` model, `Syllabus::getLecComponent()`, `CourseEvaluationService` |
| `ReviewerStatus` | `Pending`, `Approved`, `Rejected` | `SyllabusReviewer` model, `SyllabusReviewService` — currently hardcoded inside `updateReviewerStatus()` |

**For the approval workflow:** `SyllabusStatus` must include `UnderReview` and `ForRevision` — these already exist as strings. When the approval workflow is built, the Dean/Chair actions will transition between these states; without an enum, a typo in any one place silently skips the transition.

---

### `app/Policies/`

**USE — High priority. Create `SyllabusPolicy` and `CoursePolicy` at minimum.**

Authorization for syllabi is currently duplicated in three places: `SyllabusController::authorizeSyllabusAccess()`, `SyllabusController::edit()`, and `SyllabusController::update()`. All repeat `$syllabus->prepared_by !== Auth::id()` manually. Policies centralize this.

| Policy | Gates needed | Why |
|---|---|---|
| `SyllabusPolicy` | `view`, `update`, `delete`, `submit`, `approve`, `concur` | `view`: preparer or admin. `update`: preparer + draft/for_revision status. `delete`: preparer + draft only. `submit`: preparer + all gates pass. `approve`: dean role + syllabus is under_review. `concur`: chair role + syllabus is under_review. |
| `CoursePolicy` | `delete` | Chair scope-check currently lives in `CourseController::destroy()` (see issue #17). Move it here. |

**For the approval workflow:** `approve` and `concur` gates on `SyllabusPolicy` are the exact right place to enforce who can move a syllabus to `approved`. Without a Policy, every new controller or Livewire action that touches approval needs to re-implement the same checks manually.

**Don't add yet:** `UserPolicy`, `CollegePolicy`, `DepartmentPolicy` — these are admin-only resources with no cross-user access logic. `RoleMiddleware` is sufficient for now.

---

### `app/Http/Requests/`

**USE — Medium priority. Beneficial but not urgent.**

Validation logic currently lives inline in controllers (`CourseController::courseRules()`, `AuthController::register()`, etc.). Form Requests don't change behavior but they make controllers leaner.

| Request class | Replaces |
|---|---|
| `StoreSyllabusRequest` | Inline validation in `SyllabusController::store()` |
| `RegisterRequest` | Inline validation + CLSU domain rule in `AuthController::register()` |
| `StoreCourseRequest` | `courseRules()` in `CourseController` |
| `SubmitSyllabusRequest` | Submit-gate validation in `ReviewStep` (once approval workflow is built) |

**For the approval workflow:** `ApproveSyllabusRequest` / `ConcurSyllabusRequest` are the right place to enforce that a syllabus ID is valid and exists, rather than trusting Livewire's component state.

**Don't over-invest now:** CSMS is primarily a Livewire app. Livewire components do their own validation inline. Form Requests are most useful for the traditional controller-based routes (auth, courses, structure).

---

### `app/Actions/`

**SKIP for now — don't create.**

Actions (single-class invokables like `CreateSyllabus::handle()`) are useful when the same operation is triggered from multiple surfaces (controller + Livewire + CLI). In CSMS right now, each operation has exactly one entry point. Adding an Actions layer would just add an extra hop with no benefit.

**Revisit when:** The approval workflow needs to be triggered from both a Livewire component AND an admin controller. At that point, `ApproveSyllabus::handle(Syllabus $syllabus, User $actor)` makes sense to avoid duplicating the state-machine logic.

---

### `app/DTOs/`

**SKIP for now — don't create.**

DTOs are useful when data is passed across multiple layers with complex shapes. CSMS passes Eloquent models directly between controller → service → view, which is appropriate for a Laravel app of this size. The overhead of DTO classes is not justified.

**Revisit when:** The approval workflow needs to emit a structured result (e.g., new status + notification payload + audit description). A `SyllabusApprovalResult` DTO would prevent that data from being returned as a loose array.

---

### `app/Events/` and `app/Listeners/`

**USE — but only when the approval workflow is built.**

Right now, CSMS sends emails synchronously inside service methods (`Mail::to()->send()`). This blocks the request and will fail visibly if the mail server is down. Events decouple the action from its side-effects.

| Event | Listener(s) |
|---|---|
| `SyllabusSubmittedForReview` | `NotifyReviewers` (email to assigned reviewers), `RecordAuditLog` |
| `SyllabusApproved` | `NotifyPreparer` (email to faculty), `RecordAuditLog` |
| `SyllabusReturnedForRevision` | `NotifyPreparer` (email with notes), `RecordAuditLog` |
| `AccountApproved` | already handled in `AccountApprovalService` — could be extracted here |

**Don't create now** if the approval workflow doesn't exist yet. An event with no listeners is dead code. Create them when the workflow controller/Livewire action is built.

**Queue dependency:** Events + queued listeners require `QUEUE_CONNECTION=database` and a running `php artisan queue:work` process. Confirm the hosted server supports this before committing.

---

## ADDITIONAL FINDINGS (Post Code-Read)

> Issues found by reading the actual source that are not covered above.

---

### A1. `AccountApprovalService::restore()` — No `DB::transaction()`
**File:** `app/Services/AccountApprovalService.php` → `restore()`
**What's wrong:** Every other method in `AccountApprovalService` (`approve`, `reject`, `disable`, `assignRoles`) is wrapped in `DB::transaction()`. `restore()` is not — it directly calls `$user->save()` and `AuditLog::record()` with no rollback protection.
**Fix needed:** Wrap in `DB::transaction()` to match the pattern of the other methods.

---

### A2. `SyllabusWizard::mount()` — `Syllabus::create()` still fires on direct Livewire mount
**File:** `app/Livewire/Syllabus/SyllabusWizard.php` → `mount()`
**What's wrong:** `SyllabusController::wizard()` correctly checks for an existing draft before creating one (fixes issue #2 at the controller layer). However, if `SyllabusWizard` is mounted with `$courseId` directly (e.g., browser back-button to `/syllabus/wizard?courseId=X` after a Livewire full-page reload), the component's `mount()` calls `Syllabus::create()` unconditionally — the controller guard is skipped entirely.
**Fix needed:** The `firstOrCreate` duplicate-check must live inside `SyllabusWizard::mount()` itself, not only in the controller:
```php
$this->syllabus = Syllabus::firstOrCreate(
    ['course_id' => $this->course->id, 'prepared_by' => Auth::id(), 'status' => 'draft'],
    ['academic_calendar_id' => null, 'current_step' => 'academic_calendar']
);
```

---

### A3. `SyllabusWizard::submitForReview()` duplicates the `saveAsDone()` status transition
**File:** `app/Livewire/Syllabus/SyllabusWizard.php` → `submitForReview()`
**What's wrong:** Both `submitForReview()` and `saveAsDone()` independently set `status = 'under_review'`. There are now two divergent code paths that perform the same state transition with no shared logic. When the approval workflow formalizes the status machine, both will need to be updated or one will be missed.
**Fix needed:** Extract the status transition to `SyllabusApprovalService::submit()` (as already planned in the ARCHITECTURE section) and have both methods call it. This makes the state machine a single point of change.

---

### A4. `ReviewStep::render()` — Full user table queries on every re-render
**File:** `app/Livewire/Syllabus/Steps/ReviewStep.php` → `render()`
**What's wrong:** Every Livewire re-render executes:
- `User::whereHas('roles', ... 'dean')->get()` — all dean users
- `User::whereHas('roles', ... 'faculty')->whereNotIn(...)->get()` — all faculty users

These are unbounded queries with no `limit()` or caching. On a large faculty list (hundreds of users), every keystroke or state change that triggers a re-render runs two full table scans.
**Fix needed:** Either cache these lists (`Cache::remember(...)`) with a short TTL, or convert the reviewer/dean dropdowns to use a Livewire search-as-you-type pattern with `limit(20)` instead of loading all users.

---

### A5. `UserController::index()` — Sorts audit logs by `timestamp` (the redundant column)
**File:** `app/Http/Controllers/UserController.php` → `index()`
**What's wrong:** `AuditLog::where('user_id', $user->id)->orderByDesc('timestamp')->limit(20)->get()` — this uses the redundant `timestamp` column identified in issue #15. When that column is dropped, this query will throw a `QueryException`.
**Fix needed:** Change to `orderByDesc('created_at')` now, so the query works before and after the `timestamp` column removal.

---

### A6. `SyllabusReviewService::assignReviewer()` — New reviewers hardcoded as `approved`
**File:** `app/Services/Syllabus/SyllabusReviewService.php` → `assignReviewer()`
**What's wrong:** The comment says "No approval flow yet: assigning a reviewer marks them approved instantly." The status is hardcoded to `'approved'`. When the real approval workflow is built, this placeholder will silently continue marking reviewers as pre-approved unless explicitly changed. It's a future-bug landmine.
**Fix needed:** Change to `'status' => 'pending'` now, matching the `ReviewerStatus` enum planned in the architecture section. The current behavior (auto-approve) can be kept temporarily by adding a note, but `'pending'` is the correct initial state for a reviewer not yet having acted.

---

### A7. `GoalObjectiveService::getAccessibleGoalColleges()` — Dean without assignment sees all colleges
**File:** `app/Services/GoalObjectiveService.php` → `getAccessibleGoalColleges()`
**What's wrong:**
```php
if ($assignment?->college) { return College::where('id', ...) }
if ($user->hasRole('dean')) { return College::orderBy('name')->get(); } // ALL colleges
return collect();
```
A dean who exists in the system but has no college assignment falls through to the second check and receives all colleges. The correct behavior is: a dean with no assignment should see nothing (empty). The README says "scoped to their assigned college only."
**Fix needed:** Remove the `if ($user->hasRole('dean'))` fallback entirely. A dean with no assignment should get `collect()`, not all colleges.

---

### `app/Notifications/`

**USE — when the approval workflow is built.**

CSMS currently uses `Mail` classes directly. Laravel `Notification` classes are strictly better because:
- They support multiple channels (mail + database) from one class.
- The `database` channel writes to a `notifications` table — enables in-app notification bell (see F3 in RECOMMENDED FEATURES).
- `Notifiable` trait is already on the `User` model.

| Notification | Replaces / New |
|---|---|
| `SyllabusSubmittedNotification` | New — notifies reviewers when a syllabus enters `under_review` |
| `SyllabusApprovedNotification` | New — notifies preparer when syllabus is `approved` |
| `SyllabusReturnedNotification` | New — notifies preparer when returned `for_revision` with notes |
| `AccountStatusNotification` | Replaces `AccountStatusUpdated` Mailable |

**Don't replace existing mails now** — it would be a refactor-only change. Do it when adding the approval workflow so both ship together.

---

### `app/Observers/`

**SKIP — don't create.**

Observers fire on Eloquent events (creating, updated, deleted). In CSMS, all audit logging is explicit: `AuditLog::record()` is called manually and intentionally in each service. Making it implicit via an Observer would make it harder to trace what gets logged and when. The current explicit approach is correct for an audit-sensitive system.

**Exception:** If `SoftDeletes` is added to `Syllabus` (see issue #13), a `SyllabusObserver::forceDeleted()` hook to clean up snapshot files could be clean. But only then.

---

### `app/Exceptions/`

**USE — Medium priority. One custom exception is immediately useful.**

Currently, authorization failures in `SyllabusController` throw generic `AuthorizationException`. Business rule violations in services throw `ValidationException` (e.g., `SyllabusReviewService`, `SyllabusApprovalService`). These are fine, but one custom exception would be useful:

| Exception | Why |
|---|---|
| `SyllabusNotEditableException` | Currently: `isEditable()` returns bool, controller handles it with a redirect. A named exception makes the guard testable and reusable across controller + Livewire. |
| `InvalidSyllabusTransitionException` | **For the approval workflow:** when a syllabus in `approved` state is submitted again, or a `draft` is approved without going through review. A named exception makes the state machine explicit and catchable. |

---

### `app/Rules/`

**USE — One rule is needed immediately.**

| Rule | Where used |
|---|---|
| `ClsuEmail` | `AuthController::register()` and `UserController::update()` (see issue #18) both need the same `@clsu.edu.ph` / `@clsu2.edu.ph` domain check. Currently this regex is duplicated inline. Extract to a reusable `Rule` object. |

---

### `app/Services/`

**Already exists and is well-structured. Extend for the approval workflow.**

Current gap: `SyllabusApprovalService` handles setting `approved_by`/`concurred_by` signatories but it does NOT handle the status transitions (`draft → under_review → approved`). There is no method that transitions `status`. This is the core missing piece for the approval workflow.

**Add to `SyllabusApprovalService`:**

| Method | Action |
|---|---|
| `submit(Syllabus $syllabus, User $actor)` | Validates submit gate → sets `status = under_review` → fires `SyllabusSubmittedForReview` event |
| `approve(Syllabus $syllabus, User $actor)` | Checks `$actor` has dean role → sets `status = approved`, `approved_at = now()` → fires `SyllabusApproved` event |
| `returnForRevision(Syllabus $syllabus, User $actor, string $notes)` | Checks `$actor` has dean/chair role → sets `status = for_revision` → creates `SyllabusRevision` record → fires `SyllabusReturnedForRevision` event |

All three must be wrapped in `DB::transaction()`.

---

### `app/Traits/`

**USE — One trait is immediately useful.**

| Trait | What it does | Used by |
|---|---|---|
| `HasAuditLog` | Wraps `AuditLog::record()` so components/services don't call the static method directly. Easier to mock in tests. | `AccountApprovalService`, `GoalObjectiveService`, `SyllabusApprovalService`, Livewire steps |

This is a minor quality-of-life improvement, not urgent.

---

### `tests/Feature/` and `tests/Unit/`

**USE — Feature tests are essential before the approval workflow ships.**

CSMS has no tests at all. The approval workflow will involve multiple roles, state transitions, and authorization checks — all of which are exactly what feature tests are built for.

**Why you SHOULD add tests:**
- The approval workflow (`submit → review → approve/return`) is a state machine. A bug here means a faculty member's syllabus gets stuck or bypasses the dean. Tests catch this before production.
- Role-based authorization (`SyllabusPolicy`) is easy to test — one test per gate × role combination.
- The OTP flow (issue #5) has a security bug that a test would have caught.
- `SyllabusController::destroy()` (issue #1) does nothing — a test would have caught this immediately.

**Why you might SKIP them for now:**
- The system is pre-production and the DB schema may still change (adding soft deletes, enums, etc.). Tests written now may need to be rewritten.
- No `DatabaseSeeder` or model factories exist yet — setting up test data requires extra work upfront.

**Recommended test priority once the approval workflow is built:**

| Test file | Covers |
|---|---|
| `tests/Feature/Auth/RegistrationTest.php` | Registration, OTP, CLSU domain check, duplicate email |
| `tests/Feature/Auth/LoginTest.php` | Login states: active, pending, rejected, disabled, unverified |
| `tests/Feature/Syllabus/SyllabusCreateTest.php` | Create, duplicate detection, no-PO-mapping guard |
| `tests/Feature/Syllabus/SyllabusDeleteTest.php` | Draft delete (allowed), non-draft delete (blocked), other-user delete (403) |
| `tests/Feature/Syllabus/SyllabusApprovalTest.php` | Submit gate, dean approve, chair concur, return for revision, invalid transitions |
| `tests/Feature/Syllabus/SyllabusAuthorizationTest.php` | Admin can view any, faculty can't view others', dean can approve own college only |
| `tests/Unit/SyllabusStatusEnumTest.php` | Enum values match expected DB strings |
| `tests/Unit/ClsuEmailRuleTest.php` | Valid domains pass, invalid domains fail |

**Unit tests** (`tests/Unit/`) are only worth writing for pure logic classes with no DB dependency: enum methods, the `ClsuEmail` rule, `ProgramCodeHelper`, and eventually the `InvalidSyllabusTransitionException` guard logic.

---

## APPROVAL WORKFLOW — WHAT TO BUILD

> Summary of all pieces needed before the Dean-approval flow can ship.

| # | What | Where | Depends on |
|---|---|---|---|
| 1 | `SyllabusStatus` enum | `app/Enums/SyllabusStatus.php` | Nothing — create first |
| 2 | `UserRole` enum | `app/Enums/UserRole.php` | Nothing — create first |
| 3 | `ReviewerStatus` enum | `app/Enums/ReviewerStatus.php` | Nothing — create first |
| 4 | `SyllabusPolicy` with `submit`, `approve`, `concur` gates | `app/Policies/SyllabusPolicy.php` | `SyllabusStatus` enum, `UserRole` enum |
| 5 | Add `submit()`, `approve()`, `returnForRevision()` to `SyllabusApprovalService` | `app/Services/Syllabus/SyllabusApprovalService.php` | `SyllabusStatus` enum, `DB::transaction()` |
| 6 | Fix `SyllabusController::destroy()` (issue #1) | `app/Http/Controllers/SyllabusController.php` | `SyllabusPolicy::delete` gate |
| 7 | Dean approval UI — route + Livewire or controller action | New route + view | `SyllabusPolicy::approve`, `SyllabusApprovalService::approve()` |
| 8 | Chair concur UI | New route + view | `SyllabusPolicy::concur`, `SyllabusApprovalService::approve()` (or separate method) |
| 9 | Submit button in `ReviewStep` calls `SyllabusApprovalService::submit()` | `app/Livewire/Syllabus/Steps/ReviewStep.php` | All gates passing, `SyllabusStatus` enum |
| 10 | `SyllabusSubmittedForReview` event + `NotifyReviewers` listener | `app/Events/`, `app/Listeners/` | Queue working |
| 11 | `SyllabusApproved` event + `NotifyPreparer` listener | `app/Events/`, `app/Listeners/` | Queue working |
| 12 | Feature tests for the above | `tests/Feature/Syllabus/SyllabusApprovalTest.php` | All above |
