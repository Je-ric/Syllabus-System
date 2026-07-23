# Missing Actions & UI Patterns

## 1. Pages Missing Search / Filter

Only 2 pages have full search+filter: **Account Approval** (`ManageQueue`) and **Audit Logs**. Every other list page is missing search.

| Page | View Path | Current Filtering | Missing |
|------|-----------|-------------------|---------|
| **Syllabus Index** | `Syllabus\index.blade.php` | Status tabs only | No search by course name/code, no academic year filter, no pagination, no sort |
| **Courses Index** | `Course\index.blade.php` | Program selector + Active/Archived tabs | No search by course code/name, no pagination, no sort |
| **Select Course for Syllabus** | `Syllabus\selectCourse.blade.php` | Program selector only | No search, no pagination |
| **College Goals** | `GoalObjective\goal.blade.php` | College dropdown only | No search by goal text |
| **Department Objectives** | `GoalObjective\objective.blade.php` | College + Dept dropdowns | No search by objective text |
| **Programs (PEOs/POs)** | `Programs\index.blade.php` | Program selector only | No search across PEO/PO text |
| **Academic Calendars** | `AcademicCalendar\index.blade.php` | None | No search by year, no filter by status |
| **Calendar Events** | `AcademicCalendarEvent\index.blade.php` | Semester tabs only | No search by event name/type/date |
| **Academic Structure** | `AcademicStructure\index.blade.php` | None | No search for colleges/depts/programs |
| **Org Hierarchy (Colleges)** | `OrganizationalHierarchy\colleges.blade.php` | None | No search for college name |
| **Org Hierarchy (Depts)** | `OrganizationalHierarchy\departments.blade.php` | None | No search for dept/faculty name |

### Recommended Fix Pattern (Per Page)

Copy the established pattern from `ManageQueue`:
1. Add a `wire:model.live.debounce.300ms` search input
2. Add filter dropdowns for relevant dimensions
3. Add a `buildQuery()` method with `->when()` conditions
4. Implement `WithPagination` trait and `updated()` filter reset
5. Show result count ("Showing X of Y")

---

## 2. Missing Collapse / Expand Patterns

### 2.1 What Exists vs What's Missing

| Pattern | Exists? | Location |
|---------|---------|----------|
| Accordion (general) | Yes | `components/layout/accordion.blade.php` |
| Accordion (weeks) | Yes | `syllabus/steps/weekly-partials/week-accordion.blade.php` |
| Row expand (users) | Yes | `account-approval/partials/table.blade.php` |
| Text expand (audit) | Yes | `audit-log/audit-log.blade.php` |
| Offcanvas/drawer | Yes | `components/layout/offcanvas.blade.php` |
| Help panel | Yes | `components/layout/help-panel.blade.php` |
| --- | --- | --- |
| **Courses table row expand** | **MISSING** | Each course row could expand to show CO-PO mapping details |
| **Syllabus card expand** | **MISSING** | Syllabus cards in index could expand for quick metadata |
| **Academic Structure inline edit** | **MISSING** | Currently opens modal for every CRUD; inline editing would be faster |

### 2.2 Recommended Additions

- **Course Curriculum Map row expand**: Click a course row to reveal the CO-PO IED mapping matrix inline, avoiding a modal or separate page
- **Syllabus card quick-peek**: Click a syllabus card to reveal: program, academic year, last updated, progress %, preview link — without navigating away
- **Goals/Objectives inline expand**: Show related syllabi count per goal/objective on click
- **Academic calendar year expand**: Click a year card to reveal its two semesters + event counts inline

---

## 3. Missing Keyboard Shortcuts

Currently only `Escape` is used (to close modals/drawers). No application-wide shortcuts exist.

### 3.1 Proposed Shortcuts

| Shortcut | Action | Scope | Priority |
|----------|--------|-------|----------|
| `Ctrl+N` | Create new syllabus | Global | High |
| `Ctrl+F` | Focus search bar on current page | Global | High |
| `Ctrl+K` | Command palette (quick jump to any page) | Global | High |
| `Ctrl+S` | Save draft (in wizard) | Wizard steps | High |
| `Escape` | Close modal / cancel edit | Global | Already exists |
| `?` | Show keyboard shortcuts help overlay | Global | Medium |
| `g` then `s` | Go to Syllabi | Global (sequential) | Medium |
| `g` then `c` | Go to Courses | Global (sequential) | Medium |
| `g` then `p` | Go to Programs / PEOs-POs | Global (sequential) | Medium |
| `g` then `a` | Go to Academic Calendars | Global (sequential) | Medium |
| `g` then `u` | Go to User Management (admin) | Global | Medium |
| `g` then `l` | Go to Audit Logs (admin) | Global | Medium |
| `j` / `k` | Navigate next/previous item in list | List pages | Low |
| `/` | Focus search (like GitHub) | List pages | Medium |
| `Ctrl+Enter` | Submit current form / confirm modal | Forms, modals | High |
| `Ctrl+Z` | Undo in wizard | Wizard | Low |
| `Ctrl+Shift+P` | Preview current syllabus | Syllabus view | Medium |
| `Ctrl+Shift+D` | Download current syllabus | Syllabus view | Medium |

### 3.2 Implementation Approach

