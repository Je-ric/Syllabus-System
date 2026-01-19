<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Role;

class AccountApprovalController extends Controller
{
    public function index()
    {
        $users = User::all();

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
            $user->account_status = "disabled";
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

    public function assignRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'roles'   => 'required|array',
            'roles.*' => 'in:admin,oloi,dean,faculty',
        ]);

        $user = User::find($request->user_id);

        $user->roles()->sync([]);  // Detach all existing roles

        $roles = Role::whereIn('name', $request->roles)->get();  // Attach selected roles
        $user->roles()->attach($roles->pluck('id'));

        return redirect()->route('accounts.ap
        proval')
            ->with('success', "Roles assigned to {$user->name} successfully.");
    }
}
