# Academic Calendar and Events

Practical reference for how Academic Calendars and their semester events behave in CSMS.

## Files Used (Source of Truth)

- Controllers
  - `app/Http/Controllers/Academic/AcademicCalendarController.php` — Academic calendar CRUD
  - `app/Http/Controllers/Academic/AcademicCalendarEventController.php` — Academic event management
- Livewire
  - `app/Livewire/AcademicCalendar/AcademicCalendarForm.php` — Calendar form handling (create, update)
  - `app/Livewire/AcademicCalendar/AcademicCalendarEventForm.php` — Event form handling (single date, date range, delete)
- Models
  - `app/Models/AcademicCalendar.php`
  - `app/Models/AcademicCalendarEvent.php`
  - `app/Models/Syllabus.php`
  - `app/Models/SyllabusWeek.php`
- Views (controller-rendered)
  - `resources/views/Academic/AcademicCalendar/index.blade.php` — Academic years listing
  - `resources/views/Academic/AcademicCalendar/form.blade.php` — Academic year form
  - `resources/views/Academic/AcademicCalendar/Modals/cancelEditModal.blade.php` — Cancel edit confirmation
  - `resources/views/Academic/AcademicCalendar/Modals/confirmAYModal.blade.php` — Create academic year confirmation
  - `resources/views/Academic/AcademicCalendar/Modals/deleteAYModal.blade.php` — Delete academic year confirmation
  - `resources/views/Academic/AcademicCalendarEvent/index.blade.php` — Events listing for a semester
- Views (Livewire)
  - `resources/views/livewire/academic-calendar/form.blade.php` — Calendar form component with date guidelines
  - `resources/views/livewire/academic-calendar/event-form.blade.php` — Event form component with type legend
  - `resources/views/livewire/academic-calendar/partials/event-modal.blade.php` — Event add/edit modal with type guidance and quick buttons
  - `resources/views/livewire/academic-calendar/partials/` — Other partials
- Help Views
  - `resources/views/help/academic-calendar.blade.php` — User-facing help manual for calendar management
- Routes
  - `routes/web.php` (academic calendar + events routes — `role:admin,ovpaa`)
    - `GET /academic-calendars` — index
    - `GET /academic-calendars/create` — create form
    - `POST /academic-calendars` — store (routed but handled by Livewire `store()`)
    - `GET /academic-calendars/{academicYear}/edit` — edit form
    - `PUT /academic-calendars/{academicYear}` — update (routed but handled by Livewire `update()`)
    - `DELETE /academic-calendars/{academicYear}` — destroy (controller)
    - `POST /academic-calendars/{academicYear}/set-active` — set active (controller)
    - `GET /academic-calendars/{academicYear}/events` — events index
    - `POST /academic-calendars/{semester}/events` — store event (routed but handled by Livewire)
    - `PUT /academic-calendars/events/{event}` — update event (routed but handled by Livewire)
    - `DELETE /academic-calendars/events/{event}` — destroy event (controller)

## Key Concepts

- One academic year is represented by **two** `academic_calendars` rows — one for 1st semester, one for 2nd semester.
- Events are stored per semester row in `academic_calendar_events`.
- `is_active = true` on one semester row marks it as the active calendar used by the syllabus wizard and dashboard.
- `setActive()` is a static model method — it wraps all-false then single-true in a DB transaction.
- Store and update of the calendar form are handled by the `AcademicCalendarForm` Livewire component; the POST/PUT controller methods do not exist.
- Event add, edit, delete, and bulk range are handled by the `AcademicCalendarEventForm` Livewire component; the POST/PUT controller event methods do not exist.
- CSV import is present in the codebase but **currently disabled** (commented out with `WithFileUploads`).

## Recent UI/UX Improvements

### Event Type Clarity
- Added visual categorization of event types in the event modal (Reference/Skip/Lock)
- Added quick-type buttons for common events (Suspension, Christmas Break, Semester Break, Exam, Non-Teaching)
- Added dynamic guidance based on selected event type
- Added type-specific toast warnings when creating events
- Updated legend in event calendar view to show impact (Ref/Skip/Lock)
- Enhanced event form with color-coded type guidance boxes

### Calendar Form Improvements
- Added date guidelines banner explaining cross-year semester support
- Added reminders about event type impacts
- Improved user guidance for proper event type selection

### Documentation
- Created comprehensive help manual at `resources/views/help/academic-calendar.blade.php`
- Updated this MD file with event type impact documentation
- Added event type selection guide

## Event Types and Their Impact

Events are categorized into three types based on how they affect syllabus week generation:

### Reference Events (Week Created, Editable)
- **`holiday`** — Informational reference for faculty planning (e.g., class suspensions, observances)
- **`other`** — General informational events (e.g., deadlines, reminders)
- **Impact**: Weeks are created normally and remain editable by faculty. Faculty can see these dates when planning assessments, TLA, and topics.

