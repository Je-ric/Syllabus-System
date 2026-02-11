<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

class OTPController extends Controller
{
    public function showOTP()
    {
        if (!session('verify_email')) {
            return redirect()->route('login');
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

        $this->generateAndSendOtp($user);

        return redirect()
            ->route('otp.show')
            ->with('verify_email', $user->email)
            ->with('success', 'A new OTP has been sent to your email.');
    }


    // Verify OTP
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $email = $request->input('email') ?? session('verify_email');

        if (!$email) {
            return redirect()->route('login')
                ->withErrors(['email' => 'No email to verify.']);
        }

        $user = User::where('email', $email)->firstOrFail();

        // Null OTP safety
        if (!$user->otp) {
            return back()->withErrors([
                'otp' => 'OTP is invalid or has already been used.',
            ]);
        }

        // OTP expiration check
        if ($user->otp_expires_at && now()->greaterThan($user->otp_expires_at)) {
            return back()->withErrors([
                'otp' => 'OTP has expired. Please request a new one.',
            ]);
        }

        //  OTP match check
        if (!Hash::check($request->otp, $user->otp)) {
            return back()->withErrors([
                'otp' => 'Invalid OTP.',
            ]);
        }

        // After the OTP insert, mark email as verified, the otp and expiry as null
        $user->update([
            'email_verified_at' => now(),
            'otp' => null,
            'otp_expires_at' => null,
        ]);

        // Clear session
        $request->session()->forget('verify_email');

        return redirect()
            ->route('waiting.approval')
            ->with('success', 'Email verified! Await admin approval.');
    }

    // Generate and send OTP (DRY)
    private function generateAndSendOtp(User $user)
    {
        $otp = rand(100000, 999999);

        $user->update([
            'otp' => Hash::make($otp),
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(new OtpMail($otp));
    }
}
