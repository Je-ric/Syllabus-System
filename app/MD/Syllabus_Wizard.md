# Syllabus Wizard - Step-by-Step with Auto-Save

## Overview
The syllabus creation has been converted to a **5-step wizard with auto-save** functionality. Users can navigate between steps and their progress is automatically saved.

## Database Changes

### New Migration: `2026_02_05_120000_add_current_step_to_syllabi_table.php`
- Adds `current_step` column to `syllabi` table to track wizard progress
- Default value: `'academic_calendar'`

### Existing Migration Updated: `2026_02_05_104116_update_course_components_add_syllabus_id.php`
- **IMPORTANT**: This migration changes `course_components.course_id` → `course_components.syllabus_id`
- Components are now syllabus-specific (each syllabus can have different instructors)
- Run: `php artisan migrate` to apply

## Model Updates

### Syllabus Model
**Updated relationships:**
- `components()` - hasMany CourseComponent (direct relationship)
- `courseOutcomes()` - hasMany CourseOutcome

**New helper methods:**
- `getWizardSteps()` - Returns array of step keys and labels
- `getNextStep()` - Returns next step key or null
- `getPreviousStep()` - Returns previous step key or null
- `getLecComponent()` - Updated to use `$this->components()`
- `getLabComponent()` - Updated to use `$this->components()`

**Updated fillable:**
- Added `'current_step'`

### CourseComponent Model
**Changed relationship:**
- `course()` → `syllabus()` (belongs to Syllabus now, not Course)

**Updated fillable:**
- `'course_id'` → `'syllabus_id'`

### CourseOutcome Model
- Already correct with `syllabus_id` relationship
- Has `programOutcomes()` belongsToMany with IED pivot

## Wizard Steps

### Step 1: Academic Calendar
- Select academic year and semester
- Auto-saves to `syllabi.academic_calendar_id`

### Step 2: Course Components
- Enter LEC instructor info (name, email, phone, office, schedule, hours, performance standard)
- Enter LAB instructor info (if `course.has_lec_lab = true`)
- Auto-saves to `course_components` table with `syllabus_id`

### Step 3: Course Outcomes
- Define Course Outcomes (CO1, CO2, CO3...)
- Add/remove outcomes dynamically
- Auto-saves to `course_outcomes` table

### Step 4: CO-PO Mapping
- Map each Course Outcome to Program Outcomes
- Select IED level (I=Introduced, E=Emphasized, D=Demonstrated)
- Auto-saves to `course_outcome_po` pivot table

### Step 5: Review
- Summary of all entered data
- Final check before submission
- "Submit for Review" button changes status to `under_review`

## Livewire Component

### `SyllabusWizard` Component
**Location:** `app/Livewire/Syllabus/SyllabusWizard.php`

**Public Properties:**
- `$syllabus` - Current syllabus model
- `$course` - Course being used
- `$currentStep` - Current wizard step
- All form fields for each step (academic_calendar_id, lec_*, lab_*, courseOutcomes, coPoMappings)

**Key Methods:**
- `mount($syllabusId, $courseId)` - Initialize wizard (create draft or load existing)
- `saveAndNext()` - Save current step and move forward
- `saveAndPrevious()` - Save current step and move backward
- `saveCurrentStep()` - Auto-save based on current step
- `submitForReview()` - Final submission, changes status to `under_review`
- `addCourseOutcome()` - Dynamically add CO
- `removeCourseOutcome($index)` - Remove CO and resequence

**Auto-Save Logic:**
Each step has its own save logic:
- `saveComponents()` - updateOrCreate for LEC/LAB components
- `saveCourseOutcomes()` - Create/update/delete COs
- `saveCoPoMappings()` - Sync CO-PO relationships with IED values

## Views Structure

```
resources/views/
├── Syllabus/
│   ├── wizard.blade.php (wrapper view)
│   └── selectCourse.blade.php (program/course selection)
└── livewire/syllabus/
    ├── syllabus-wizard.blade.php (main wizard UI with progress bar)
    └── steps/
        ├── academic-calendar.blade.php
        ├── course-components.blade.php
        ├── course-outcomes.blade.php
        ├── co-po-mapping.blade.php
        └── review.blade.php
```

## Routes

### New Route:
```php
Route::get('/syllabus/wizard', [SyllabusController::class, 'wizard'])->name('syllabus.wizard');
```

### Updated Route:
```php
Route::get('/syllabus/form/{courseId}', ...)
// Now redirects to wizard with courseId parameter
```

## Controller Updates

### SyllabusController
**Modified methods:**
- `showForm($courseId)` - Now redirects to wizard route

**New method:**
- `wizard($syllabusId = null, $courseId = null)` - Renders wizard wrapper view

**Deprecated (but kept for compatibility):**
- `store()` - May not be used anymore (wizard auto-saves)
- `update()` - May not be used anymore (wizard auto-saves)

## Usage Flow

### Creating New Syllabus:
1. User goes to `/syllabus/create`
2. Selects college → department → program
3. Clicks "Create Syllabus" on a course
4. Redirected to `/syllabus/wizard?courseId={id}`
5. Wizard creates draft syllabus with `status='draft'` and `current_step='academic_calendar'`
6. User fills steps, each step auto-saves
7. User clicks "Submit for Review" on final step
8. Status changes to `under_review`, current_step locked at `review`

### Editing Existing Draft:
1. User clicks "Edit" on a draft syllabus
2. Redirected to `/syllabus/wizard?syllabusId={id}`
3. Wizard loads existing data into form fields
4. User can navigate back/forth, all changes auto-save
5. Can submit when ready

## Key Features

✅ **Auto-save on navigation** - No data loss when switching steps
✅ **Progress indicator** - Visual progress bar showing completed steps
✅ **Back/Next navigation** - Easy to review and update previous steps
✅ **Dynamic CO management** - Add/remove outcomes with auto-resequencing
✅ **Validation** - Each step validates before saving
✅ **Loading states** - Shows "Saving..." overlay during async operations
✅ **Authorization** - Only syllabus preparer can edit
✅ **Draft system** - All syllabi start as drafts until submitted

## Next Steps

1. **Run migrations:**
   ```bash
   php artisan migrate
   ```

2. **Test the wizard:**
   - Create a new syllabus
   - Navigate through all steps
   - Verify auto-save works (refresh page and data persists)
   - Submit and check status changes

3. **Optional enhancements:**
   - Add validation messages for each step
   - Add step completion indicators (checkmarks)
   - Add "Save as Template" feature
   - Add approval workflow (chair review, dean approval)
