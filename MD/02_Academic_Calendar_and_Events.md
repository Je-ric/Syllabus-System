# Academic Calendar and Events

Practical reference for how Academic Calendars and their semester events behave in CSMS.

## Files Used (Source of Truth)

- Controllers
  - `app/Http/Controllers/AcademicCalendarController.php`
  - `app/Http/Controllers/AcademicCalendarEventController.php`
- Livewire
  - `app/Livewire/AcademicCalendar/AcademicCalendarForm.php`
  - `app/Livewire/AcademicCalendar/AcademicCalendarEventForm.php`
  - `resources/views/livewire/academic-calendar/form.blade.php`
  - `resources/views/livewire/academic-calendar/event-form.blade.php`
- Models
  - `app/Models/AcademicCalendar.php`
  - `app/Models/AcademicCalendarEvent.php`
  - `app/Models/Syllabus.php`
  - `app/Models/SyllabusWeek.php`
- Routes
  - `routes/web.php` (academic calendar + events routes)

## Key Concepts

- One academic year is represented by **two** `academic_calendars` rows (1st + 2nd semester).
- Events are stored per semester row in `academic_calendar_events`.
- Store and update are handled by the `AcademicCalendarForm` Livewire component, not the controller.
- Event add, edit, delete, bulk range, and CSV import are handled by `AcademicCalendarEventForm` Livewire component.

## Conditions (If / Then)

### Academic Calendar (Create — Livewire)

- If `academic_year` is missing:
  - Then validation fails.
- If `academic_year` format is not `YYYY-YYYY`:
  - Then validation fails.
- If `academic_year` already exists in `academic_calendars`:
  - Then validation fails (must be unique).
- If any semester start/end date is missing:
  - Then validation fails.
- If 1st semester `end_date < start_date`:
  - Then validation fails.
- If 2nd semester `start_date` is not after 1st semester `end_date`:
  - Then validation fails.
- If 2nd semester `end_date < start_date`:
  - Then validation fails.
- If all validations pass:
  - Then `requestCreate()` opens the confirm modal (`showConfirmModal = true`).
  - Then `store()` creates two `academic_calendars` rows (1st and 2nd semester).
  - Then redirect to the events screen for the new academic year.
- Real-time validation: `academic_year` only checks format while typing (no DB hit per keystroke); uniqueness is checked on submit.

### Academic Calendar (Update — Livewire)

- If the requested `academic_year` does not exist in DB:
  - Then add error and return.
- If changing the `academic_year` value:
  - Then the new year must still be unique (unless it matches the current year being edited).
- If dates are changed and syllabi with generated weeks exist for this calendar:
  - Then `showStaleWeeksWarning = true` and update is paused.
  - Then the blade shows a warning banner: "Syllabi with generated weeks exist."
  - If admin clicks "Proceed Anyway":
    - Then `showStaleWeeksWarning` is bypassed and update proceeds.
    - Then existing syllabus week dates become stale (faculty must regenerate manually).
  - If admin clicks "Cancel":
    - Then `showStaleWeeksWarning = false` and no changes are saved.
- If all validations pass and no stale-weeks pause:
  - Then update both semester rows under the same `academic_year`.
  - Then redirect to the calendar index with a success toast.

### Academic Calendar (Delete — Controller)

- If `academic_year` URL parameter does not match format `YYYY-YYYY`:
  - Then redirect with error toast (invalid format).
- If no `academic_calendars` rows exist for that year:
  - Then redirect with error toast (not found).
- If any syllabus is linked to this academic year's calendar IDs:
  - Then redirect with error toast showing the count of linked syllabi.
  - Then delete is blocked.
- If all checks pass:
  - Then delete all `academic_calendars` rows matching the confirmed ID set (not raw string).
  - Then related events are removed via FK cascade.
  - Then redirect to index with a success toast.

### Academic Event (Single Date — Add or Edit)

- If `type` is missing or not one of the valid types:
  - Then a toast error is dispatched and save is blocked.
