# Integration Reference — CSMS ↔ Mini CSMS

## What Is This?

Two separate Laravel applications running simultaneously.

| App | Port | Database | Role |
|---|---|---|---|
| **CSMS** | `8000` | `csms_2` | The main syllabus system. Acts as the **API consumer** — it logs in to Mini CSMS and reads data from it. |
| **Mini CSMS** | `8001` | `mini_csms` | The external system. Acts as the **API provider** — it exposes a REST API secured with Bearer tokens. |

> **Current state:** Mini CSMS has its own users, colleges, departments, goals, and objectives. CSMS authenticates against Mini CSMS and reads that data. They do not share a database.

---

## How to Run Both

```bash
# Terminal 1
cd C:\Development\CLSU\mini_csms
php artisan serve --port=8001

# Terminal 2
cd C:\csms
php artisan serve --port=8000
```

---

## The Full Request Flow

```
[Browser]
    │
    │  GET http://127.0.0.1:8000/integration/data-viewer
    ▼
[CSMS — MiniCsmsController@dataViewer]
    │
    │  Calls MiniCsmsService::getToken()
    │  Checks Laravel cache key: "mini_csms.token"
    │
    ├─ Cache HIT  → uses cached token, skips login
    │
    └─ Cache MISS → POST http://127.0.0.1:8001/api/login
                        Body: { email, password }  ← from CSMS .env
                            │
                            ▼
                    [Mini CSMS — Api\AuthController@login]
                        Validates credentials against mini_csms.users table
                        Creates Sanctum personal_access_token
                        Returns: { token, token_type: "Bearer", user: {...} }
                            │
                            ▼
                    CSMS receives token
                    Stores in cache for 60 minutes
    │
    │  Now makes 5 GET requests to Mini CSMS, each with:
    │  Header: Authorization: Bearer {token}
    │
    ├─ GET /api/faculty        → returns { users: [...] }
    ├─ GET /api/colleges       → returns { colleges: [...] }
    ├─ GET /api/departments    → returns { departments: [...] }
    ├─ GET /api/goals          → returns { goals: [...] }
    └─ GET /api/objectives     → returns { objectives: [...] }
            │
            ▼
    [Mini CSMS — auth:sanctum middleware]
        Validates Bearer token against personal_access_tokens table
        Passes to the relevant controller
        Returns JSON
            │
            ▼
    CSMS collects all responses
    Passes arrays to Integration/data-viewer.blade.php
    Renders the page
            │
            ▼
[Browser sees the data table]
```

---

## Token Lifecycle

```
First request to data-viewer
    → cache miss
    → POST /api/login to Mini CSMS
    → token stored in CSMS cache for 60 min

Requests 2–N within 60 min
    → cache hit
    → no login call made
    → token used directly

If Mini CSMS returns 401 (token expired/revoked)
    → MiniCsmsService::refreshToken() called
    → cache cleared
    → POST /api/login again
    → retry the original request once
```

---

## Files — CSMS (Consumer)

| File | What It Does |
|---|---|
| `config/mini_csms.php` | Holds the Mini CSMS base URL, login email, password, timeout |
| `.env` | `MINI_CSMS_URL`, `MINI_CSMS_EMAIL`, `MINI_CSMS_PASSWORD`, `MINI_CSMS_TIMEOUT` |
| `app/Services/Integration/MiniCsmsService.php` | All HTTP logic. `getToken()` authenticates and caches. `getFaculty()`, `getColleges()`, `getDepartments()`, `getGoals()`, `getObjectives()` fetch data. Auto-retries on 401. |
| `app/Http/Controllers/Integration/MiniCsmsController.php` | Two actions: `loginDemo` (shows the raw token exchange) and `dataViewer` (calls the service and passes data to blade) |
| `resources/views/Integration/login-demo.blade.php` | Form that sends a live POST /api/login to Mini CSMS and shows the raw JSON response including the token |
| `resources/views/Integration/data-viewer.blade.php` | Displays all fetched data (faculty, colleges, departments, goals, objectives) in tables |
| `resources/views/components/integration/section.blade.php` | Reusable section wrapper used by data-viewer |
| `resources/views/components/integration/empty.blade.php` | Empty state used when an endpoint returns no records |
| `routes/web.php` | `GET /integration/login-demo`, `POST /integration/login-demo`, `GET /integration/data-viewer` — all behind `auth` middleware |

### CSMS .env keys added

```env
MINI_CSMS_URL=http://127.0.0.1:8001
MINI_CSMS_EMAIL=admin@clsu.edu.ph
MINI_CSMS_PASSWORD=password
MINI_CSMS_TIMEOUT=10
```

---

## Files — Mini CSMS (Provider)

