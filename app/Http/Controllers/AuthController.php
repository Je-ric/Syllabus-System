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
            'phone_number' => 'required|string|max:20',
            'office' => 'required|string|max:255',
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
            'otp_expires_at' => now()->addMinutes(10),
            'phone_number'   => $request->phone_number,
            'office'         => $request->office,
        ]);

        // Send OTP email
        Mail::to($user->email)->send(new OtpMail($otp));

        // Store email in session for OTP verification form
        return redirect()
            ->route('otp.show')
            ->with('verify_email', $user->email)
            ->with('success', 'Account created. Please verify your email.');
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
                    'email' => 'Please verify your email first using OTP.',
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



    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.show');
    }
}
