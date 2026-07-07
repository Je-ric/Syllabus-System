# Syllabus Creation Review
**Pre-Deployment Audit — Syllabus Creation Module**
Central Luzon State University — Course Syllabus Management System

---

## Issue #2

**Problem**
No authorization check when the wizard is accessed via `syllabusId` in the controller.

**When It Occurs**
`SyllabusController::wizard()` accepts a `syllabusId` query parameter and passes it directly to the view without verifying that the authenticated user owns that syllabus. The ownership check only happens inside `SyllabusWizard::mount()`.

**Impact**
A faculty member who knows another faculty member's syllabus ID can load the wizard URL directly (e.g., `/syllabus/wizard?syllabusId=99`). The Livewire component will abort with 403, but the controller renders the view first, which may expose the course code and title in the page title before Livewire boots.

**Recommended Fix**
Add an ownership check in `SyllabusController::wizard()` before rendering the view when `syllabusId` is provided:
```php
$syllabus = Syllabus::findOrFail($syllabusId);
if ($syllabus->prepared_by !== Auth::id()) {
    abort(403);
}
```

---

## Issue #3

**Problem**
`submitForReview()` in `SyllabusWizard` changes the syllabus status but the Submit button is commented out in the view.

**When It Occurs**
The entire submit section in `review.blade.php` is wrapped in a Blade comment (`{{-- ... --}}`). The `submitForReview()` method still exists and is callable via direct Livewire wire calls.

**Impact**
The submit workflow is non-functional from the UI. Faculty cannot submit their syllabus for review through the normal flow. The only path to `under_review` status is through `saveAsDone()`, which also sets the status — but `saveAsDone()` does not enforce the same gate checks that `submitForReview()` does (calendar, components, outcomes, weeks, evaluation). A faculty member can click "Create version" on an incomplete syllabus and it will be marked `under_review` without passing all required checks.

**Recommended Fix**
Either restore the Submit button or ensure `saveAsDone()` runs the same completeness gate as `submitForReview()` before changing the status to `under_review`.

---

## Issue #4

**Problem**
`saveAsDone()` sets `status = 'under_review'` without validating that all required steps are complete.

**When It Occurs**
A faculty member navigates directly to Step 6 (Review) and clicks "Create version" without completing Steps 1–5.

**Impact**
An incomplete syllabus (missing calendar, no outcomes, no weeks, no evaluation weights) gets its status changed to `under_review` and a snapshot is generated and stored. The snapshot will be empty or malformed. The syllabus is then locked from editing (`isEditable()` returns false for `under_review`), trapping the faculty in a broken state.

**Recommended Fix**
Add the same gate checks from `submitForReview()` at the top of `saveAsDone()` before generating snapshots or changing status.

---

## Issue #9

**Problem**
Consultation hours are saved to the user's profile globally, not scoped to the syllabus.

**When It Occurs**
Any time a faculty member saves consultation hours in any syllabus wizard, those hours overwrite the user's profile-level consultation hours. If the faculty member has different consultation hours for different courses/semesters, saving one syllabus's hours will corrupt the others.

**Impact**
A faculty member with two active syllabi (e.g., one for 1st semester, one for 2nd semester) who updates consultation hours in one wizard will see those hours reflected in all other syllabi. Previously saved syllabi snapshots are unaffected, but the live wizard view for other syllabi will show the wrong hours.

**Recommended Fix**
Store consultation hours per `CourseComponent` row rather than on the user profile, or at minimum warn the user that saving consultation hours updates their profile globally and affects all syllabi.

---

## Issue #13

**Problem**
The `approved_by` field on the `Syllabus` model is not validated to ensure the selected user actually has the `dean` role.

**When It Occurs**
`SyllabusApprovalService::setApprovedBy()` accepts any `?int $userId` and writes it directly to `approved_by` without checking whether that user is a dean.

**Impact**
A faculty member (or a Livewire wire call with a tampered payload) could set `approved_by` to any user ID, including another faculty member or even themselves. The printed syllabus would then show a non-dean as the approving authority, which is an academic integrity issue.

**Recommended Fix**
Add a role check in `setApprovedBy()`:
```php
if ($userId && !User::whereKey($userId)->whereHas('roles', fn($q) => $q->where('name', 'dean'))->exists()) {
    throw ValidationException::withMessages(['approved_by' => 'Selected user is not a dean.']);
}
```
Apply the same check to `setConcurredBy()`.

---

## Issue #14

**Problem**
`SyllabusReviewService::assignReviewer()` sets reviewer status to `'approved'` immediately on assignment with no actual review flow.

**When It Occurs**
Any time a reviewer is added on the Review step.

**Impact**
The reviewer list shows "Approved" for every reviewer the moment they are added, before they have reviewed anything. This is misleading in the printed syllabus signature section and undermines the purpose of having reviewers. If a real review workflow is intended later, this hardcoded `'approved'` status will require a data migration.

**Recommended Fix**
Set the initial status to `'pending'` and implement a reviewer notification/acceptance flow, or at minimum document clearly that this is a placeholder and the status has no functional meaning yet.

---

## Issue #15

**Problem**
The `labUsers` query in `ComponentsStep::render()` runs on every Livewire render cycle.

**When It Occurs**
Every time any Livewire property changes on the Course Components step (e.g., typing in the instructor name field triggers `updated()`, which triggers a re-render, which re-runs the query).

**Impact**
For a system with many active faculty users, this query runs dozens of times per page interaction. On a hosted server with a slow DB connection, this will cause noticeable lag on the Course Components step.

