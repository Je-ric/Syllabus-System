<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountApprovalController;
use App\Http\Controllers\AcademicStructureController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\ObjectiveController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\AcademicCalendarController;
use App\Http\Controllers\AcademicCalendarEventController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\SyllabusController;
use App\Http\Controllers\OrganizationalHierarchyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\WorkloadController;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('landing');
})->name('home');


Route::get('/auth', [AuthController::class, 'show'])->name('auth.show');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/waiting-approval', function () {
    return view('Authentication.waiting-approval');
})->name('waiting.approval');


Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [UserController::class,'index'])->name('profile.index');
    Route::put('/profile', [UserController::class,'update'])->name('profile.update');
    Route::post('/profile/password', [UserController::class,'changePassword'])->name('profile.password.change');
    Route::post('/profile/password/verify-otp', [UserController::class,'verifyPasswordOtp'])->name('profile.password.verify-otp');
    Route::post('/profile/password/resend-otp', [UserController::class,'resendPasswordOtp'])->name('profile.password.resend-otp');
    Route::post('/profile/consultation-hours', [UserController::class,'storeConsultationHour'])->name('profile.consultation.store');
    Route::delete('/profile/consultation-hours/{hour}', [UserController::class,'destroyConsultationHour'])->name('profile.consultation.destroy');

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/account-approval', [AccountApprovalController::class, 'index'])->name('accounts.approval');
        Route::post('/account-approval/approve', [AccountApprovalController::class, 'approve'])->name('account-approval.approve');
        Route::post('/account-approval/reject', [AccountApprovalController::class, 'reject'])->name('account-approval.reject');
        Route::post('/account-approval/restore', [AccountApprovalController::class, 'restore'])->name('account-approval.restore');
        Route::post('/account-approval/disable', [AccountApprovalController::class, 'disable'])->name('account-approval.disable');
        Route::post('/account-approval/assign-role', [AccountApprovalController::class, 'assignRole'])->name('account-approval.assign-role');
        Route::put('/account-approval/edit-user', [AccountApprovalController::class, 'editUser'])->name('account-approval.edit-user');

        Route::get('/academic-structure', [AcademicStructureController::class, 'index'])->name('academic.structure.index');
        Route::post('/academic-structure/college', [AcademicStructureController::class, 'storeCollege'])->name('college.store');
        Route::put('/academic-structure/college/{college}', [AcademicStructureController::class, 'updateCollege'])->name('college.update');
        Route::delete('/academic-structure/college/{college}', [AcademicStructureController::class, 'destroyCollege'])->name('college.destroy');
        Route::post('/academic-structure/department', [AcademicStructureController::class, 'storeDepartment'])->name('department.store');
        Route::put('/academic-structure/department/{department}', [AcademicStructureController::class, 'updateDepartment'])->name('department.update');
        Route::delete('/academic-structure/department/{department}', [AcademicStructureController::class, 'destroyDepartment'])->name('department.destroy');
        Route::post('/academic-structure/program', [AcademicStructureController::class, 'storeProgram'])->name('program.store');
        Route::put('/academic-structure/program/{program}', [AcademicStructureController::class, 'updateProgram'])->name('program.update');
        Route::delete('/academic-structure/program/{program}', [AcademicStructureController::class, 'destroyProgram'])->name('program.destroy');

        Route::get('/organizational/colleges', [OrganizationalHierarchyController::class, 'collegesIndex'])->name('organizational.colleges.index');
        Route::post('/organizational/assign-dean', [OrganizationalHierarchyController::class, 'assignDean'])->name('organizational.assign-dean');
        Route::post('/organizational/remove-dean', [OrganizationalHierarchyController::class, 'removeDean'])->name('organizational.remove-dean');

        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit.logs.index');

    });

    Route::middleware(['role:admin,ovpaa'])->group(function () {
        Route::get('/academic-calendars', [AcademicCalendarController::class, 'index'])->name('academic.calendars.index');
        Route::get('/academic-calendars/create', [AcademicCalendarController::class, 'create'])->name('academic.calendars.create');
        Route::post('/academic-calendars', [AcademicCalendarController::class, 'store'])
            ->middleware(HandlePrecognitiveRequests::class)
            ->name('academic.calendars.store');
        Route::get('/academic-calendars/{academicYear}/edit', [AcademicCalendarController::class, 'edit'])->name('academic.calendars.edit');
        Route::put('/academic-calendars/{academicYear}', [AcademicCalendarController::class, 'update'])
            ->middleware(HandlePrecognitiveRequests::class)
            ->name('academic.calendars.update');
        Route::delete('/academic-calendars/{academicYear}', [AcademicCalendarController::class, 'destroy'])->name('academic.calendars.destroy');
        Route::post('/academic-calendars/{academicYear}/set-active', [AcademicCalendarController::class, 'setActive'])->name('academic.calendars.set-active');

        Route::get('/academic-calendars/{academicYear}/events', [AcademicCalendarEventController::class, 'index'])->name('academic.calendar.events.index');
        Route::post('/academic-calendars/{semester}/events', [AcademicCalendarEventController::class, 'store'])->name('academic.calendar.events.store');
        Route::put('/academic-calendars/events/{event}', [AcademicCalendarEventController::class, 'update'])->name('academic.calendar.events.update');
        Route::delete('/academic-calendars/events/{event}', [AcademicCalendarEventController::class, 'destroy'])->name('academic.calendar.events.destroy');
    });

    Route::middleware(['role:admin,dean,chair'])->group(function () {
        Route::get('/organizational/hierarchy', [OrganizationalHierarchyController::class, 'hierarchyView'])->name('organizational.hierarchy');
        Route::get('/organizational/college/{collegeId}/departments', [OrganizationalHierarchyController::class, 'departmentsIndex'])->name('organizational.departments.index');
    });

    Route::middleware(['role:admin'])->group(function () {
        Route::post('/organizational/assign-chair', [OrganizationalHierarchyController::class, 'assignChair'])->name('organizational.assign-chair');
        Route::post('/organizational/remove-chair', [OrganizationalHierarchyController::class, 'removeChair'])->name('organizational.remove-chair');
        Route::post('/organizational/assign-faculty', [OrganizationalHierarchyController::class, 'assignFaculty'])->name('organizational.assign-faculty');
        Route::post('/organizational/remove-faculty', [OrganizationalHierarchyController::class, 'removeFaculty'])->name('organizational.remove-faculty');
    });

    Route::middleware(['role:admin,dean'])->group(function () {
        Route::get('/college/goals', [GoalController::class, 'goal_index'])->name('goal.index');
        Route::post('/college/goals', [GoalController::class, 'goal_store'])->name('goal.store');
        Route::put('/college/goals/{goal}', [GoalController::class, 'goal_update'])->name('goal.update');
        Route::delete('/college/goals/{goal}', [GoalController::class, 'goal_destroy'])->name('goal.destroy');
    });

    Route::middleware(['role:admin,chair'])->group(function () {
        Route::get('/department/objectives', [ObjectiveController::class, 'objective_index'])->name('objective.index');
        Route::post('/department/objectives', [ObjectiveController::class, 'objective_store'])->name('objective.store');
        Route::put('/department/objectives/{objective}', [ObjectiveController::class, 'objective_update'])->name('objective.update');
        Route::delete('/department/objectives/{objective}', [ObjectiveController::class, 'objective_destroy'])->name('objective.destroy');

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

    Route::middleware(['role:admin,faculty,ovpaa'])->group(function () {
        Route::get('/syllabus', [SyllabusController::class, 'index'])->name('syllabus.index');
        Route::get('/syllabus/create', [SyllabusController::class, 'create'])->name('syllabus.create');
        Route::get('/syllabus/courses/{programId}', [SyllabusController::class, 'showCourses'])->name('syllabus.courses');
        Route::get('/syllabus/wizard', [SyllabusController::class, 'wizard'])->name('syllabus.wizard');
        Route::get('/syllabus/form/{courseId}', [SyllabusController::class, 'showForm'])->name('syllabus.form');
        Route::post('/syllabus', [SyllabusController::class, 'store'])->name('syllabus.store');

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
        Route::get('/syllabus/{syllabus}/edit', [SyllabusController::class, 'edit'])->name('syllabus.edit');
        Route::put('/syllabus/{syllabus}', [SyllabusController::class, 'update'])->name('syllabus.update');
        Route::delete('/syllabus/{syllabus}', [SyllabusController::class, 'destroy'])->name('syllabus.destroy');

        Route::view('/showcase', 'components.showcase');
    });

});
