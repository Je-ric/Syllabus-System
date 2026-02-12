# Academic Calendar and Events Rules

This document summarizes conditions for academic calendar setup and semester event management.

## Source Controllers

- `app/Http/Controllers/AcademicCalendarController.php`
- `app/Http/Controllers/AcademicCalendarEventController.php`

## Academic Calendar Creation Conditions

- `academic_year` is required.
- `academic_year` must be unique in validation.
- 1st semester start and end dates are required.
- 1st semester end date must be after or equal to start date.
- 2nd semester start and end dates are required.
- 2nd semester end date must be after or equal to start date.

## Academic Calendar Creation Behavior

- Store creates two records:
- Semester `1st`
- Semester `2nd`
- After creation, user is redirected to event management for the created academic year.

## Academic Calendar Edit Conditions

- Requested academic year must exist.
- If not found, redirect to calendar index with error toast.

## Academic Calendar Update Conditions

- Existing academic year records must exist.
- If changing academic year value, new year must not already exist.
- Same date validations as creation apply.

## Academic Calendar Deletion

- Deletion removes all calendar rows for the given `academic_year`.
- Related events are removed through foreign key cascade.

## Academic Event Conditions

- Event type is required and must be one of:
- `holiday`
- `exam`
- `break`
- `other`
- Event name is required.
- Event date is required and must be a valid date.
- Event date must be within semester date range:
- `after_or_equal semester.start_date`
- `before_or_equal semester.end_date`
- Event date must be unique per semester date.

## Academic Event Update Conditions

- Same type, name, and date range checks.
- Date uniqueness check excludes current event id.

## Academic Event Deletion

- Event is deleted by event id.
- User is redirected back to academic year events page.

