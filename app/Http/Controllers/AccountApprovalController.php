<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\UserAssignment;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountStatusUpdated;
use Illuminate\Support\Facades\DB;


class AccountApprovalController extends Controller
{
    public function index()
    {
        // $users = User::with('roles')->get();

        // return view(
        //     'AccountApproval.index',
        //     compact('users')
        // );

        return view('AccountApproval.index');
    }

    public function approve(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        DB::beginTransaction();

        try {
            $user = User::findOrFail($request->input('user_id'));
            $user->account_status = 'active';
            $user->save();

            $facultyRole = Role::where('name', '=', 'faculty')->first();

            if ($facultyRole && !$user->roles()->wherePivot('role_id', $facultyRole->id)->exists()) {
                $user->roles()->attach($facultyRole->id);
            }

            // EMAIL
            Mail::to($user->email)->send(new AccountStatusUpdated($user, 'active'));

            // LOGS
            AuditLog::record(
                action: 'approved',
                module: 'Account Approval',
                referenceId: $user->id,
                description: "Approved user {$user->name} ({$user->email})."
            );

            DB::commit();

            return redirect()->route('accounts.approval')->with('toast', [
                'message' => 'User approved successfully.',
                'type' => 'success',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

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

        DB::beginTransaction();

        try {
            $user = User::findOrFail($request->input('user_id'));
            $user->account_status = 'rejected';
            $user->save();

            // EMAIL
            Mail::to($user->email)->send(new AccountStatusUpdated($user, 'rejected'));

            // LOGS
            AuditLog::record(
                action: 'rejected',
                module: 'Account Approval',
                referenceId: $user->id,
                description: "Rejected user {$user->name} ({$user->email})."
            );

            DB::commit();

            return redirect()->route('accounts.approval')->with('toast', [
                'message' => 'User rejected successfully.',
                'type' => 'success',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

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

        $user = User::findOrFail($request->input('user_id'));
        $user->account_status = "pending";
        $user->save();

        // LOGS
        AuditLog::record(
            action: 'restored',
            module: 'Account Approval',
            referenceId: $user->id,
            description: "Restored user {$user->name} ({$user->email}) to pending."
        );

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

        $user = User::findOrFail($request->input('user_id'));
        $user->account_status = "disabled";
        $user->save();

        // EMAIL
        Mail::to($user->email)->send(new AccountStatusUpdated($user, 'disabled'));

        // LOGS
        AuditLog::record(
            action: 'disabled',
            module: 'Account Approval',
            referenceId: $user->id,
            description: "Disabled user {$user->name} ({$user->email})."
        );

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

        $user = User::findOrFail($request->user_id);

        if ($user->account_status !== 'active') {
            abort(403, 'Roles can only be assigned to active accounts.');
        }

        DB::beginTransaction();

        try {
            // Get old roles before modification (for comparison)
            $oldRoleNames = $user->roles->pluck('name')->toArray();

            $roles = collect($request->roles)
                ->push('faculty') // always include faculty
                ->unique();

            $roleIds = $roles->map(function ($roleName) { // ensure roles exist in database
                return Role::firstOrCreate(
                    ['name' => $roleName]
                )->id;
            });

            // Handle role removal: clean up UserAssignments when dean/chair roles are removed
            $newRoleNames = $roles->toArray();

            // If dean role is being removed, delete all dean assignments for this user
            if (in_array('dean', $oldRoleNames) && !in_array('dean', $newRoleNames)) {
                UserAssignment::where('user_id', $user->id)
                    ->where('context', 'dean')
                    ->delete();
            }

            // If chair role is being removed, delete all chair assignments for this user
            if (in_array('chair', $oldRoleNames) && !in_array('chair', $newRoleNames)) {
                UserAssignment::where('user_id', $user->id)
                    ->where('context', 'chair')
                    ->delete();
            }

            $user->roles()->sync($roleIds); // Sync roles

            // LOGS
            AuditLog::record(
                action: 'roles_updated',
                module: 'Account Approval',
                referenceId: $user->id,
                description: 'Updated roles for ' . $user->name . '. New roles: ' . implode(', ', $newRoleNames) . '.'
            );

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->route('accounts.approval')
                ->withErrors([
                    'error' => 'Failed to assign roles. Please try again.',
                ]);
        }
        // means any roles not in the list will be removed

        return redirect()
            ->route('accounts.approval')
            ->with('toast', [
                'message' => "Roles assigned to {$user->name} successfully.",
                'type' => 'success'
            ]);
    }
}
