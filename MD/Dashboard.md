# Dashboard Proposal

## 1. Purpose

Provide a role-aware landing page that surfaces the most critical information at a glance, replacing the current redirect to `/syllabus` for all roles. Each role sees data relevant to their responsibilities.

---

## 2. Role-Based Sections

### 2.1 Faculty

| Section | Content | Data Source |
|---------|---------|-------------|
| **My Syllabi at a Glance** | 4 stat cards: Draft, Under Review, For Revision, Approved (counts) | `Syllabus::where('preparer_id', auth()->id())` |
| **Recent Activity** | Last 5 syllabi updated | `Syllabus::wherePreparerId()->latest('updated_at')->limit(5)` |
| **Active Semester** | Current semester info from CAIS | `CaisSemester::where('status', 'active')->first()` |
| **Quick Actions** | "Create New Syllabus" button, "Continue Draft" (latest incomplete) | route + latest draft |

### 2.2 Chair

| Section | Content | Data Source |
|---------|---------|-------------|
| **Department Overview** | Total syllabi, by status, for dept's programs | `Syllabus::whereIn('program_id', $programIds)` |
| **Pending Actions** | Syllabi needing chair concurrence (status: under_review) | count + list |
| **Faculty Workload** | Number of syllabi per faculty member | grouped by `preparer_id` |
| **Program Status** | PEOs / POs completeness for each program | counts per program |
| **Quick Actions** | Manage PEOs/POs, View Courses, Approve Syllabi | relevant routes |

### 2.3 Dean

| Section | Content | Data Source |
|---------|---------|-------------|
| **College Overview** | Total syllabi, by status, for college's departments | `Syllabus::whereIn('program_id', $collegeProgramIds)` |
| **Pending Approvals** | Syllabi needing dean approval | count + list |
| **Department Comparison** | Number of syllabi per department | grouped by dept |
| **College Goals** | Goal completion status | `CollegeGoal::where('college_id', $collegeId)` |
| **Quick Actions** | Manage Goals, View Org Hierarchy | relevant routes |

### 2.4 OVPAA

| Section | Content | Data Source |
|---------|---------|-------------|
| **Institution Overview** | Total syllabi system-wide, by status | `Syllabus::selectRaw('status, count(*)')` |
| **Calendar Status** | Active academic year, upcoming events | `AcademicCalendar::latest()->first()` + events |
| **Compliance Rate** | % of courses with syllabi per program | join courses + syllabi |
| **Quick Actions** | Manage Academic Calendars, View All Syllabi | relevant routes |

### 2.5 Admin

| Section | Content | Data Source |
|---------|---------|-------------|
| **System Overview** | Total users, colleges, departments, programs, courses, syllabi | counts per model |
| **Pending Registrations** | Users with status `pending` | `User::where('account_status', 'pending')->count()` |
| **Recent Registrations** | Last 10 registered users | `User::latest()->limit(10)` |
| **Recent Audit Activity** | Last 5 audit log entries | `AuditLog::latest()->limit(5)` |
| **Quick Actions** | Manage Users, Academic Structure, View Audit Logs | relevant routes |

---

## 3. Shared / Global Widgets

| Widget | Description | Visible To |
|--------|-------------|------------|
| **Active Semester Banner** | Highlight current active semester at top | All roles |
| **System-wide Stats** | Total syllabi (with trend), courses, users | Admin, OVPAA |
| **Quick Links** | Role-based shortcut cards (3-4 per role) | All roles |
| **Recent Updates** | Last 5 system-wide changes (audit log) | Admin |
| **CAIS Sync Status** | Last successful sync, connection health | Admin |

---

## 4. Visual Layout

```
┌──────────────────────────────────────────────────────────────┐
│  Welcome back, [Name]                            [Role Badge]│
│  Active Semester: A.Y. 2025-2026 1st Semester               │
├──────────────────────────────────────────────────────────────┤
│  ┌─────────┐  ┌─────────┐  ┌─────────┐  ┌─────────┐        │
│  │ Draft   │  │ Under   │  │ For     │  │Approved │        │
│  │   12    │  │ Review  │  │Revision │  │   45    │        │
│  │         │  │    8    │  │    3    │  │         │        │
│  └─────────┘  └─────────┘  └─────────┘  └─────────┘        │
├──────────────────────┬───────────────────────────────────────┤
│  Pending Actions     │  Quick Actions                        │
│  ┌────────────────┐  │  ┌─────────────────────────────────┐ │
│  │ • Syllabus X   │  │  │ [Create New Syllabus]           │ │
│  │ • Syllabus Y   │  │  │ [Manage PEOs & POs]            │ │
│  │ • Syllabus Z   │  │  │ [View Courses]                  │ │
│  └────────────────┘  │  └─────────────────────────────────┘ │
├──────────────────────┴───────────────────────────────────────┤
│  Recent Activity                                              │
│  ┌─────────────────────────────────────────────────────────┐ │
│  │ • Syllabus "CS 101" submitted for review — 2 mins ago   │ │
│  │ • Syllabus "MATH 201" approved — 1 hour ago             │ │
│  │ • User "jdoe" registered — 3 hours ago                  │ │
│  └─────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────┘
```

---

## 5. Data / API Endpoints Needed

| Endpoint | Purpose |
|----------|---------|
| `GET /api/dashboard/stats` | Aggregated counts (syllabi by status, users, courses) |
| `GET /api/dashboard/pending-actions` | Role-specific action items |
| `GET /api/dashboard/recent-activity` | Latest 5-10 entries |
| `GET /api/dashboard/quick-actions` | Role-based shortcut definitions |

A single `DashboardController` with a `Livewire\Dashboard` component is recommended — the Livewire component can compose data from existing services.

---

## 6. Implementation Notes

- **Route:** `GET /dashboard` at `DashboardController` or `Livewire\Dashboard`
- **Layout:** Reuse `layouts/app.blade.php` sidebar
- **Components:** Use existing `components/layout/card-section.blade.php`, `components/layout/panel.blade.php`, `components/ui/button.blade.php`
- **State:** All data is read-only (no forms), so no `$errors` or validation needed
- **Caching:** Stat counts can be cached for 5-10 minutes with `Cache::remember()`
- **Access:** All authenticated roles; content filtered per-role in the controller/component
