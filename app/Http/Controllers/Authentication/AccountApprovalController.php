<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Rules\NoInjectionRule;
use App\Services\Authentication\AccountApprovalService;
use Illuminate\Support\Facades\Auth;


class AccountApprovalController extends Controller
{
    public function __construct(protected AccountApprovalService $accountApprovalService) { }

    public function index()
    {
        return view('Authentication.AccountApproval.index');
    }

    public function approve(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        try {
            $this->accountApprovalService->approve((int) $request->input('user_id'));

            return redirect()->route('accounts.approval')->with('toast', [
                'message' => 'User approved successfully.',
                'type' => 'success',
            ]);
        } catch (\Exception $e) {
            return redirect()->route('accounts.approval')->withErrors([
                'error' => 'An error occurred while approving the user. Please try again.',
            ]);
        }
    }

    public function reject(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        try {
            $this->accountApprovalService->reject((int) $request->input('user_id'));

            return redirect()->route('accounts.approval')->with('toast', [
                'message' => 'User rejected successfully.',
                'type' => 'success',
            ]);
        } catch (\Exception $e) {
            return redirect()->route('accounts.approval')->withErrors([
                'error' => 'An error occurred while rejecting the user. Please try again.',
            ]);
        }
    }

    public function restore(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        try {
            $this->accountApprovalService->restore((int) $request->input('user_id'));

            return redirect()->route('accounts.approval')
                ->with('toast', [
                    'message' => 'User account restored to pending.',
                    'type' => 'success',
                ]);
        } catch (\Exception $e) {
            return redirect()->route('accounts.approval')->withErrors([
                'error' => 'An error occurred while restoring the user. Please try again.',
            ]);
        }
    }

    public function disable(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        try {
            $this->accountApprovalService->disable((int) $request->input('user_id'));

            return redirect()->route('accounts.approval')
                ->with('toast', [
                    'message' => 'User account disabled.',
                    'type' => 'success',
                ]);
        } catch (\Exception $e) {
            return redirect()->route('accounts.approval')->withErrors([
                'error' => 'An error occurred while disabling the user. Please try again.',
            ]);
        }
    }

    public function assignRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'roles'   => 'required|array',
            'roles.*' => 'in:admin,chair,dean,faculty,ovpaa',
        ]);

        $roles = $request->input('roles', []);

        if (in_array('dean', $roles) && in_array('chair', $roles)) {
            return redirect()
                ->route('accounts.approval')
                ->with('toast', [
                    'message' => 'A user cannot hold both Dean and Chair roles simultaneously.',
                    'type' => 'error',
                ]);
        }

        try {
            $user = $this->accountApprovalService->assignRoles(
                (int) $request->input('user_id'),
                $roles
            );
        } catch (\Throwable $e) {
            return redirect()
                ->route('accounts.approval')
                ->with('toast', [
                    'message' => 'Failed to assign roles. Please try again.',
                    'type' => 'error',
                ]);
        }

        return redirect()
            ->route('accounts.approval')
            ->with('toast', [
                'message' => "Roles assigned to {$user->name} successfully.",
                'type' => 'success'
            ]);
    }

    public function editUser(Request $request)
    {
        $request->validate([
            'user_id'      => 'required|exists:users,id',
            'name'         => ['required', 'string', 'min:2', 'max:255', 'regex:/^[\p{L}\s]+$/u', new NoInjectionRule()],
            'email'        => [
                'required',
                'email',
                'max:255',
                'regex:/@(clsu\.edu\.ph|clsu2\.edu\.ph)$/i',
                \Illuminate\Validation\Rule::unique('users', 'email')->ignore($request->input('user_id')),
            ],
            'phone_number' => 'nullable|string|max:30|regex:/^[0-9\s\-\+\(\)]*$/',
            'office'       => ['nullable', 'string', 'max:255', 'regex:/^[\p{L}\s\-\.\,0-9]*$/u', new NoInjectionRule()],
        ], [
            'email.regex' => 'Email must be a @clsu.edu.ph or @clsu2.edu.ph address.',
            'email.unique' => 'This email is already in use by another account.',
            'name.regex' => 'Name must contain letters and spaces only — no numbers or special characters.',
            'name.min' => 'Name must be at least 2 characters.',
            'phone_number.regex' => 'Phone number can only contain digits, spaces, and standard phone characters.',
            'office.regex' => 'Office can only contain letters, numbers, spaces, and basic punctuation.',
        ]);

        // Only admins may edit other users' accounts
        // Route is already admin-only, but guard explicitly as a safety net
        /** @var \App\Models\User $admin */
        $admin = Auth::user();
        if (! $admin->hasRole('admin')) {
            abort(403);
        }

        $user = \App\Models\User::findOrFail($request->input('user_id'));
        $user->update($request->only('name', 'email', 'phone_number', 'office'));

        AuditLog::record(
            action: 'updated',
            module: 'Account Approval',
            referenceId: $user->id,
            description: "Admin edited user {$user->name} ({$user->email})."
        );

        return redirect()->route('accounts.approval')
            ->with('toast', ['message' => "User {$user->name} updated successfully.", 'type' => 'success']);
    }
}
