# Unused Model Functions

Functions defined in models but not called anywhere in controllers, services, or Livewire components.

---

## AcademicCalendar

| Function | Reason unused |
|---|---|
| `getFormattedSemester()` | Called only in `AcademicCalendarStep` (used — not unused) |

> No unused functions.

---

## Course

| Function | Notes |
|---|---|
| `components()` | Returns components from latest syllabus. Never called — use `syllabus->components()` directly. |
| `hasLabComponent()` | Alias for `has_lec_lab`. Never called — callers read the attribute directly. |
| `getLecComponent()` | Delegates to latest syllabus. Never called — `SyllabusWizard` loads via `syllabus->getLecComponent()`. |
| `getLabComponent()` | Same as above. |
| `scopeWithFullDetails()` | Never called — no controller uses `Course::withFullDetails()`. |
| `scopeWithEditData()` | Never called — `CourseController::edit()` calls `loadMissing()` directly. |

---

## CourseComponent

| Function | Notes |
|---|---|
| `scopeLecture()` | Never called — callers use `->where('type', 'LEC')` inline. |
| `scopeLaboratory()` | Never called — callers use `->where('type', 'LAB')` inline. |
| `isLecture()` | Never called anywhere. |
| `isLaboratory()` | Never called anywhere. |
| `getFormattedSchedule()` | Never called anywhere. |

---

## Department

| Function | Notes |
|---|---|
| `chair()` | Returns `belongsTo(User, 'chair_user_id')`. Column exists but is never populated — `deptChair()` via `UserAssignment` is used instead. |

---

## Program

| Function | Notes |
|---|---|
| `scopeWithOrderedOutcomes()` | Used in `CourseController` — **not unused**. |

> No unused functions.

---

## Syllabus

| Function | Notes |
|---|---|
| `hasLab()` | Delegates to `course->has_lec_lab`. Never called — callers read the course attribute directly. |
| `scopeWithFullDetails()` | Never called — no controller uses `Syllabus::withFullDetails()`. |
| `isApproved()` | Never called anywhere. |

---

## User

| Function | Notes |
|---|---|
| `chairedDepartments()` | `hasMany(Department, 'chair_user_id')`. Column unused — assignments use `UserAssignment`. Never called. |
| `preparedSyllabi()` | Never called — callers query `Syllabus::where('prepared_by', ...)` directly. |
| `concurredSyllabi()` | Never called anywhere. |
| `approvedSyllabi()` | Never called anywhere. |
| `createdCourses()` | Never called anywhere. |
| `isChairOfDepartment(int $departmentId)` | Never called anywhere. |
| `isFacultyOfDepartment(int $departmentId)` | Never called anywhere. |

---

## Summary

| Model | Unused count |
|---|---|
| Course | 6 |
| CourseComponent | 5 |
| Department | 1 |
| Syllabus | 3 |
| User | 7 |
| **Total** | **22** |

These functions are safe to remove if you want to reduce dead code, or keep them as utility helpers for future use.
