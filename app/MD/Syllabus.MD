# Syllabus Module (Overview)

High-level documentation for the Syllabus feature in CSMS (wizard-based creation/editing).

## Files Used (Common)

- Wizard
  - `app/Livewire/Syllabus/SyllabusWizard.php`
  - `resources/views/livewire/syllabus/syllabus-wizard.blade.php`
- Steps
  - `app/Livewire/Syllabus/Steps/*`
  - `resources/views/livewire/syllabus/steps/*`
- Models
  - `app/Models/Syllabus.php`
  - `app/Models/CourseComponent.php`
  - `app/Models/CourseOutcome.php`
  - `app/Models/SyllabusWeek.php`
  - `app/Models/WeekContent.php`
  - `app/Models/SyllabusEvaluationItem.php`
  - `app/Models/Reference.php`
  - `app/Models/OnlineMaterial.php`
- Supporting models
  - `app/Models/AcademicCalendar.php`
  - `app/Models/AcademicCalendarEvent.php`
  - `app/Models/Course.php`
- Routes
  - `routes/web.php` (syllabus routes)

## Related Documentation

- Detailed wizard behavior: `app/MD/10_Syllabus_Wizard.md`
- Calendar rules (week generation inputs): `app/MD/02_Academic_Calendar_and_Events.md`
- Course rules (course prerequisites, lab flag): `app/MD/04_Course_Management.md`

## Sequences (Typical User Flow)

1. Faculty opens Syllabus Wizard for a course.
2. Select academic calendar.
3. Fill lecture/lab component details.
4. Encode course outcomes.
5. Generate weeks and encode weekly coverage.
6. Encode course evaluation weights.
7. Review summary.
8. Submit for chair review.
