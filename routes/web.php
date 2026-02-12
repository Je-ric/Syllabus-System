<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountApprovalController;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\AcademicStructureController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\ObjectiveController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\AcademicCalendarController;
use App\Http\Controllers\AcademicCalendarEventController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\SyllabusController;
use App\Http\Controllers\OrganizationalHierarchyController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    // Keep a single source of truth for the auth view
    return redirect()->route('auth.show');
});


Route::get('/auth', [AuthController::class, 'show'])->name('auth.show');
// Route::post('/login', [AuthController::class, 'login'])->name('login');
// Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// OTP routes
Route::get('/show-otp', [OTPController::class, 'showOTP'])->name('otp.show');
Route::post('/verify-otp', [OTPController::class, 'verifyOTP'])->name('otp.verify');
Route::get('/resend-otp', function () {
    return view('Authentication.resendOTP');
})->name('otp.resend');
Route::post('/resend-otp', [OTPController::class, 'resendOtpByEmail'])->name('otp.resend.email');


Route::get('/waiting-approval', function () {
    return view('Authentication.waiting-approval');
})->name('waiting.approval');


Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/account-approval', [AccountApprovalController::class, 'index'])->name('accounts.approval');
        Route::post('/account-approval/approve', [AccountApprovalController::class, 'approve'])->name('account-approval.approve');
        Route::post('/account-approval/reject', [AccountApprovalController::class, 'reject'])->name('account-approval.reject');
        Route::post('/account-approval/restore', [AccountApprovalController::class, 'restore'])->name('account-approval.restore');
        Route::post('/account-approval/disable', [AccountApprovalController::class, 'disable'])->name('account-approval.disable');
        Route::post('/account-approval/assign-role', [AccountApprovalController::class, 'assignRole'])->name('account-approval.assign-role');

        Route::get('/academic-structure', [AcademicStructureController::class, 'index'])->name('academic.structure.index');
        Route::post('/academic-structure/college', [AcademicStructureController::class, 'storeCollege'])->name('college.store');
        Route::put('/academic-structure/college/{college}', [AcademicStructureController::class, 'updateCollege'])->name('college.update');
        Route::post('/academic-structure/department', [AcademicStructureController::class, 'storeDepartment'])->name('department.store');
        Route::put('/academic-structure/department/{department}', [AcademicStructureController::class, 'updateDepartment'])->name('department.update');
        Route::post('/academic-structure/program', [AcademicStructureController::class, 'storeProgram'])->name('program.store');
        Route::put('/academic-structure/program/{program}', [AcademicStructureController::class, 'updateProgram'])->name('program.update');

        Route::get('/academic-calendars', [AcademicCalendarController::class, 'index'])->name('academic.calendars.index');
        Route::get('/academic-calendars/create', [AcademicCalendarController::class, 'create'])->name('academic.calendars.create');
        Route::post('/academic-calendars', [AcademicCalendarController::class, 'store'])->name('academic.calendars.store');
        Route::get('/academic-calendars/{academicYear}/edit', [AcademicCalendarController::class, 'edit'])->name('academic.calendars.edit');
        Route::put('/academic-calendars/{academicYear}', [AcademicCalendarController::class, 'update'])->name('academic.calendars.update');
        Route::delete('/academic-calendars/{academicYear}', [AcademicCalendarController::class, 'destroy'])->name('academic.calendars.destroy');

        Route::get('/academic-calendars/{academicYear}/events', [AcademicCalendarEventController::class, 'index'])->name('academic.calendar.events.index');
        Route::post('/academic-calendars/{semester}/events', [AcademicCalendarEventController::class, 'store'])->name('academic.calendar.events.store');
        Route::put('/academic-calendars/events/{event}', [AcademicCalendarEventController::class, 'update'])->name('academic.calendar.events.update');
        Route::delete('/academic-calendars/events/{event}', [AcademicCalendarEventController::class, 'destroy'])->name('academic.calendar.events.destroy');

        Route::get('/organizational/colleges', [OrganizationalHierarchyController::class, 'collegesIndex'])->name('organizational.colleges.index');
        Route::post('/organizational/assign-dean', [OrganizationalHierarchyController::class, 'assignDean'])->name('organizational.assign-dean');
        Route::post('/organizational/remove-dean', [OrganizationalHierarchyController::class, 'removeDean'])->name('organizational.remove-dean');
        Route::get('/organizational/college/{collegeId}/departments', [OrganizationalHierarchyController::class, 'departmentsIndex'])->name('organizational.departments.index');
        Route::post('/organizational/assign-chair', [OrganizationalHierarchyController::class, 'assignChair'])->name('organizational.assign-chair');
        Route::post('/organizational/remove-chair', [OrganizationalHierarchyController::class, 'removeChair'])->name('organizational.remove-chair');
        Route::post('/organizational/assign-faculty', [OrganizationalHierarchyController::class, 'assignFaculty'])->name('organizational.assign-faculty');
        Route::post('/organizational/remove-faculty', [OrganizationalHierarchyController::class, 'removeFaculty'])->name('organizational.remove-faculty');

    });

    Route::middleware(['role:admin,dean,chair'])->group(function () {
        Route::get('/organizational/hierarchy', [OrganizationalHierarchyController::class, 'hierarchyView'])->name('organizational.hierarchy');

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
        Route::get('/courses/{id}', [CourseController::class, 'show'])->name('courses.show');
        Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
        Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
        Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
    });

    Route::middleware(['role:admin,faculty'])->group(function () {
        Route::get('/syllabus', [SyllabusController::class, 'index'])->name('syllabus.index');
        Route::get('/syllabus/create', [SyllabusController::class, 'create'])->name('syllabus.create');
        Route::get('/syllabus/courses/{programId}', [SyllabusController::class, 'showCourses'])->name('syllabus.courses');
        Route::get('/syllabus/wizard', [SyllabusController::class, 'wizard'])->name('syllabus.wizard');
        Route::get('/syllabus/form/{courseId}', [SyllabusController::class, 'showForm'])->name('syllabus.form');
        Route::post('/syllabus', [SyllabusController::class, 'store'])->name('syllabus.store');
        Route::get('/syllabus/{syllabus}', [SyllabusController::class, 'show'])->name('syllabus.show');
        Route::get('/syllabus/{syllabus}/preview', [SyllabusController::class, 'preview'])->name('syllabus.preview');
        Route::get('/syllabus/{syllabus}/edit', [SyllabusController::class, 'edit'])->name('syllabus.edit');
        Route::put('/syllabus/{syllabus}', [SyllabusController::class, 'update'])->name('syllabus.update');
        Route::delete('/syllabus/{syllabus}', [SyllabusController::class, 'destroy'])->name('syllabus.destroy');
    });

});

