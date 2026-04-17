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

### Academic Calendar (Update — Livewire)

- If the requested `academic_year` does not exist in DB:
  - Then add error and return.
- If changing the `academic_year` value:
  - Then the new year must still be unique (unless it matches the current year being edited).
- If dates are changed and syllabi with generated weeks exist for this calendar:
  - Then `showStaleWeeksWarning = true` and update is paused.
  - Then blade shows a red warning banner: "Syllabi with generated weeks exist."
  - If admin clicks "Proceed Anyway":
    - Then `showStaleWeeksWarning` is bypassed and update proceeds.
    - Then existing syllabus week dates become stale (faculty must regenerate manually).
  - If admin clicks "Cancel":
    - Then `showStaleWeeksWarning = false` and no changes are saved.
- If all validations pass and no stale-weeks pause:
  - Then update both semester rows under the same `academic_year`.

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

- If an event is deleted:
  - Then that event row is removed.
  - Then redirect back to the academic-year events screen.
- If the event is linked to a syllabus week (via week lock logic):
  - Then the week will no longer be locked on next week regeneration.
  - Then existing locked weeks in already-generated syllabi are not automatically unlocked.

## Sequences (Typical Flow)

### Create an Academic Year

1. User submits year + two semester date ranges.
2. Livewire validates year uniqueness and date ranges in real time.
3. User clicks "Create Calendar" → confirm modal opens.
4. User confirms → system creates 2 semester rows (1st and 2nd).
5. System redirects to the event management screen for that year.

### Edit an Academic Year with Existing Syllabi

1. User edits dates.
2. System detects syllabi with generated weeks linked to this calendar.
3. System shows stale-weeks warning banner.
4. User either proceeds (accepting stale weeks) or cancels.

### Add / Edit Events

1. User selects the academic year and semester.
2. User adds or edits events.
3. System enforces type whitelist, date-in-range, and per-date uniqueness.
4. Events immediately affect features that depend on calendar weeks (see `app/MD/10_Syllabus_Wizard.md`).
