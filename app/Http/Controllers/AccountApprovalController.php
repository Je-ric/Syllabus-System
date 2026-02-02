<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountStatusUpdated;

class AccountApprovalController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get();

        return view(
            'AccountApproval.index',
            compact('users')
        );
    }

    public function approve(Request $request)
    {
        $user = User::find($request->input('user_id'));

        if ($user) {
            $user->account_status = 'active';
            $user->save();

            $facultyRole = Role::where('name', '=', 'faculty')->first();  // Assign faculty role if not already assigned

            if ($facultyRole && !$user->roles()->wherePivot('role_id', $facultyRole->id)->exists()) {
                $user->roles()->attach($facultyRole->id);
            }

            Mail::to($user->email)->send(new AccountStatusUpdated($user, 'active'));
        }

        return redirect()->route('accounts.approval')
            ->with('toast', [
                'message' => 'User approved and assigned as Faculty.',
                'type' => 'success'
            ]);
    }

    public function reject(Request $request)
    {
        $user = User::find($request->input('user_id'));
        if ($user) {
            $user->account_status = "rejected";
            $user->save();

            Mail::to($user->email)->send(new AccountStatusUpdated($user, 'rejected'));
        }

        return redirect()->route('accounts.approval')
            ->with('toast', [
                'message' => 'User account rejected.',
                'type' => 'success'
            ]);
    }

    public function restore(Request $request)
    {
        $user = User::find($request->input('user_id'));
        if ($user) {
            $user->account_status = "pending";
            $user->save();
        }

        return redirect()->route('accounts.approval')
            ->with('toast', [
                'message' => 'User account restored to pending.',
                'type' => 'success'
            ]);
    }

    public function disable(Request $request)
    {
        $user = User::find($request->input('user_id'));

        if ($user) {
            $user->account_status = "disabled";
            $user->save();

            Mail::to($user->email)->send(new AccountStatusUpdated($user, 'disabled'));
        }

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

        $roles = collect($request->roles)
            ->push('faculty') // always include faculty
            ->unique();

        $roleIds = $roles->map(function ($roleName) { // ensure roles exist in database
            return Role::firstOrCreate(
                ['name' => $roleName]
            )->id;
        });

        $user->roles()->sync($roleIds); // Sync roles

        return redirect()
            ->route('accounts.approval')
            ->with('toast', [
                'message' => "Roles assigned to {$user->name} successfully.",
                'type' => 'success'
            ]);
    }

    public function edit(User $user)
    {
        
    }
}