### Skip Events (No Week Created)
- **`break`** — Skips week entirely (e.g., Christmas break, semester breaks, health breaks)
- **Impact**: No syllabus week row is created for that period. The week is completely skipped in the syllabus weekly coverage.

### Lock Events (Week Created, Locked)
- **`exam`** — Locks week as "Exam Week" (e.g., midterm exams, final exams)
- **`non_teaching`** — Locks week as "Non-Teaching Week" (e.g., institutional events)
- **Impact**: Week is created but locked. Faculty cannot edit the content. Auto-filled with "1st Term Exam", "2nd Term Exam", "Final Term Exam", or "Non-Teaching Week".

## Event Type Selection Guide

- Use **`holiday`** for: Class suspensions, holiday observances (reference only, week remains editable)
- Use **`break`** for: Christmas break, semester breaks, health breaks, summer break (week skipped entirely)
- Use **`exam`** for: Midterm exams, final exams, practical exams (week locked as exam)
- Use **`non_teaching`** for: Institutional non-teaching days, special events (week locked as non-teaching)
- Use **`other`** for: General informational events, reminders (reference only, week remains editable)

## Conditions (If / Then)

### Academic Calendar (Create — Livewire `requestCreate` + `store`)

- If `academic_year` is missing or does not match `YYYY-YYYY`:
  - Then validation fails with inline error.
- If `academic_year` already exists in `academic_calendars`:
  - Then validation fails (unique constraint checked on submit, not per keystroke).
- If any semester start/end date is missing:
  - Then validation fails.
- If 1st semester `end_date < start_date`:
  - Then validation fails.
- If 2nd semester `start_date` is not strictly after 1st semester `end_date`:
  - Then validation fails.
- If 2nd semester `end_date < start_date`:
  - Then validation fails.
- If all validations pass:
  - Then `requestCreate()` sets `showConfirmModal = true` — confirm modal opens.
  - Then `store()` is called on confirmation:
    - Then two `academic_calendars` rows are created (1st and 2nd semester) inside a DB transaction.
    - If no calendar is currently active: the newly created 1st semester is auto-activated.
    - Then AuditLog recorded.
    - Then redirect to events index for the new academic year.
  - If DB error: transaction rolled back, inline error shown, modal closed.

### Academic Calendar (Update — Livewire `update`)

- If the requested `academic_year` does not exist in DB:
  - Then inline error added and return.
- If changing the `academic_year` value:
  - Then the new year must be unique (unless it matches the current year being edited).
- If dates are changed AND syllabi with generated weeks exist for this calendar:
  - Then `showStaleWeeksWarning = true` — update is paused.
  - Then the blade shows a stale-weeks warning banner.
  - If admin clicks "Proceed Anyway":
    - Then `showStaleWeeksWarning` is bypassed and `update()` runs again.
    - Then existing syllabus week dates become stale (faculty must regenerate manually).
  - If admin clicks "Cancel" (`cancelStaleWarning`):
    - Then `showStaleWeeksWarning = false` — no changes saved.
- If all validations pass and no stale-weeks pause:
  - Then both semester rows under the same `academic_year` are updated inside a DB transaction.
  - Then AuditLog recorded.
  - Then redirect to calendar index with a success toast.

### Academic Calendar (Set Active — Controller)

- If `academic_year` does not exist in DB:
  - Then redirect to index with error toast.
- If valid:
  - Then `AcademicCalendar::setActive()` wraps all-false + single-true in a transaction.
  - Then AuditLog recorded.
  - Then redirect to index with success toast.

### Academic Calendar (Delete — Controller)

- If `academic_year` URL parameter does not match format `YYYY-YYYY`:
  - Then redirect with error toast (invalid format).
- If no `academic_calendars` rows exist for that year:
  - Then redirect with error toast (not found).
- If any `Syllabus` is linked to this academic year's calendar IDs:
  - Then redirect with error toast showing the count of linked syllabi.
  - Then delete is blocked.
- If all checks pass:
  - Then delete all `academic_calendars` rows for that year inside a DB transaction.
  - Then related events are removed via FK cascade.
  - Then AuditLog recorded.
  - Then redirect to index with success toast.
  - If DB error: transaction rolled back, error toast shown.

### Academic Event (Single Date — Livewire `saveEvent`)

- If `type` is not one of `holiday`, `exam`, `break`, `non_teaching`, `other`:
  - Then `lw-toast` error dispatched.
- If `name` is missing or exceeds 255 chars:
  - Then `lw-toast` error dispatched.
- If `date` is missing, invalid, or outside the semester's `start_date`–`end_date` range:
  - Then `lw-toast` error dispatched.
- If another event already exists on the same `date` for the same semester:
  - Then `lw-toast` error dispatched (unique per `academic_calendar_id` + `date`).
