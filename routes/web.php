<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Authentication\AuthController;
use App\Http\Controllers\Authentication\AccountApprovalController;
use App\Http\Controllers\System\DashboardController;
use App\Http\Controllers\System\AuditLogController;
use App\Http\Controllers\System\NotificationController;
use App\Http\Controllers\University\UniversityStructureController;
use App\Http\Controllers\UserManagement\UserController;
use App\Http\Controllers\UserManagement\UserAssignmentsController;
use App\Http\Controllers\Academic\AcademicCalendarController;
use App\Http\Controllers\Academic\AcademicCalendarEventController;
use App\Http\Controllers\Academic\CourseController;
use App\Http\Controllers\Academic\WorkloadController;
use App\Http\Controllers\CQI\GoalController;
use App\Http\Controllers\CQI\ObjectiveController;
use App\Http\Controllers\CQI\ProgramController;
use App\Http\Controllers\Syllabus\SyllabusController;
use App\Http\Controllers\Syllabus\SyllabusReviewFormController;
use App\Http\Controllers\Syllabus\ReviewQueueController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('landing');
})->name('home');


Route::get('/auth/login', [AuthController::class, 'showLogin'])->name('auth.login');
Route::get('/auth/register', [AuthController::class, 'showRegister'])->name('auth.register');
Route::get('/auth', function () {
    return redirect()->route('auth.login');
})->name('auth.show');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/waiting-approval', function () {
    return view('Authentication.waiting-approval');
})->name('waiting.approval');


Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Notification endpoints — consumed by the bell dropdown via fetch()
    Route::get('/notifications/data',          [NotificationController::class, 'data'])->name('notifications.data');
    Route::post('/notifications/{id}/read',    [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all',     [NotificationController::class, 'markAllRead'])->name('notifications.read-all');

    Route::get('/profile', [UserController::class, 'index'])->name('profile.index');
    Route::put('/profile', [UserController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [UserController::class, 'changePassword'])->name('profile.password.change');
    Route::post('/profile/password/verify-otp', [UserController::class, 'verifyPasswordOtp'])->name('profile.password.verify-otp');
    Route::post('/profile/password/resend-otp', [UserController::class, 'resendPasswordOtp'])->name('profile.password.resend-otp');
    Route::post('/profile/consultation-hours', [UserController::class, 'storeConsultationHour'])->name('profile.consultation.store');
    Route::delete('/profile/consultation-hours/{hour}', [UserController::class, 'destroyConsultationHour'])->name('profile.consultation.destroy');

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/account-approval', [AccountApprovalController::class, 'index'])->name('accounts.approval');
        Route::post('/account-approval/approve', [AccountApprovalController::class, 'approve'])->name('account-approval.approve');
        Route::post('/account-approval/reject', [AccountApprovalController::class, 'reject'])->name('account-approval.reject');
        Route::post('/account-approval/restore', [AccountApprovalController::class, 'restore'])->name('account-approval.restore');
        Route::post('/account-approval/disable', [AccountApprovalController::class, 'disable'])->name('account-approval.disable');
        Route::post('/account-approval/assign-role', [AccountApprovalController::class, 'assignRole'])->name('account-approval.assign-role');
        Route::put('/account-approval/edit-user', [AccountApprovalController::class, 'editUser'])->name('account-approval.edit-user');

        Route::get('/university-structure', [UniversityStructureController::class, 'index'])->name('university.structure.index');
        Route::post('/university-structure/college', [UniversityStructureController::class, 'storeCollege'])->name('university.structure.college.store');
        Route::put('/university-structure/college/{college}', [UniversityStructureController::class, 'updateCollege'])->name('university.structure.college.update');
        Route::delete('/university-structure/college/{college}', [UniversityStructureController::class, 'destroyCollege'])->name('university.structure.college.destroy');
        Route::post('/university-structure/department', [UniversityStructureController::class, 'storeDepartment'])->name('university.structure.department.store');
        Route::put('/university-structure/department/{department}', [UniversityStructureController::class, 'updateDepartment'])->name('university.structure.department.update');
        Route::delete('/university-structure/department/{department}', [UniversityStructureController::class, 'destroyDepartment'])->name('university.structure.department.destroy');
        Route::post('/university-structure/program', [UniversityStructureController::class, 'storeProgram'])->name('university.structure.program.store');
        Route::put('/university-structure/program/{program}', [UniversityStructureController::class, 'updateProgram'])->name('university.structure.program.update');
        Route::delete('/university-structure/program/{program}', [UniversityStructureController::class, 'destroyProgram'])->name('university.structure.program.destroy');

        Route::get('/user-assignments/colleges', [UserAssignmentsController::class, 'collegesIndex'])->name('user-assignments.colleges.index');
        Route::post('/user-assignments/assign-dean', [UserAssignmentsController::class, 'assignDean'])->name('user-assignments.assign-dean');
        Route::post('/user-assignments/remove-dean', [UserAssignmentsController::class, 'removeDean'])->name('user-assignments.remove-dean');

        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit.logs.index');
    });

    Route::middleware(['role:admin,ovpaa'])->group(function () {
        Route::get('/academic-calendars', [AcademicCalendarController::class, 'index'])->name('academic.calendars.index');
        Route::get('/academic-calendars/create', [AcademicCalendarController::class, 'create'])->name('academic.calendars.create');
        Route::get('/academic-calendars/{academicYear}/edit', [AcademicCalendarController::class, 'edit'])->name('academic.calendars.edit');
        Route::delete('/academic-calendars/{academicYear}', [AcademicCalendarController::class, 'destroy'])->name('academic.calendars.destroy');
        Route::post('/academic-calendars/{academicYear}/set-active', [AcademicCalendarController::class, 'setActive'])->name('academic.calendars.set-active');

        Route::get('/academic-calendars/{academicYear}/events', [AcademicCalendarEventController::class, 'index'])->name('academic.calendar.events.index');
        Route::post('/academic-calendars/{semester}/events', [AcademicCalendarEventController::class, 'store'])->name('academic.calendar.events.store');
        Route::put('/academic-calendars/events/{event}', [AcademicCalendarEventController::class, 'update'])->name('academic.calendar.events.update');
        Route::delete('/academic-calendars/events/{event}', [AcademicCalendarEventController::class, 'destroy'])->name('academic.calendar.events.destroy');
    });

    Route::middleware(['role:admin,dean,chair'])->group(function () {
        Route::get('/user-assignments/hierarchy', [UserAssignmentsController::class, 'hierarchyView'])->name('user-assignments.hierarchy');
        Route::get('/user-assignments/college/{collegeId}/departments', [UserAssignmentsController::class, 'departmentsIndex'])->name('user-assignments.departments.index');
    });

    Route::middleware(['role:admin'])->group(function () {
        Route::post('/user-assignments/assign-chair', [UserAssignmentsController::class, 'assignChair'])->name('user-assignments.assign-chair');
        Route::post('/user-assignments/remove-chair', [UserAssignmentsController::class, 'removeChair'])->name('user-assignments.remove-chair');
        Route::post('/user-assignments/assign-faculty', [UserAssignmentsController::class, 'assignFaculty'])->name('user-assignments.assign-faculty');
        Route::post('/user-assignments/bulk-assign-faculty', [UserAssignmentsController::class, 'bulkAssignFaculty'])->name('user-assignments.bulk-assign-faculty');
        Route::post('/user-assignments/remove-faculty', [UserAssignmentsController::class, 'removeFaculty'])->name('user-assignments.remove-faculty');
    });

    Route::middleware(['role:admin,dean'])->group(function () {
        Route::get('/college/goals', [GoalController::class, 'index'])->name('goal.index');
        Route::post('/college/goals', [GoalController::class, 'store'])->name('goal.store');
        Route::put('/college/goals/{goal}', [GoalController::class, 'update'])->name('goal.update');
        Route::delete('/college/goals/{goal}', [GoalController::class, 'destroy'])->name('goal.destroy');
    });

    Route::middleware(['role:admin,chair'])->group(function () {
        Route::get('/department/objectives', [ObjectiveController::class, 'index'])->name('objective.index');
        Route::post('/department/objectives', [ObjectiveController::class, 'store'])->name('objective.store');
        Route::put('/department/objectives/{objective}', [ObjectiveController::class, 'update'])->name('objective.update');
        Route::delete('/department/objectives/{objective}', [ObjectiveController::class, 'destroy'])->name('objective.destroy');

        Route::get('/programs', [ProgramController::class, 'index'])->name('programs.index');
        Route::get('/programs/{program}', [ProgramController::class, 'show'])->name('programs.show');
        Route::delete('/programs/peo/{peo}', [ProgramController::class, 'deletePeo'])->name('programs.peo.delete');
        Route::delete('/programs/po/{po}', [ProgramController::class, 'deletePo'])->name('programs.po.delete');

        Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
        Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
        Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
        Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
        Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
        Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
        Route::post('/courses/{course}/archive', [CourseController::class, 'archive'])->name('courses.archive');
        Route::post('/courses/{course}/restore', [CourseController::class, 'restore'])->name('courses.restore');
    });

    Route::middleware(['role:admin,faculty'])->group(function () {
        Route::get('/workload', [WorkloadController::class, 'index'])->name('workload.index');
        Route::post('/workload/sync', [WorkloadController::class, 'sync'])->name('workload.sync');
    });

    // Reviewer queue — accessible by chairs, faculty assigned as reviewers, and admins.
    Route::middleware(['role:admin,faculty,chair'])->group(function () {
        Route::get('/syllabus-review-queue', [ReviewQueueController::class, 'index'])->name('syllabus.review-queue.index');
    });

    Route::middleware(['role:admin,faculty,ovpaa'])->group(function () {
        Route::get('/syllabus', [SyllabusController::class, 'index'])->name('syllabus.index');
        Route::get('/syllabus/create', [SyllabusController::class, 'create'])->name('syllabus.create');
        Route::get('/syllabus/courses/{programId?}', [SyllabusController::class, 'showCourses'])->name('syllabus.courses');
        Route::get('/syllabus/wizard', [SyllabusController::class, 'wizard'])->name('syllabus.wizard');
        Route::get('/syllabus/form/{courseId}', [SyllabusController::class, 'showForm'])->name('syllabus.form');
        Route::get('/syllabus/{syllabus}/edit', [SyllabusController::class, 'edit'])->name('syllabus.edit');
        Route::put('/syllabus/{syllabus}', [SyllabusController::class, 'update'])->name('syllabus.update');
        Route::delete('/syllabus/{syllabus}', [SyllabusController::class, 'destroy'])->name('syllabus.destroy');
        Route::view('/showcase', 'components.showcase');
    });

    // Read-only syllabus access — authors, chairs (reviewers), admins, ovpaa.
    // authorizeSyllabusAccess() enforces per-record checks (author / assigned reviewer / admin).
    Route::middleware(['role:admin,faculty,ovpaa,chair'])->group(function () {
        // Review form preview (live)
        Route::get('/syllabus/{syllabus}/review-form/preview', [SyllabusReviewFormController::class, 'preview'])
            ->name('syllabus.review-form.preview');
        Route::get('/syllabus/saved/{completeSyllabus}/review-form/preview', [SyllabusReviewFormController::class, 'previewSaved'])
            ->name('syllabus.saved.review-form.preview');

        // Saved version previews & downloads — must be before {syllabus} wildcard
        Route::get('/syllabus/saved/{completeSyllabus}/preview', [SyllabusController::class, 'previewSavedComplete'])->name('syllabus.saved.complete.preview');
        Route::get('/syllabus/saved/{completeSyllabus}/abridged/preview', [SyllabusController::class, 'previewSavedAbridged'])->name('syllabus.saved.abridged.preview');
        Route::get('/syllabus/saved/{completeSyllabus}/assessment/preview', [SyllabusController::class, 'previewSavedAssessment'])->name('syllabus.saved.assessment.preview');
        Route::get('/syllabus/saved/{completeSyllabus}/download', [SyllabusController::class, 'downloadSavedComplete'])->name('syllabus.saved.complete.download');
        Route::get('/syllabus/saved/{completeSyllabus}/abridged/download', [SyllabusController::class, 'downloadSavedAbridged'])->name('syllabus.saved.abridged.download');
        Route::get('/syllabus/saved/{completeSyllabus}/assessment/download', [SyllabusController::class, 'downloadSavedAssessment'])->name('syllabus.saved.assessment.download');

        // Live syllabus — wildcard {syllabus} must come after all static-segment routes
        Route::get('/syllabus/{syllabus}', [SyllabusController::class, 'show'])->name('syllabus.show');
        Route::get('/syllabus/{syllabus}/preview/complete', [SyllabusController::class, 'previewComplete'])->name('syllabus.preview.complete');
        Route::get('/syllabus/{syllabus}/preview/abridged', [SyllabusController::class, 'previewAbridged'])->name('syllabus.preview.abridged');
        Route::get('/syllabus/{syllabus}/preview/assessment-plan', [SyllabusController::class, 'previewAssessment'])->name('syllabus.preview.assessment');
        Route::get('/syllabus/{syllabus}/preview/complete/download', [SyllabusController::class, 'downloadComplete'])->name('syllabus.preview.complete.download');
        Route::get('/syllabus/{syllabus}/preview/abridged/download', [SyllabusController::class, 'downloadAbridged'])->name('syllabus.preview.abridged.download');
        Route::get('/syllabus/{syllabus}/preview/assessment-plan/download', [SyllabusController::class, 'downloadAssessment'])->name('syllabus.preview.assessment.download');

        // Reviewer action page — chair/member fills checklist and records decision
        Route::get('/syllabus/{syllabus}/review', [\App\Http\Controllers\Syllabus\ReviewQueueController::class, 'show'])
            ->name('syllabus.reviewer.show');
    });

});
