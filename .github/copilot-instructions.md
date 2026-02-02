# CSMS (Curriculum & Syllabus Management System) – AI Agent Guide

## System Overview
CSMS is a Laravel 12 + Livewire 4 web application for managing academic structures, programs, curricula, and courses at higher education institutions (CLSU). The system enforces role-based access control and manages hierarchical academic entities: colleges → departments → programs → courses with their learning outcomes (PEOs, POs).

**Tech Stack:**
- **Backend:** Laravel 12 (PHP 8.2), Livewire 4, Eloquent ORM
- **Frontend:** Tailwind CSS 4 (via Vite) + daisyUI, CDN-based Tailwind (Blade), Heroicons
- **Database:** SQLite (dev), migrations-based schema
- **Build:** Vite (Laravel Vite plugin), concurrently for local dev
- **Auth:** Custom email + password with OTP verification (CLSU email only: `@clsu.edu.ph`, `@clsu2.edu.ph`)

---

## Application Architecture

### Authentication & Role Flow
1. **Registration**: Users register with CLSU email, get OTP via email (`AccountStatusUpdated`, `OtpMail` mails), set to `pending` status
2. **Admin Approval**: Admins approve/reject/disable users in `/account-approval` route (Account Approval → AccountApprovalController)
3. **Role Assignment**: Approved users auto-assigned `faculty` role; admins can assign `admin`, `dean`, `chair` roles
4. **Access Control**: Route middleware `['role:admin']`, `['role:chair']`, etc. via custom `RoleMiddleware` (see [app/Http/Middleware/RoleMiddleware.php](app/Http/Middleware/RoleMiddleware.php))
   - User account_status enum: `pending`, `active`, `rejected`, `disabled`
   - Routes redirect `/` → `/auth` (login/register) or `/dashboard` if authenticated

### Academic Hierarchy Data Model
```
College (has many goals, departments)
├─ CollegeGoal (belongs to college)
└─ Department (has many objectives, programs via pivot)
   ├─ DepartmentObjective (belongs to department)
   └─ Program (belongs to many departments via pivot with 'role')
      ├─ ProgramEducationalObjective (PEO)
      ├─ ProgramOutcome (PO)
      └─ Course (belongs to program, maps to POs via pivot 'ied')
         └─ CourseCurriculumMap (junction: course-outcome with IED level)
```

**Key Relationships:**
- Programs ↔ Departments: many-to-many with `program_departments` pivot (stores role)
- Courses ↔ ProgramOutcomes: many-to-many via `course_curriculum_maps` (stores IED: 1, 2, or 3)
- Users ↔ Roles: many-to-many via `user_roles`

### Core Routes & Controllers
[routes/web.php](routes/web.php) maps to:
- **AuthController**: login, register, logout, OTP flow
- **AccountApprovalController**: user management (approve/reject/restore/disable/assign-role)
- **AcademicStructureController**: CRUD colleges, departments, programs
- **GoalController**: college goals & department objectives CRUD
- **ProgramController**: PEO/PO management, uses `ProgramCodeHelper` to auto-code (PEO1, PEO2... or a, b, c...)
- **CourseController**: course creation/listing with program association
- **AcademicCalendarController**, **AcademicCalendarEventController**: calendar management

---

## Key Patterns & Conventions

### Helper Functions
- **[app/Helpers/ProgramCodeHelper.php](app/Helpers/ProgramCodeHelper.php)**: 
  - `numberToLetter(int)` → converts 1→a, 2→b, 26→z (for PO codes)
  - `resequencePeoCodes(programId)` → auto-assigns PEO1, PEO2... after deletion
  - `resequencePoCodes(programId)` → auto-assigns a, b, c... to POs
  - Use these when reordering/deleting objectives to maintain code consistency

