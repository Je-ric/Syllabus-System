<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use App\Services\CaisApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Log;

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
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $email    = $request->email;
        $password = $request->password;

        // --- Attempt CAIS authentication first ---
        /** @var CaisApiService $cais */
        $cais   = app(CaisApiService::class);
        $result = $cais->verifyUser($email, $password);

        if ($result !== null) {
            // CAIS confirmed — find or create the local user row
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name'              => trim(($result['user']['first_name'] ?? '') . ' ' . ($result['user']['last_name'] ?? '')),
                    'password'          => Hash::make($password),
                    'account_status'    => 'active',
                    'email_verified_at' => now(),
                ]
            );

            // Ensure active — a previously pending/rejected CAIS user should be activated
            if ($user->account_status !== 'active') {
                $user->update(['account_status' => 'active']);
            }

            $request->session()->put('cais_token', $result['token']);
            $user->syncFromCais($result['user']);
            Auth::login($user, $request->filled('remember'));
            $request->session()->regenerate();

            AuditLog::record(
                action: 'login',
                module: 'Authentication',
                referenceId: $user->id,
                description: "User {$user->name} ({$user->email}) logged in via CAIS."
            );

            return redirect()->intended(route('syllabus.index'));
        }

        // --- CAIS unavailable or rejected — fall back to local auth ---
        $user = User::where('email', $email)->first();

        if (! $user || ! Auth::attempt(['email' => $email, 'password' => $password], $request->filled('remember'))) {
            return redirect()->route('auth.show')
                ->with('toast', ['message' => 'Invalid email or password.', 'type' => 'error'])
                ->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        /** @var User $user */
        $user = Auth::user();

        if ($user->account_status === 'pending') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('waiting.approval')
                ->with('toast', ['message' => 'Your account is pending admin approval.', 'type' => 'info']);
        }

        if (in_array($user->account_status, ['rejected', 'disabled'])) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $msg = $user->account_status === 'rejected'
                ? 'Your account registration was rejected.'
                : 'Your account has been disabled by an administrator.';
            return redirect()->route('auth.show')
                ->with('toast', ['message' => $msg, 'type' => 'error'])
                ->withInput($request->only('email'));
        }

        AuditLog::record(
            action: 'login',
            module: 'Authentication',
            referenceId: $user->id,
            description: "User {$user->name} ({$user->email}) logged in (local fallback)."
        );

        return redirect()->intended(route('syllabus.index'));
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
        $request->session()->forget('cais_token');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.show');
    }
}


// Login
    // public function login(Request $request)
    // {
    //     $credentials = $request->validate([
    //         'email'    => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     if (Auth::attempt($credentials, $request->filled('remember'))) {
    //         $request->session()->regenerate();

    //         /** @var User $user */
    //         $user = Auth::user();

    //         AuditLog::record(
    //             action: 'login',
    //             module: 'Authentication',
    //             referenceId: $user->id,
    //             description: "User {$user->name} ({$user->email}) logged in."
    //         );

    //         switch ($user->account_status) {
    //             case 'active':
    //                 return redirect()->intended(route('syllabus.index'));
    //             case 'pending':
    //                 Auth::logout();
    //                 $request->session()->invalidate();
    //                 $request->session()->regenerateToken();
    //                 return redirect()->route('waiting.approval')
    //                     ->with('toast', [
    //                         'message' => 'Your account is pending admin approval.',
    //                         'type' => 'info',
    //                     ]);
    //             case 'rejected':
    //                 Auth::logout();
    //                 $request->session()->invalidate();
    //                 $request->session()->regenerateToken();
    //                 return redirect()->route('auth.show')
    //                     ->with('toast', [
    //                         'message' => 'Your account registration was rejected.',
    //                         'type' => 'error',
    //                     ])
    //                     ->withInput($request->only('email'));
    //             case 'disabled':
    //                 Auth::logout();
    //                 $request->session()->invalidate();
    //                 $request->session()->regenerateToken();
    //                 return redirect()->route('auth.show')
    //                     ->with('toast', [
    //                         'message' => 'Your account has been disabled by an administrator.',
    //                         'type' => 'error',
    //                     ])
    //                     ->withInput($request->only('email'));
    //             default:
    //                 Auth::logout();
    //                 $request->session()->invalidate();
    //                 $request->session()->regenerateToken();
    //                 return redirect()->route('auth.show')
    //                     ->with('toast', [
    //                         'message' => 'Your account is in an unrecognized state. Please contact support.',
    //                         'type' => 'error',
    //                     ])
    //                     ->withInput($request->only('email'));
    //         }
    //     }

    //     return redirect()->route('auth.show')
    //         ->with('toast', ['message' => 'Invalid email or password.', 'type' => 'error'])
    //         ->withInput($request->only('email'));
    // }
