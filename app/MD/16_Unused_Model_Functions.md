# Unused Model Functions

All previously unused functions have been removed. This document reflects the current state.

---

## Changes Made

### Removed from `Course`
- `components()` — delegated to `syllabus->components()` directly
- `hasLabComponent()` — callers read `has_lec_lab` attribute directly
- `getLecComponent()` — callers use `syllabus->getLecComponent()`
- `getLabComponent()` — callers use `syllabus->getLabComponent()`
- `scopeWithFullDetails()` — no callers
- `scopeWithEditData()` — `CourseController::edit()` uses `loadMissing()` directly

### Removed from `CourseComponent`
- `scopeLecture()` — callers use `->where('type', 'LEC')` inline
- `scopeLaboratory()` — callers use `->where('type', 'LAB')` inline
- `isLecture()` — no callers
- `isLaboratory()` — no callers
- `getFormattedSchedule()` — **kept** as a display utility

### Removed from `Department`
- `chair()` — `chair_user_id` column is never populated; `deptChair()` via `UserAssignment` is the canonical approach

### Removed from `Syllabus`
- `hasLab()` — callers read `course->has_lec_lab` directly
- `scopeWithFullDetails()` — no callers
- `isApproved()` — no callers

### Removed from `User`
- `chairedDepartments()` — `chair_user_id` column unused; assignments use `UserAssignment`
- `preparedSyllabi()` — callers query `Syllabus::where('prepared_by', ...)` directly
- `concurredSyllabi()` — no callers
- `approvedSyllabi()` — no callers
- `createdCourses()` — no callers
- `isChairOfDepartment(int $departmentId)` — no callers
- `isFacultyOfDepartment(int $departmentId)` — no callers

---

## Summary

| Model          | Removed |
|----------------|---------|
| Course         | 6       |
| CourseComponent| 4       |
| Department     | 1       |
| Syllabus       | 3       |
| User           | 7       |
| **Total**      | **21**  |

No unused model functions remain.
