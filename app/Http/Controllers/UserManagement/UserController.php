<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\UserConsultationHour;
use App\Rules\NoInjectionRule;
use App\Services\Authentication\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private const PASSWORD_CHANGE_OTP_SESSION_KEY = 'password_change_otp';

    public function __construct(private OtpService $otpService) {}

    public function index()
    {
        $user = User::with([
            'roles',
            'assignments.department.college',
            'assignments.college',
            'consultationHours',
        ])->findOrFail(Auth::id());

        $recentActivity = AuditLog::where('user_id', $user->id)
            ->orderByDesc('timestamp')
            ->limit(20)
            ->get();

        return view('Authentication.viewDetails', compact('user', 'recentActivity'));
    }

    public function storeConsultationHour(Request $request)
    {
        $validated = $request->validate([
            'day'  => ['required', 'in:Monday,Tuesday,Wednesday,Thursday,Friday'],
            'time' => ['required', 'string', 'max:100', new NoInjectionRule()],
        ]);

        UserConsultationHour::create([
            'user_id' => Auth::id(),
            'day'     => $validated['day'],
            'time'    => $validated['time'],
        ]);

        return back()->with('toast', ['message' => 'Consultation hour added.', 'type' => 'success']);
    }

    public function destroyConsultationHour(UserConsultationHour $hour)
    {
        abort_if($hour->user_id !== Auth::id(), 403);
        $hour->delete();
        return back()->with('toast', ['message' => 'Consultation hour removed.', 'type' => 'success']);
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
            'name'         => ['required', 'string', 'min:2', 'max:255', 'regex:/^[\p{L}\s]+$/u', new NoInjectionRule()],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone_number' => ['nullable', 'string', 'max:30', 'regex:/^[0-9\s\-\+\(\)]*$/'],
            'office'       => ['nullable', 'string', 'max:255', 'regex:/^[\p{L}\s\-\.\,0-9]*$/u', new NoInjectionRule()],
        ], [
            'name.regex' => 'Name must contain letters and spaces only — no numbers or special characters.',
            'name.min' => 'Name must be at least 2 characters.',
            'phone_number.regex' => 'Phone number can only contain digits, spaces, and standard phone characters.',
            'office.regex' => 'Office can only contain letters, numbers, spaces, and basic punctuation.',
        ]);

        $user->update($validated);

        AuditLog::record(
            action: 'updated',
            module: 'Profile',
            referenceId: $user->id,
            description: "User updated their profile."
        );

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
