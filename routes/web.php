<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountApprovalController;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\AcademicStructureController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    // Keep a single source of truth for the auth view
    return redirect()->route('auth.show');
});


Route::get('/auth', [AuthController::class, 'show'])->name('auth.show');

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');

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

    Route::get('/account-approval', [AccountApprovalController::class, 'index'])->name('accounts.approval');

    Route::post('/account-approval/approve', [AccountApprovalController::class, 'approve']);
    Route::post('/account-approval/reject', [AccountApprovalController::class, 'reject']);
    Route::post('/account-approval/restore', [AccountApprovalController::class, 'restore']);
    Route::post('/account-approval/disable', [AccountApprovalController::class, 'disable']);
    Route::post('/account-approval/assign-role', [AccountApprovalController::class, 'assignRole'])->name('account-approval.assign-role');

    Route::get('/academic-structure', [AcademicStructureController::class, 'index'])->name('academic.structure.index');
    Route::post('/academic-structure/college', [AcademicStructureController::class, 'storeCollege'])->name('college.store');
    Route::post('/academic-structure/department', [AcademicStructureController::class, 'storeDepartment'])->name('department.store');
    Route::post('/academic-structure/program', [AcademicStructureController::class, 'storeProgram'])->name('program.store');

});