**Recommended Fix**
Move the `labUsers` query into `loadData()` and cache the result as a component property, the same pattern used for `academicCalendars` in `AcademicCalendarStep`.

---

## Issue #16

**Problem**
`ReviewStep::onAnyStepSaved()` calls `loadData(force: true)` on every `syllabus-step-saved` event, regardless of which step was saved.

**When It Occurs**
Every time any step emits `syllabus-step-saved` — including auto-saves triggered by week collapses, evaluation flushes, and navigation.

**Impact**
`loadData()` in `ReviewStep` is expensive: it eager-loads the syllabus with 8 relationships, queries `CompleteSyllabus` twice, and rebuilds reviewer and revision lists. This runs on every auto-save across all steps, even when the user is not on the Review step. On a slow connection, this adds unnecessary latency to every save operation throughout the wizard.

**Recommended Fix**
Guard the reload with a step check:
```php
public function onAnyStepSaved(): void
{
    // Only reload if the review step is currently active
    if ($this->isLoaded && $this->syllabus?->current_step === 'review') {
        $this->loadData(force: true);
    }
}
```

---

## Issue #18

**Problem**
The `course_evaluation` missing-step check counts `Non-Teaching Week` exclusions by exact string match, but exam week labels are written dynamically (e.g., "1st Term Exam").

**When It Occurs**
`stepHasMissingRequired` for `course_evaluation` filters out rows where `assessment_task = 'Non-Teaching Week'` but does not filter out exam week rows. Exam rows are included in the required-weight count. This is correct behavior — but the filter logic uses `TRIM(week_contents.assessment_task) <> 'Non-Teaching Week'` which will miss rows where the value is `'Non-Teaching Week '` (trailing space) or a localized variant.

**Impact**
Minor: if a `WeekContent` row has a non-teaching label with extra whitespace (possible if written by a future code change), the evaluation completeness check will incorrectly require a weight for it, blocking submission.

**Recommended Fix**
The existing `TRIM()` in the SQL already handles leading/trailing spaces. No immediate action needed, but add a note in `WeekLockService` to always write the exact string `'Non-Teaching Week'` without variation.

---

## Issue #20

**Problem**
The `break` event detection in `WeekGenerationService` only checks if any break date falls within the week range, but a break event spanning multiple days could partially overlap a week without the `between()` check catching all cases.

**When It Occurs**
If a break event is stored as a single date (the first day of the break), and the break actually spans multiple days crossing a week boundary, only the week containing the stored date is skipped.

**Impact**
A week that is partially a break week (e.g., break starts Thursday, ends the following Tuesday) will not be skipped. The faculty will see a week row for what should be a break week and may fill in content for it, only to have it cause confusion in the academic calendar.

**Recommended Fix**
Verify with the academic office whether break events are stored as single dates or date ranges. If single dates, document this limitation clearly. If ranges are intended, update the `AcademicCalendarEvent` model and generation logic to support `start_date`/`end_date` for break events.

---

## Issue #21

**Problem**
The `deanMap` in `reviewers.blade.php` is rendered as inline Alpine JavaScript with raw PHP values, creating a potential XSS vector.

**When It Occurs**
The dean name map is rendered as:
```js
deanMap: {
    {{ $u['id'] }}: @js($u['name']),
}
```
`@js()` escapes the value for JavaScript, which is correct. However, the key `{{ $u['id'] }}` is unescaped. If a user ID were somehow non-numeric (e.g., due to a type coercion bug), it could inject JavaScript.

**Impact**
Low risk in practice since user IDs are integers from the database, but it is a code smell. More importantly, if the dean list is large (50+ deans), this inline map bloats the initial HTML payload significantly.

**Recommended Fix**
Pass the dean map as a JSON-encoded variable:
```blade
x-data="{ deanMap: @js(collect($deanUsers)->pluck('name', 'id')->all()), ... }"
```

---

## Issue #24

**Problem**
`CourseOutcomeService::resyncCodes()` orders by `id` (insertion order), but if outcomes are reordered by the user in the future, the codes will not reflect the display order.

**When It Occurs**
Currently not a problem since there is no reordering UI. However, the `all()` method orders by `co_code` (alphabetical/numeric), while `resyncCodes()` orders by `id`. These two orderings are consistent only as long as outcomes are never reordered.

**Impact**
Low risk now, but if a drag-to-reorder feature is added later, the CO codes will be assigned based on insertion order, not display order, causing CO1 to not necessarily be the first outcome shown to the user.

**Recommended Fix**
Add a `sort_order` column to `course_outcomes` now, even if it is not exposed in the UI yet, to make future reordering straightforward without a data migration.

---

## Issue #25

**Problem**
The `for_revision` status is defined in `Syllabus::isEditable()` but there is no UI path to set a syllabus to `for_revision` status.

**When It Occurs**
The `isEditable()` method returns `true` for both `draft` and `for_revision` statuses, and the README describes a "For Revision" tab on the My Syllabi page. However, no controller, service, or Livewire component sets `status = 'for_revision'`.

**Impact**
The "For Revision" tab on the My Syllabi index will always be empty. Faculty who have submitted syllabi that need revision have no way to receive them back for editing. The review workflow is incomplete.

**Recommended Fix**
Implement a "Return for Revision" action accessible to the admin (or chair/reviewer) that sets `status = 'for_revision'` and notifies the faculty member.

---

*End of Review — 25 issues identified.*
