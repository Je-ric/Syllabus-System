<?php

namespace App\Services\UserAssignments;

use App\Models\College;
use App\Models\Department;
use App\Models\User;

class UserAssignmentsChecker
{
    public function scopeCollegeForDepartmentsIndex(?User $actor, College $college): array
    {
        $isAdmin = $actor?->hasRole('admin') ?? false;
        $isDean = $actor?->isDean() ?? false;
        $chairAssignment = $actor?->assignments()->where('context', 'chair')->with('department')->first();
        $isChair = (bool) $chairAssignment;

        if ($isDean && !$isAdmin) {
            $deanAssignment = $actor?->getPrimaryCollegeAssignment();
            if (!$deanAssignment || (int) $deanAssignment->college_id !== (int) $college->id) {
                abort(403, 'Unauthorized college access.');
            }
        }

        if ($isChair && !$isAdmin && !$isDean) {
            $chairDepartmentId = (int) ($chairAssignment->department_id ?? 0);
            if ($chairDepartmentId === 0 || !$college->departments->contains('id', $chairDepartmentId)) {
                abort(403, 'Unauthorized department access.');
            }

            $college->setRelation('departments', $college->departments->where('id', $chairDepartmentId)->values());
        }

        $canManageChair = $isAdmin;
        $canManageFaculty = $isAdmin;

        return [
            'college' => $college,
            'isAdmin' => $isAdmin,
            'isDean' => $isDean,
            'isChair' => $isChair,
            'canManageChair' => $canManageChair,
            'canManageFaculty' => $canManageFaculty,
        ];
    }

    public function checkActorCanManageChair(?User $actor, Department $department, string $verb): ?array
    {
        $actorIsAdmin = $actor?->hasRole('admin') ?? false;

        if (!$actorIsAdmin) {
            return [
                'message' => "Only admin can {$verb} chairs.",
                'type' => 'error',
            ];
        }

        return null;
    }

    public function checkActorCanManageFaculty(?User $actor, Department $department, string $action): ?array
    {
        $actorIsAdmin = $actor?->hasRole('admin') ?? false;

        if (!$actorIsAdmin) {
            $message = $action === 'remove'
                ? 'Only admin can remove faculty assignments.'
                : 'Only admin can assign faculty.';

            return [
                'message' => $message,
                'type' => 'error',
            ];
        }

        return null;
    }

    public function checkTargetHasRoleOrAdmin(User $target, string $role, string $message): ?array
    {
        if (!$target->hasRole($role) && !$target->hasRole('admin')) {
            return [
                'message' => $message,
                'type' => 'error',
            ];
        }

        return null;
    }

    public function checkTargetNotBothDeanAndChairForDeanAssign(User $target): ?array
    {
        if ($target->isAssignedAsChair()) {
            return [
                'message' => 'User cannot be both dean and chair. Remove their chair assignment first.',
                'type' => 'error',
            ];
        }

        return null;
    }

    public function checkTargetNotBothDeanAndChairForChairAssign(User $target): ?array
    {
        if ($target->isAssignedAsDean()) {
            return [
                'message' => 'User cannot be both dean and chair. Remove their dean assignment first.',
                'type' => 'error',
            ];
        }

        return null;
    }

    public function checkTargetNotAlreadyDean(User $target): ?array
    {
        if ($target->isAssignedAsDean()) {
            $currentDean = $target->getPrimaryCollegeAssignment();
            return [
                'message' => "User is already dean of {$currentDean->college->name}. A dean can only be assigned to one college.",
                'type' => 'error',
            ];
        }

        return null;
    }

    public function checkTargetNotAlreadyChair(User $target): ?array
    {
        if ($target->isAssignedAsChair()) {
            $currentChair = $target->getPrimaryDepartmentAssignment();
            return [
                'message' => "User is already chair of {$currentChair->department->name}. A chair can only be assigned to one department.",
                'type' => 'error',
            ];
        }

        return null;
    }

    public function checkAlreadyAssigned(User $target, string $context, ?int $collegeId, ?int $departmentId, string $message, string $type = 'info'): ?array
    {
        $query = $target->assignments()->where('context', $context);

        if ($collegeId !== null) {
            $query->where('college_id', $collegeId);
        }

        if ($departmentId !== null) {
            $query->where('department_id', $departmentId);
        }

        if ($query->exists()) {
            return [
                'message' => $message,
                'type' => $type,
            ];
        }

        return null;
    }

    public function getPotentialUsers(array $roles, ?string $excludeContext = null)
    {
        $query = User::whereHas('roles', function ($query) use ($roles) {
            $query->whereIn('name', $roles);
        })
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'admin');
            })
            ->where('account_status', 'active');

        if ($excludeContext) {
            $query->whereNotIn('id', function ($query) use ($excludeContext) {
                $query->select('user_id')
                    ->from('user_assignments')
                    ->where('context', $excludeContext);
            });
        }

        return $query->orderBy('name')->get();
    }
}
