# Academic Calendar and Events

Practical reference for how Academic Calendars and their semester events behave in CSMS.

## Files Used (Source of Truth)

- Controllers
  - `app/Http/Controllers/AcademicCalendarController.php`
  - `app/Http/Controllers/AcademicCalendarEventController.php`
- Models
  - `app/Models/AcademicCalendar.php`
  - `app/Models/AcademicCalendarEvent.php`
- Routes
  - `routes/web.php` (academic calendar + events routes)

## Key Concepts

- One academic year is represented by **two** `academic_calendars` rows (1st + 2nd semester).
- Events are stored per semester row in `academic_calendar_events`.

## Conditions (If / Then)

### Academic Calendar (Create)

- If `academic_year` is missing:
  - Then validation fails.
- If `academic_year` already exists:
  - Then validation fails (must be unique).
- If any semester start/end date is missing:
  - Then validation fails.
- If a semester `end_date < start_date`:
  - Then validation fails.

### Academic Calendar (Update)

- If the requested `academic_year` does not exist:
  - Then redirect back to calendar index with an error toast.
- If changing the `academic_year` value:
  - Then the new year must still be unique.
- If dates are changed:
  - Then the same date validations as Create apply.

### Academic Calendar (Delete)

- If an academic year is deleted:
  - Then all semester rows for that `academic_year` are removed.
  - Then related events are removed via FK cascade.

### Academic Event (Create)

- If `type` is missing:
  - Then validation fails.
- If `type` is not one of:
  - `holiday`
  - `exam`
  - `break`
  - `other`
  - Then validation fails.
- If `name` is missing:
  - Then validation fails.
- If `date` is missing or invalid:
  - Then validation fails.
- If `date` is outside the semester range:
  - Then validation fails.
  - (Range check: `date >= semester.start_date` and `date <= semester.end_date`.)
- If another event already exists on the same `date` for the same semester:
  - Then validation fails (unique per semester date).

### Academic Event (Update)

- If validations fail:
  - Then update is blocked.
- If checking date uniqueness:
  - Then exclude the current event id.

### Academic Event (Delete)

- If an event id is deleted:
  - Then that event row is removed.
  - Then redirect back to the academic-year events screen.

## Sequences (Typical Flow)

### Create an Academic Year

1. User submits year + two semester date ranges.
2. System validates year uniqueness and date ranges.
3. System creates 2 semester rows (1st and 2nd) under the same `academic_year`.
4. System redirects to the event management screen for that year.

### Add / Edit Events

1. User selects the academic year and semester.
2. User adds or edits events.
3. System enforces type whitelist, date-in-range, and per-date uniqueness.
4. Events immediately affect features that depend on calendar weeks (see `app/MD/10_Syllabus_Wizard.md`).
