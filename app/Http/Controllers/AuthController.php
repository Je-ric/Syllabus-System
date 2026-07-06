<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

        $user = User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'account_status' => 'pending',
            'email_verified_at' => now(),
            'phone_number'   => $request->phone_number,
            'office'         => $request->office,
        ]);

        AuditLog::record(
            action: 'registered',
            module: 'Authentication',
            referenceId: $user->id,
            description: "New user registered: {$user->name} ({$user->email}).",
            userId: $user->id
        );

        return redirect()
            ->route('waiting.approval')
            ->with('success', 'Account created! Please wait for admin approval.');
    }

    // Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();

            AuditLog::record(
                action: 'login',
                module: 'Authentication',
                referenceId: $user->id,
                description: "User {$user->name} ({$user->email}) logged in."
            );

            switch ($user->account_status) {
                case 'active':
                    return redirect()->intended(route('syllabus.index'));
                case 'pending':
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return redirect()->route('waiting.approval')
                        ->with('toast', [
                            'message' => 'Your account is pending admin approval.',
                            'type' => 'info',
                        ]);
                case 'rejected':
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return redirect()->route('auth.show')
                        ->with('toast', [
                            'message' => 'Your account registration was rejected.',
                            'type' => 'error',
                        ])
                        ->withInput($request->only('email'));
                case 'disabled':
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return redirect()->route('auth.show')
                        ->with('toast', [
                            'message' => 'Your account has been disabled by an administrator.',
                            'type' => 'error',
                        ])
                        ->withInput($request->only('email'));
                default:
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return redirect()->route('auth.show')
                        ->with('toast', [
                            'message' => 'Your account is in an unrecognized state. Please contact support.',
                            'type' => 'error',
                        ])
                        ->withInput($request->only('email'));
            }
        }

        return redirect()->route('auth.show')
            ->with('toast', ['message' => 'Invalid email or password.', 'type' => 'error'])
            ->withInput($request->only('email'));
    }



    // Logout
    public function logout(Request $request)
    {
        /** @var User|null $user */
        $user = Auth::user();
        if ($user) {
            AuditLog::record(
                action: 'logout',
                module: 'Authentication',
                referenceId: $user->id,
                description: "User {$user->name} ({$user->email}) logged out."
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.show');
    }
}
