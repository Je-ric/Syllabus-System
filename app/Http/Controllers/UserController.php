<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private const PASSWORD_CHANGE_OTP_SESSION_KEY = 'password_change_otp';

    public function __construct(private OtpService $otpService)
    {
    }

    public function index()
    {
        $user = User::with([
            'roles',
            'assignments.department.college',
            'assignments.college',
        ])->findOrFail(Auth::id());

        return view('Authentication.viewDetails', compact('user'));
    }

    public function update(Request $request)
    {
        $user = User::findOrFail(Auth::id());

        if ($user->hasRole('admin')) {
            return redirect()
                ->route('profile.index')
                ->with('toast', [
                    'message' => 'Admin profile details cannot be edited here.',
                    'type' => 'warning',
                ]);
        }

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'office'       => ['nullable', 'string', 'max:255'],
        ]);

        $user->update($validated);

        return redirect()
            ->route('profile.index')
            ->with('toast', [
                'message' => 'Profile details updated successfully.',
                'type' => 'success',
            ]);
    }

    public function changePassword(Request $request)
    {
        $user = User::findOrFail(Auth::id());

        if ($user->hasRole('admin')) {
            return redirect()
                ->route('profile.index')
                ->with('toast', [
                    'message' => 'Admin password cannot be changed from this page.',
                    'type' => 'warning',
                ]);
        }

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'Current password is incorrect.',
            ]);
        }

        $mailSent = $this->otpService->issueForUser($user, OtpService::PURPOSE_PASSWORD_CHANGE);

        $request->session()->put(self::PASSWORD_CHANGE_OTP_SESSION_KEY, [
            'user_id' => $user->id,
            'password_hash' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('profile.index')
            ->with('toast', [
                'message' => $mailSent
                    ? 'OTP sent to your email. Enter it below to confirm password change.'
                    : 'Could not send OTP email. Please try resending.',
                'type' => $mailSent ? 'info' : 'warning',
            ]);
    }

    public function verifyPasswordOtp(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $pending = $request->session()->get(self::PASSWORD_CHANGE_OTP_SESSION_KEY);

        if (!$pending || (int) ($pending['user_id'] ?? 0) !== (int) $user->id) {
            return redirect()
                ->route('profile.index')
                ->with('toast', [
                    'message' => 'No pending password change request found.',
                    'type' => 'warning',
                ]);
        }

        $this->otpService->migrateLegacyOtp($user, OtpService::PURPOSE_PASSWORD_CHANGE);

        $validated = $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $otpError = $this->otpService->validate($user, $validated['otp'], OtpService::PURPOSE_PASSWORD_CHANGE);
        if ($otpError) {
            return back()->withErrors([
                'otp' => $otpError,
            ]);
        }

        $user->forceFill([
            'password' => $pending['password_hash'],
        ])->save();
        $this->otpService->clear($user, OtpService::PURPOSE_PASSWORD_CHANGE);

        $request->session()->forget(self::PASSWORD_CHANGE_OTP_SESSION_KEY);

        return redirect()
            ->route('profile.index')
            ->with('toast', [
                'message' => 'Password changed successfully.',
                'type' => 'success',
            ]);
    }

    public function resendPasswordOtp(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $pending = $request->session()->get(self::PASSWORD_CHANGE_OTP_SESSION_KEY);

        if ($user->hasRole('admin')) {
            return redirect()
                ->route('profile.index')
                ->with('toast', [
                    'message' => 'Admin password cannot be changed from this page.',
                    'type' => 'warning',
                ]);
        }

        if (!$pending || (int) ($pending['user_id'] ?? 0) !== (int) $user->id) {
            return redirect()
                ->route('profile.index')
                ->with('toast', [
                    'message' => 'No pending password change request found.',
                    'type' => 'warning',
                ]);
        }

        $mailSent = $this->otpService->issueForUser($user, OtpService::PURPOSE_PASSWORD_CHANGE);

        return redirect()
            ->route('profile.index')
            ->with('toast', [
                'message' => $mailSent
                    ? 'A new OTP has been sent to your email.'
                    : 'Could not send OTP email. Please check your mail settings.',
                'type' => $mailSent ? 'success' : 'warning',
            ]);
    }
}
