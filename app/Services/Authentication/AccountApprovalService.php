<?php

namespace App\Services\Authentication;

use App\Mail\AccountStatusUpdated;
use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use App\Models\UserAssignment;
use App\Notifications\SystemRoleChangedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AccountApprovalService
{
    public function approve(int $userId): User
    {
        return DB::transaction(function () use ($userId) {
            $user = User::findOrFail($userId);
            $user->account_status = 'active';
            $user->email_verified_at ??= now();
            $user->save();

            $facultyRole = Role::where('name', '=', 'faculty')->first();
            if ($facultyRole && !$user->roles()->wherePivot('role_id', $facultyRole->id)->exists()) {
                $user->roles()->attach($facultyRole->id);
            }

            Mail::to($user->email)->send(new AccountStatusUpdated($user, 'active'));

            AuditLog::record(
                action: 'approved',
                module: 'Account Approval',
                referenceId: $user->id,
                description: "Approved user {$user->name} ({$user->email})."
            );

            return $user;
        });
    }

    public function reject(int $userId): User
    {
        return DB::transaction(function () use ($userId) {
            $user = User::findOrFail($userId);
            $user->account_status = 'rejected';
            $user->save();

            UserAssignment::where('user_id', $user->id)->delete();

            Mail::to($user->email)->send(new AccountStatusUpdated($user, 'rejected'));

            AuditLog::record(
                action: 'rejected',
                module: 'Account Approval',
                referenceId: $user->id,
                description: "Rejected user {$user->name} ({$user->email})."
            );

            return $user;
        });
    }

    public function restore(int $userId): User
    {
        $user = User::findOrFail($userId);
        $user->account_status = 'pending';
        $user->save();

        AuditLog::record(
            action: 'restored',
            module: 'Account Approval',
            referenceId: $user->id,
            description: "Restored user {$user->name} ({$user->email}) to pending."
        );

        return $user;
    }

    public function disable(int $userId): User
    {
        return DB::transaction(function () use ($userId) {
        $user = User::findOrFail($userId);
        $user->account_status = 'disabled';
        $user->save();

        UserAssignment::where('user_id', $user->id)->delete();

        Mail::to($user->email)->send(new AccountStatusUpdated($user, 'disabled'));

        AuditLog::record(
            action: 'disabled',
            module: 'Account Approval',
            referenceId: $user->id,
            description: "Disabled user {$user->name} ({$user->email})."
        );

        return $user;
        }); // end transaction
    }

    public function assignRoles(int $userId, array $roles): User
    {
        $user = User::findOrFail($userId);

        if ($user->account_status !== 'active') {
            abort(403, 'Roles can only be assigned to active accounts.');
        }

        return DB::transaction(function () use ($user, $roles) {
            $oldRoleNames = $user->roles->pluck('name')->toArray();

            // Always keep 'faculty' for non-ovpaa users.
            // OVPAA is a distinct role and should not have 'faculty' forced onto them.
            $roleNames = collect($roles);
            if (! $roleNames->contains('ovpaa')) {
                $roleNames = $roleNames->push('faculty');
            }
            $roleNames = $roleNames->unique()->values();

            $roleIds = $roleNames->map(function ($roleName) {
                return Role::firstOrCreate(['name' => $roleName])->id;
            });

            $newRoleNames = $roleNames->toArray();

            if (in_array('dean', $newRoleNames) && in_array('chair', $newRoleNames)) {
                abort(422, 'A user cannot hold both Dean and Chair roles simultaneously.');
            }

            if (in_array('dean', $oldRoleNames) && !in_array('dean', $newRoleNames)) {
                UserAssignment::where('user_id', $user->id)
                    ->where('context', 'dean')
                    ->delete();
            }

            if (in_array('chair', $oldRoleNames) && !in_array('chair', $newRoleNames)) {
                UserAssignment::where('user_id', $user->id)
                    ->where('context', 'chair')
                    ->delete();
            }

            // faculty role is always kept (pushed above); clean up assignments if ever removed
            if (in_array('faculty', $oldRoleNames) && !in_array('faculty', $newRoleNames)) {
                UserAssignment::where('user_id', $user->id)
                    ->where('context', 'faculty')
                    ->delete();
            }

            $user->roles()->sync($roleIds);

            // Notify user for each meaningful role that was added or removed.
            // 'faculty' is excluded — it is always forced on and is not a meaningful admin action.
            $notifiableRoles = ['admin', 'dean', 'chair', 'ovpaa'];

            $added   = array_diff($newRoleNames, $oldRoleNames);
            $removed = array_diff($oldRoleNames, $newRoleNames);

            foreach (array_intersect($added, $notifiableRoles) as $role) {
                $user->notify(new SystemRoleChangedNotification($role, 'granted'));
            }

            foreach (array_intersect($removed, $notifiableRoles) as $role) {
                $user->notify(new SystemRoleChangedNotification($role, 'revoked'));
            }

            AuditLog::record(
                action: 'roles_updated',
                module: 'Account Approval',
                referenceId: $user->id,
                description: 'Updated roles for ' . $user->name . '. New roles: ' . implode(', ', $newRoleNames) . '.'
            );

            return $user;
        });
    }
}