| File | What It Does |
|---|---|
| `routes/api.php` | Defines all API endpoints. `POST /api/login` is public. Everything else requires `auth:sanctum`. |
| `routes/web.php` | Web login form and dashboard (separate from the API — for human access to Mini CSMS itself) |
| `app/Http/Controllers/Api/AuthController.php` | `login()` — validates credentials, issues Sanctum token. `logout()` — revokes current token. |
| `app/Http/Controllers/Api/FacultyController.php` | `index()` — all users (filter: `?dept_id=`, `?college_id=`). `show($id)` — single user. `me()` — authenticated user's own profile. |
| `app/Http/Controllers/Api/CollegeController.php` | `index()` — all colleges. `show($id)` — single college with departments. |
| `app/Http/Controllers/Api/DepartmentController.php` | `index()` — all departments (filter: `?college_id=`). `show($id)` — single department with college. |
| `app/Http/Controllers/Api/GoalController.php` | `index()` — all goals with objectives (filter: `?department_id=`). `show($id)` — single goal. |
| `app/Http/Controllers/Api/ObjectiveController.php` | `index()` — all objectives (filter: `?goal_id=`). `show($id)` — single objective. |
| `app/Http/Resources/FacultyResource.php` | Shapes the user JSON. Exposes both `id`/`user_id`, `first_name`/`user_fname`, `department_id`/`dept_id` so CSMS can read it with either key. |
| `app/Http/Resources/CollegeResource.php` | Shapes college JSON. Exposes `id` and `college_id`. |
| `app/Http/Resources/DepartmentResource.php` | Shapes department JSON. Exposes `id`, `department_id`, and `dept_id`. |
| `app/Http/Resources/GoalResource.php` | Shapes goal JSON. Includes nested `objectives` array when loaded. |
| `app/Http/Resources/ObjectiveResource.php` | Shapes objective JSON. |
| `app/Models/User.php` | Has `HasApiTokens` (Sanctum). Fields: `employee_no`, `first_name`, `last_name`, `email`, `user_type`, `department_id`, `college_id`. |
| `app/Models/College.php` | `hasMany(Department)` |
| `app/Models/Department.php` | `belongsTo(College)`, `hasMany(Goal)`, `hasMany(User)` |
| `app/Models/Goal.php` | `belongsTo(Department)`, `hasMany(Objective)` |
| `app/Models/Objective.php` | `belongsTo(Goal)` |
| `app/Http/Controllers/WebController.php` | Web login (issues token into PHP session), logout, dashboard (reads DB directly — not via API) |
| `resources/views/auth/login.blade.php` | Plain login form for Mini CSMS web access |
| `resources/views/dashboard/index.blade.php` | Shows active token + stats + all data tables |
| `database/seeders/DatabaseSeeder.php` | Seeds 3 colleges, 4 departments, 5 goals, 10 objectives, 1 admin, 8 faculty |

### Mini CSMS seeded credentials

```
admin@clsu.edu.ph   / password   (user_type: admin)
jdelacruz@clsu.edu.ph / password (user_type: faculty)
```

---

## API Endpoints — Mini CSMS

### Public
```
POST /api/login
Body:    { "email": "...", "password": "..." }
Returns: { "token": "...", "token_type": "Bearer", "user": { id, employee_no, first_name, last_name, email, user_type } }
```

### Protected (Authorization: Bearer {token} required)
```
POST   /api/logout

GET    /api/faculty              ?dept_id=  ?college_id=
GET    /api/faculty/me
GET    /api/faculty/{id}

GET    /api/colleges
GET    /api/colleges/{id}

GET    /api/departments          ?college_id=
GET    /api/departments/{id}

GET    /api/goals                ?department_id=
GET    /api/goals/{id}

GET    /api/objectives           ?goal_id=
GET    /api/objectives/{id}
```

### Response envelope pattern
```json
{ "colleges":    [ ... ] }   ← list
{ "college":     { ... } }   ← single
{ "users":       [ ... ] }   ← faculty list
{ "user":        { ... } }   ← single faculty
{ "departments": [ ... ] }
{ "goals":       [ ... ] }
{ "objectives":  [ ... ] }
```

---

## Database — Mini CSMS

```
colleges
  id | code | name

departments
  id | college_id (FK) | code | name

goals
  id | department_id (FK) | goal_code | title | description

objectives
  id | goal_id (FK) | objective_code | description

users
  id | employee_no | first_name | last_name | email | password
     | user_type | department_id (FK) | college_id (FK)

personal_access_tokens   ← Sanctum table
  id | tokenable_type | tokenable_id | name | token (hashed)
     | abilities | last_used_at | expires_at
```

---

## What CSMS Currently Does With the Data

| Page | URL | What It Shows |
|---|---|---|
| Login Demo | `/integration/login-demo` | Sends a live POST /api/login, shows raw JSON response and the token |
| Data Viewer | `/integration/data-viewer` | Fetches all 5 endpoints, displays faculty/colleges/departments/goals/objectives in tables |

Both pages require the CSMS user to be logged in (`auth` middleware). They are read-only — CSMS never writes to Mini CSMS.

---

## What Is NOT Done Yet (Next Steps)

| What | Why It Matters |
|---|---|
| CSMS does not expose its own API | Mini CSMS currently has its own data. If the goal is for Mini CSMS to read CSMS data, CSMS needs Sanctum installed and its own `POST /api/login` + read endpoints for colleges, departments, goals, objectives. |
| Mini CSMS web login uses Mini CSMS credentials | It does not log in against CSMS. To change this: Mini CSMS WebController would call `POST http://127.0.0.1:8000/api/login` instead of checking its own DB. |
| Token is not scoped | Any Mini CSMS user can access all endpoints. No role-based API restrictions exist yet. |
| No HTTPS | Both apps run on HTTP locally. Production deployment needs HTTPS for Bearer tokens to be safe in transit. |
