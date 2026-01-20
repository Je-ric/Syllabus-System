<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Role;

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
        }

        return redirect()->route('accounts.approval')
            ->with('success', 'User approved and assigned as Faculty.');
    }

    public function reject(Request $request)
    {
        $user = User::find($request->input('user_id'));
        if ($user) {
            $user->account_status = "rejected";
            $user->save();
        }

        return redirect()->route('accounts.approval');
    }

    public function restore(Request $request)
    {
        $user = User::find($request->input('user_id'));
        if ($user) {
            $user->account_status = "pending";
            $user->save();
        }

        return redirect()->route('accounts.approval');
    }

    public function disable(Request $request)
    {
        $user = User::find($request->input('user_id'));
        if ($user) {
            $user->account_status = "disabled";
            $user->save();
        }

        return redirect()->route('accounts.approval');
    }
    public function assignRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'roles'   => 'required|array',
            'roles.*' => 'in:admin,chair,dean,faculty',
        ]);

        $user = User::findOrFail($request->user_id);

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
            ->with('success', "Roles assigned to {$user->name} successfully.");
    }
}