- Valid types: `holiday`, `exam`, `break`, `non_teaching`, `other`
- If `name` is missing:
  - Then a toast error is dispatched.
- If `date` is missing, invalid, or outside the semester's `start_date`–`end_date` range:
  - Then a toast error is dispatched.
- If another event already exists on the same `date` for the same semester:
  - Then a toast error is dispatched (unique per semester date).
- When editing: the current event's own date is excluded from the uniqueness check.
- If all validations pass:
  - Then the event is saved and a success toast is dispatched.

### Academic Event (Date Range — Bulk Add)

- If `type`, `name`, `dateStart`, or `dateEnd` fail validation:
  - Then a toast error is dispatched and no rows are inserted.
- If `dateStart` or `dateEnd` are outside the semester range:
  - Then validation fails.
- If `dateEnd < dateStart`:
  - Then validation fails.
- If all validations pass:
  - Then the system iterates each day in the range.
  - Dates that already have an event for this semester are silently skipped.
  - Only new dates are inserted in a single bulk DB operation.
  - Then a success toast shows how many events were added (e.g. "3 event(s) added.").

### Academic Event (CSV Import)

- File must be `.csv` or `.txt`, max 512 KB.
- Each row must have at least 3 columns: `type`, `name`, `date`.
- Rows with fewer than 3 columns are skipped.
- Each row is individually validated against the same rules as single-date add (type whitelist, name required, date in semester range, unique per semester date).
- Rows that fail validation are skipped silently.
- After processing:
  - Then a toast shows how many events were imported and how many were skipped.
  - Then the CSV file field is cleared.

### Academic Event (Delete)

- Deletion is handled by the Livewire `deleteEvent()` method (inline in the event form) or the controller `destroy()` (via route).
- If an event is deleted:
  - Then that event row is removed.
  - Then a success toast is dispatched.
- If the event type was `exam` or `non_teaching` and it was affecting a syllabus week lock:
  - Then the week will no longer be locked on the next week regeneration.
  - Then existing locked weeks in already-generated syllabi are **not** automatically unlocked.

## Sequences (Typical Flow)

### Create an Academic Year

1. User submits year + two semester date ranges.
2. Livewire validates format and date order in real time (uniqueness only on submit).
3. User clicks "Create Calendar" → confirm modal opens.
4. User confirms → system creates 2 semester rows (1st and 2nd).
5. System redirects to the event management screen for that year.

### Edit an Academic Year with Existing Syllabi

1. User edits dates.
2. System detects syllabi with generated weeks linked to this calendar.
3. System shows stale-weeks warning banner.
4. User either proceeds (accepting stale weeks) or cancels.
5. If proceeding, both semester rows are updated and user is redirected to the calendar index.

### Add Events (Single Date)

1. User selects an event type, name, and a single date.
2. Livewire validates type whitelist, date-in-range, and per-date uniqueness.
3. If valid, event is saved and a success toast is shown.

### Add Events (Date Range)

1. User selects an event type, name, start date, and end date.
2. Livewire validates the range against the semester boundaries.
3. System inserts one event per day, skipping dates that already have an event.
4. Success toast reports how many days were inserted.

### Import Events via CSV

1. User uploads a CSV file with columns: `type`, `name`, `date`.
2. System validates each row individually.
3. Valid rows are inserted; invalid or duplicate rows are skipped.
4. Success toast reports imported and skipped counts.

### Edit an Event

1. User clicks edit on an existing event.
2. Form pre-fills with current values.
3. Same validation rules apply (date uniqueness ignores the current event).
4. If valid, event is updated and a success toast is shown.

### Delete an Event

1. User clicks delete on an existing event.
2. Event is removed immediately (no confirmation modal).
3. Success toast is shown.

> **Note:** Events immediately affect features that depend on calendar weeks. See `MD/10_Syllabus_Wizard.md` for how `exam`, `non_teaching`, and `break` events affect week generation and locking.