- When editing: the current event's own `id` is excluded from the uniqueness check.
- If editing and the event no longer exists (deleted in another tab):
  - Then `event-saved` dispatched (refreshes list) + error toast. No crash.
- If all validations pass:
  - Then event is created or updated.
  - Then type-specific warning toast is dispatched:
    - If `break`: "Break event created: This week will be SKIPPED in syllabi."
    - If `exam` or `non_teaching`: "Exam/Non-Teaching event created: This week will be LOCKED in syllabi."
    - If `holiday` with "christmas" in name: "Tip: Use 'Break' type to skip Christmas break, or 'Holiday' for reference only."
  - Then AuditLog recorded.
  - Then `event-saved` dispatched + success toast.

### Academic Event (Date Range — Livewire `saveEventRange`)

- If `type`, `name`, `dateStart`, or `dateEnd` fail validation:
  - Then `lw-toast` error dispatched, no rows inserted.
- If `dateStart` or `dateEnd` are outside the semester range:
  - Then validation fails.
- If `dateEnd < dateStart`:
  - Then validation fails.
- If all validations pass:
  - Then existing event dates in the range are fetched and flipped into a skip map.
  - Then one row is inserted per day in the range not already covered (bulk `insert`).
  - Then type-specific warning toast is dispatched:
    - If `break`: "Break event created: X week(s) will be SKIPPED in syllabi."
    - If `exam` or `non_teaching`: "Exam/Non-Teaching event created: X week(s) will be LOCKED in syllabi."
  - Then AuditLog recorded.
  - Then success toast shows how many events were added (e.g. "3 event(s) added.").

### Academic Event (CSV Import)

- **Currently disabled** — `WithFileUploads` trait and `importCsv()` method are commented out.
- When re-enabled: file must be `.csv` or `.txt`, max 512 KB.
- Each row must have at least 3 columns: `type`, `name`, `date`.
- Rows with fewer than 3 columns or failing validation are silently skipped.
- After processing: toast reports imported and skipped counts.

### Academic Event (Delete — Livewire `deleteEvent`)

- Uses `find()` instead of `findOrFail()` — if the event was already deleted (double-click, duplicate dispatch), the method no-ops gracefully.
- If event exists:
  - Then AuditLog recorded.
  - Then event deleted.
- Then `event-deleted` dispatched (closes modal / refreshes list).

### Academic Event (Delete — Controller `destroy`)

- Called by the `DELETE /academic-calendars/events/{event}` route.
- Loads the calendar relationship to get academic year + semester for the AuditLog description.
- Deletes the event and records AuditLog.
- Redirects to events index for that academic year with success toast.

## Sequences (Typical Flow)

### Create an Academic Year

1. Admin navigates to `/academic-calendars/create`.
2. Admin fills in academic year (YYYY-YYYY) and two semester date ranges.
3. Livewire validates format/dates in real time (uniqueness only on submit).
4. Admin clicks "Create Calendar" → confirm modal opens (`showConfirmModal = true`).
5. Admin confirms → `store()` creates 2 semester rows in a transaction.
6. If no active calendar: 1st semester auto-activated.
7. Redirect to events index for the new academic year.

### Edit an Academic Year (with Existing Syllabi)

1. Admin navigates to the edit form.
2. Admin changes semester dates.
3. Livewire detects syllabi with generated weeks linked to this calendar.
4. `showStaleWeeksWarning = true` — warning banner shown, save paused.
5. Admin clicks "Proceed Anyway" → `showStaleWeeksWarning` bypassed, both rows updated.
6. Existing syllabus week dates are now stale — faculty must regenerate weeks manually.

### Set Active Academic Year

1. Admin clicks "Set Active" on a calendar row.
2. Controller calls `AcademicCalendar::setActive()`.
3. All calendars set to `is_active = false`, selected one set to `is_active = true`.
4. AuditLog recorded. Redirect to index with success toast.

### Add Events (Single Date)

1. User selects event type, name, and a single date within the semester range.
2. Livewire validates type whitelist, date-in-range, and per-date uniqueness.
3. If valid: event saved, `event-saved` dispatched, success toast shown.

### Add Events (Date Range)

1. User selects event type, name, start date, and end date.
2. Livewire validates the range against the semester boundaries.
3. System bulk-inserts one row per day, skipping dates already covered.
4. Success toast reports how many days were inserted.

### Delete an Event

1. User clicks delete on an existing event.
2. Livewire `deleteEvent()` uses `find()` — safe against double-click.
3. AuditLog recorded, event deleted, `event-deleted` dispatched.

> **Note:** Events affect features that depend on calendar weeks. See `MD/10_Syllabus_Wizard.md` for how `exam`, `non_teaching`, and `break` events affect week generation and locking.
