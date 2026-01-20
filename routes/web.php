<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountApprovalController;
use Illuminate\Support\Facades\Auth;


Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return view('Authentication.auth');
});


Route::get('/auth', [AuthController::class, 'show'])->name('auth.show');

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::post('/verify-otp', [AuthController::class, 'verifyOTP'])->name('verify.otp');
Route::post('/request-otp', [AuthController::class, 'requestOTP'])->name('request.otp');

Route::get('/waiting-approval', function () {
    return view('Authentication.waiting-approval');
})->name('waiting.approval');


Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/account-approval', [AccountApprovalController::class, 'index'])->name('accounts.approval');

Route::post('/account-approval/approve', [AccountApprovalController::class, 'approve']);
Route::post('/account-approval/reject', [AccountApprovalController::class, 'reject']);
Route::post('/account-approval/restore', [AccountApprovalController::class, 'restore']);
Route::post('/account-approval/disable', [AccountApprovalController::class, 'disable']);
Route::post('/account-approval/assign-role', [AccountApprovalController::class, 'assignRole'])->name('account-approval.assign-role');