Create a global Alpine.js component `x-data="shortcuts()"` in the master layout (`layouts/app.blade.php`):

```js
document.addEventListener('keydown', (e) => {
    // Don't trigger when typing in inputs/textareas
    if (['INPUT', 'TEXTAREA', 'SELECT'].includes(e.target.tagName)) return;

    if (e.ctrlKey && e.key === 'n') { e.preventDefault(); window.location.href = '/syllabus/create'; }
    if (e.ctrlKey && e.key === 'k') { e.preventDefault(); openCommandPalette(); }
    if (e.key === '?') { e.preventDefault(); toggleShortcutsHelp(); }
    // ... etc
});
```

A command palette (`Ctrl+K`) would be the most impactful single shortcut — a modal overlay with fuzzy search across all pages.

---

## 4. Missing Bulk Actions

| Page | Action | Currently | Missing |
|------|--------|-----------|---------|
| **Syllabus Index** | Bulk delete drafts | None | Checkbox per card + "Delete Selected" |
| **Syllabus Index** | Bulk submit for review | None | Checkbox + "Submit Selected" |
| **Courses Index** | Bulk archive | None | Checkbox + "Archive Selected" |
| **Courses Index** | Bulk restore (archived tab) | None | Checkbox + "Restore Selected" |
| **Academic Calendars** | Bulk delete | None | Checkbox + "Delete Selected" |
| **College Goals** | Bulk delete | None | Checkbox + "Delete Selected" |
| **Dept Objectives** | Bulk delete | None | Checkbox + "Delete Selected" |

The `ManageQueue::executeBulk()` pattern already exists in the codebase and can be reused.

---

## 5. Missing Navigation Helpers

| Feature | Description | Current State |
|---------|-------------|---------------|
| **Breadcrumbs** | Show current page path (Syllabus > CS 101 > Edit) | Missing |
| **Back to Top** | Floating button on long pages | Missing |
| **Page size control** | Choose items per page (10/25/50) | Fixed at 10 or 25 |
| **Column visibility** | Show/hide columns in tables | Missing |
| **Export to CSV** | Download current table view as CSV | Missing |
| **Remember scroll position** | Restore scroll on browser back | Missing |
| **Active sidebar indicator** | Highlight current page in sidebar | Has basic, but could be more visible |
| **Collapsible sidebar** | Collapse sidebar to icons-only for more content space | Sidebar is fixed-width |

---

## 6. Missing Helpful Actions

| Action | Where | Benefit |
|--------|-------|---------|
| **Clone/Duplicate syllabus** | Syllabus index, per card | Faculty often reuse syllabi across semesters |
| **Copy course from previous AY** | Course create | Avoid re-entering course data every year |
| **Quick-view tooltip on hover** | Syllabus cards, course rows | See metadata without clicking |
| **In-place edit (inline)** | Goals, Objectives, PEOs, POs | Faster than opening a modal for text-only edits |
| **Drag-to-reorder** | PEOs, POs, Goals, Objectives | Currently fixed order; reordering requires delete+recreate |
| **Smart default values** | Wizard step 1 | Pre-fill semester from active CAIS semester |
| **Auto-save draft** | Syllabus wizard | Currently only saves on explicit "Save" — risk of data loss |
| **Side-by-side comparison** | Syllabus review, revision history | Compare two syllabus versions |
| **Print-friendly view** | Syllabus show/preview | Clean printable layout without sidebar/nav |
| **Share link** | Syllabus show | Copy a direct URL to clipboard |
| **Activity feed per syllabus** | Syllabus detail | Timeline of status changes, reviews, revisions |
| **Notification badge** | Sidebar nav items | Show pending counts (e.g. "Approve (3)" on Syllabi) |
| **Progress ring** | Syllabus index cards | Visual percentage indicator instead of fractional text |

---

## 7. Missing Responsive / Mobile Actions

| Issue | Location |
|-------|----------|
| Tables not horizontally scrollable on mobile | Courses Index, Goals, Objectives |
| Sidebar doesn't collapse into hamburger | Layout (all pages) |
| Modals don't use full-screen on small viewports | All modals |
| Cards don't stack well in narrow viewports | Syllabus Index, Academic Calendars |

---

## 8. Implementation Priority Matrix

| Item | Effort | Impact | Priority |
|------|--------|--------|----------|
| Search bar on Syllabus Index | Medium | High | P0 |
| Search bar on Courses Index | Medium | High | P0 |
| Ctrl+K command palette | High | Very High | P0 |
| Bulk actions on Syllabus Index | Medium | Medium | P1 |
| Breadcrumbs | Low | Medium | P1 |
| Ctrl+S save in wizard | Low | High | P1 |
| Auto-save draft | High | High | P1 |
| Keyboard shortcut help overlay | Low | Medium | P1 |
| Pagination on Syllabus Index | Medium | Medium | P2 |
| Inline edit for Goals/Objectives | Medium | Medium | P2 |
| Collapsible sidebar | Medium | Low | P2 |
| Clone/duplicate syllabus | Medium | High | P2 |
| Side-by-side comparison | High | Medium | P3 |
| Export to CSV | Low | Low | P3 |
| Mobile responsive tables | Medium | Medium | P3 |
| Print-friendly view | Low | Low | P3 |
