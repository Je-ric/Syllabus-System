<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

class AuthController extends Controller
{
    // Show login/register page
    public function show()
    {
        return view('Authentication.auth');
    }

    // Registration
    public function register(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'unique:users',
                function ($attribute, $value, $fail) {
                    if (
                        !str_ends_with($value, '@clsu.edu.ph') &&
                        !str_ends_with($value, '@clsu2.edu.ph')
                    ) {
                        $fail('Only CLSU email addresses are allowed.');
                    }
                },
            ],
            'password' => 'required|min:6|confirmed',
        ]);

        // Generate OTP
        $otp = rand(100000, 999999);

        $user = User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'account_status' => 'pending',
            'otp'            => Hash::make($otp),
        ]);

        // Send OTP email
        Mail::to($user->email)->send(new OtpMail($otp));

        // Store email in session for OTP verification form
        return redirect()
            ->route('auth.show')
            ->with('verify_email', $user->email)
            ->with('success', 'Account created. Please verify your email with the OTP sent.');
    }

    // Verify OTP
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $email = $request->input('email') ?? $request->session()->get('verify_email');
        if (!$email) {
            return redirect()->route('auth.show')->with('error', 'No email to verify.');
        }


        $user = User::where('email', $email)->firstOrFail();

        // Check OTP match
        if (!Hash::check($request->otp, $user->otp)) {
            return back()->withErrors(['otp' => 'Invalid OTP.']);
        }

        // Mark email as verified
        $user->email_verified_at = now();
        $user->otp = null;
        $user->save();

        // Remove verify_email from session
        $request->session()->forget('verify_email');

        // Redirect to waiting approval page
        return redirect()->route('waiting.approval')->with('success', 'Email verified! Await admin approval.');
    }

    // Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if (!$user->email_verified_at) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Please verify your email first.',
                ]);
            }

            if ($user->account_status !== 'active') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account is not active. Await admin approval.',
                ]);
            }

            if ($user->email_verified_at && $user->account_status !== 'active') {
                return redirect()->route('waiting.approval');
            }

            return redirect('/dashboard'); // change as needed
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ]);
    }

    // Request OTP (resend)
    public function requestOTP(Request $request)
    {
        $email = $request->session()->get('verify_email');
        if (!$email) {
            return redirect()->route('auth.show')->with('error', 'No email to resend OTP.');
        }

        $user = User::where('email', $email)->firstOrFail();

        if ($user->email_verified_at) {
            return back()->with('success', 'Email is already verified.');
        }

        $otp = rand(100000, 999999);
        $user->otp = Hash::make($otp);
        $user->save();

        Mail::to($user->email)->send(new OtpMail($otp));

        return back()->with('success', 'OTP sent to your email.');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.show');
    }
}
