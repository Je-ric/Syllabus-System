<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;

class OTPController extends Controller
{
    public function __construct(private OtpService $otpService)
    {
    }

    public function showOTP()
    {
        if (!session('verify_email')) {
            return redirect()->route('auth.show');
        }

        return view('Authentication.verifyOTP');
    }

    // Resend OTP using email input (cross-device / cross-day)
    public function resendOtpByEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'No account found with this email.',
            ]);
        }

        if ($user->email_verified_at) {
            return back()->withErrors([
                'email' => 'This email is already verified.',
            ]);
        }

        $mailSent = $this->otpService->issueForUser($user, OtpService::PURPOSE_EMAIL_VERIFICATION);

        $message = $mailSent
            ? 'A new OTP has been sent to your email.'
            : 'Could not send the OTP email. Please check your email address or try again later.';

        return redirect()
            ->route('otp.show')
            ->with('verify_email', $user->email)
            ->with('success', $message);
    }


    // Verify OTP
    public function verifyOTP(Request $request)
    {
        $validated = $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $email = $request->input('email') ?? session('verify_email');

        if (!$email) {
            return redirect()->route('auth.show')
                ->withErrors(['email' => 'No email to verify.']);
        }

        $user = User::where('email', $email)->firstOrFail();
        $this->otpService->migrateLegacyOtp($user, OtpService::PURPOSE_EMAIL_VERIFICATION);

        $otpError = $this->otpService->validate($user, $validated['otp'], OtpService::PURPOSE_EMAIL_VERIFICATION);
        if ($otpError) {
            return back()
                ->withErrors(['otp' => $otpError])
                ->with('verify_email', $email);
        }

        $user->update(['email_verified_at' => now()]);
        $this->otpService->clear($user, OtpService::PURPOSE_EMAIL_VERIFICATION);
        $request->session()->forget('verify_email');

        return redirect()
            ->route('waiting.approval')
            ->with('success', 'Email verified! Await admin approval.');
    }

}