### Form Responses & Flash Messages
All form submissions use consistent pattern:
```php
return redirect()->route('route.name')
    ->with('toast', [
        'message' => 'Action completed successfully.',
        'type' => 'success|error'  // daisyUI toast styling
    ]);
```
Toast displayed via `<x-feedback-status.toast>` component in layout (see [resources/views/layouts/app.blade.php](resources/views/layouts/app.blade.php#L15-L17))

### Validation Rules
- **Emails**: Must end with `@clsu.edu.ph` or `@clsu2.edu.ph`
- **Unique Constraints**: 
  - Course code unique per program (implicitly via course_code)
  - College name unique globally
  - Goals/objectives auto-coded (a, b, c...) per college/department—no manual entry
- **IED Enum**: 1 (Introduced), 2 (Emphasized), 3 (Demonstrated) for course-outcome mapping

### Livewire Components Pattern
Located in [app/Livewire/Programs/](app/Livewire/Programs/):
- **ProgramSelector**: Cascading dropdowns (college → department → program)
  - `mount()` pre-selects if programId passed
  - `updatedCollegeId()`, `updatedDepartmentId()`, `updatedProgramId()` handle state changes
  - Emits/dispatches for parent reactivity (if used in modals)
- **ManagePeos**: Read-only list of PEOs for a program
  - `loadPeos()` fetches and maps from DB
  - Save logic commented out (future enhancement)
- **ManagePos**, **PeoDisplay**: Similar pattern for outcomes

---

## Development Workflow

### Local Development
```bash
# Install dependencies
composer install && npm install

# Run migrations & seed
php artisan migrate --seed

# Start all services (server, queue, logs, Vite)
composer run dev
# Runs: php artisan serve, queue:listen, pail, npm run dev (concurrently with colors)
```

**Services included in `composer run dev`:**
- **server** (blue): `php artisan serve` (localhost:8000)
- **queue** (purple): `php artisan queue:listen --tries=1` (background jobs)
- **logs** (red): `php artisan pail --timeout=0` (real-time log tailing)
- **vite** (yellow): `npm run dev` (HMR, asset compilation)

### Build & Deployment
```bash
# Production build
npm run build   # Outputs optimized assets to public/build/

# Run tests
php artisan test
```

### Database
- Uses SQLite by default (database/database.sqlite)
- All migrations in [database/migrations/](database/migrations/) with timestamps
- Seeders in [database/seeders/](database/seeders/) (e.g., AdminSeeder)

---

## Important Files & Directory Map

| Path | Purpose |
|------|---------|
| [routes/web.php](routes/web.php) | All route definitions; guards with auth & role middleware |
| [app/Http/Controllers/](app/Http/Controllers/) | All endpoint logic; follow single-responsibility (one model per controller) |
| [app/Models/](app/Models/) | Eloquent models with relationships & casts |
| [app/Livewire/Programs/](app/Livewire/Programs/) | Reactive Livewire components for program/objective UI |
| [app/Http/Middleware/RoleMiddleware.php](app/Http/Middleware/RoleMiddleware.php) | Custom role enforcement; attach via route groups |
| [resources/views/layouts/app.blade.php](resources/views/layouts/app.blade.php) | Main layout; header nav shows role-based links |
| [vite.config.js](vite.config.js) | Vite + Tailwind 4 + daisyUI config; do not remove Tailwind plugin |
| [bootstrap/app.php](bootstrap/app.php) | App container config; RoleMiddleware aliased here |

---

## Common Tasks & Solutions

### Adding a New Resource (e.g., Accreditation)
1. **Create migration**: `php artisan make:migration create_accreditations_table`
2. **Create model**: `php artisan make:model Accreditation`
3. **Add relationships** in model (e.g., `belongsTo(Program)`)
4. **Create controller**: `php artisan make:controller AccreditationController`
5. **Define routes** in [routes/web.php](routes/web.php) under appropriate role group
6. **Create Blade views** in [resources/views/Accreditation/](resources/views/Accreditation/) (extends layouts.app)
7. **Add to layout nav** if role-visible in [resources/views/layouts/app.blade.php](resources/views/layouts/app.blade.php#L24-L37)

### Modifying a Form (e.g., CourseController.store)
1. Update validation rules in controller
2. Update `$request->validate([...])` before create/update
3. Ensure model `$fillable` array includes new fields
4. Add Blade input in view (check for toast feedback)
5. Test with `php artisan test` or manual form submission

### Adding Role-Based Access
1. Check route in [routes/web.php](routes/web.php): wrap with `middleware(['role:admin|chair'])`
2. Verify user.hasRole('role_name') in controller if fine-grained checks needed
3. Add nav link in [resources/views/layouts/app.blade.php](resources/views/layouts/app.blade.php#L24-L37) with `@if(auth()->user()->hasRole(...))`

### Working with Codes (PEO, PO, Goal codes)
- **PEO codes**: Always auto-generated as PEO1, PEO2... via `ProgramCodeHelper::resequencePeoCodes(programId)` after any add/delete
- **PO codes**: Auto-generated as a, b, c... via `ProgramCodeHelper::resequencePoCodes(programId)`
- **Goal codes**: Auto-generated as a, b, c... per college (see [GoalController.php](app/Http/Controllers/GoalController.php#L50-L58))
- **Never** manually input codes in forms; always use helpers

### Testing Locally
```bash
# Run PHPUnit
php artisan test

# Run specific test
php artisan test --filter=TestClassName

# With coverage
php artisan test --coverage
```

---

## Troubleshooting & Notes

- **Login issues**: Confirm user email ends with `@clsu.edu.ph` or `@clsu2.edu.ph`; check OTP expiry (timestamps)
- **Route access denied**: Verify user account_status is `active` AND role exists in `user_roles` table
- **Asset 404s**: Ensure `npm run dev` or `npm run build` has run; Vite manifest should exist in public/build/
- **Database lock**: If queue listener stalls, kill process: `pkill -f "queue:listen"`
- **Missing relationships**: Models use eager-loading (`with()`) in controllers to avoid N+1; check model relationships match DB schema
- **No Livewire reactivity**: Verify component extends `Livewire\Component`, properties are public, and `render()` returns view with `{{ $property }}`

