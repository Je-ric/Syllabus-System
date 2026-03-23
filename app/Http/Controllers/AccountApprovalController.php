<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AccountApprovalService;


class AccountApprovalController extends Controller
{
    public function __construct(protected AccountApprovalService $accountApprovalService) { }

    public function index()
    {
        return view('AccountApproval.index');
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

        $this->accountApprovalService->restore((int) $request->input('user_id'));

        return redirect()->route('accounts.approval')
            ->with('toast', [
                'message' => 'User account restored to pending.',
                'type' => 'success'
            ]);
    }

    public function disable(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $this->accountApprovalService->disable((int) $request->input('user_id'));

        return redirect()->route('accounts.approval')
            ->with('toast', [
                'message' => 'User account disabled.',
                'type' => 'success'
            ]);
    }

    // public function changeStatus(Request $request)
    // {
    //     $request->validate([
    //         'user_id' => 'required|exists:users,id',
    //         'status'  => 'required|in:pending,active,rejected,disabled',
    //     ]);

    //     $user = User::findOrFail($request->user_id);
    //     $user->update(['account_status' => $request->status]);

    //     return redirect()->route('accounts.approval');
    // }


    public function assignRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'roles'   => 'required|array',
            'roles.*' => 'in:admin,chair,dean,faculty',
        ]);

        try {
            $user = $this->accountApprovalService->assignRoles(
                (int) $request->input('user_id'),
                $request->input('roles', [])
            );
        } catch (\Throwable $e) {
            return redirect()
                ->route('accounts.approval')
                ->withErrors([
                    'error' => 'Failed to assign roles. Please try again.',
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
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255|unique:users,email,' . $request->input('user_id'),
            'phone_number' => 'nullable|string|max:30',
            'office'       => 'nullable|string|max:255',
        ]);

        $user = \App\Models\User::findOrFail($request->input('user_id'));
        $user->update($request->only('name', 'email', 'phone_number', 'office'));

        return redirect()->route('accounts.approval')
            ->with('toast', ['message' => "User {$user->name} updated successfully.", 'type' => 'success']);
    }
}
