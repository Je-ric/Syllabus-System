<?php

namespace App\Services\OrganizationalHierarchy;

use App\Models\AuditLog;
use App\Models\College;
use App\Models\Department;
use App\Models\User;
use App\Models\UserAssignment;
use Illuminate\Support\Facades\DB;
use Throwable;

class OrganizationalHierarchyService
{
    public function __construct(
        protected OrganizationalHierarchyChecker $checker
    ) {
    }

    public function collegesIndexData(): array
    {
        $colleges = College::with('departments')
            ->orderBy('name')
            ->get();

        $potentialDeans = $this->checker->getPotentialUsers(['admin', 'dean'], 'dean');

        $deanAssignments = UserAssignment::where('context', 'dean')
            ->with('user')
            ->get()
            ->groupBy('college_id');

        return compact('colleges', 'potentialDeans', 'deanAssignments');
    }

    public function departmentsIndexData(?User $actor, int $collegeId): array
    {
        $college = College::with('departments')->findOrFail($collegeId);

        $scoped = $this->checker->scopeCollegeForDepartmentsIndex($actor, $college);
        /** @var \App\Models\College $college */
        $college = $scoped['college'];
        $canManageChair = $scoped['canManageChair'];
        $canManageFaculty = $scoped['canManageFaculty'];

        $potentialChairs = $canManageChair
            ? $this->checker->getPotentialUsers(['admin', 'chair'], 'chair')
            : collect();

        $departmentIds = $college->departments->pluck('id')->toArray();
        $potentialFaculty = $canManageFaculty
            ? $this->checker->getPotentialUsers(['admin', 'faculty'])
            : collect();

        $chairAssignments = UserAssignment::whereIn('department_id', $departmentIds)
            ->where('context', 'chair')
            ->with('user')
            ->get()
            ->groupBy('department_id');

        $facultyAssignments = UserAssignment::whereIn('department_id', $departmentIds)
            ->where('context', 'faculty')
            ->with('user')
            ->get()
            ->groupBy('department_id');

        return compact(
            'college',
            'potentialChairs',
            'chairAssignments',
            'potentialFaculty',
            'facultyAssignments',
            'canManageChair',
            'canManageFaculty'
        );
    }

    public function assignDean(int $collegeId, int $userId): array
    {
        $college = College::findOrFail($collegeId);
        $user = User::findOrFail($userId);

        if ($toast = $this->checker->checkTargetHasRoleOrAdmin($user, 'dean', 'User must have dean role assigned.')) {
            return ['toast' => $toast];
        }

        if ($toast = $this->checker->checkTargetNotBothDeanAndChairForDeanAssign($user)) {
            return ['toast' => $toast];
        }

        if ($toast = $this->checker->checkTargetNotAlreadyDean($user)) {
            return ['toast' => $toast];
        }

        if ($toast = $this->checker->checkAlreadyAssigned($user, 'dean', $college->id, null, "User is already dean of {$college->name}.")) {
            return ['toast' => $toast];
        }

        try {
            DB::beginTransaction();

            UserAssignment::create([
                'user_id' => $user->id,
                'college_id' => $college->id,
                'department_id' => null,
                'context' => 'dean',
            ]);

            $user->ensureFacultyRoleAndAssignment($college->id, null);

            AuditLog::record(
                action: 'assigned',
                module: 'Organizational Hierarchy',
                referenceId: $college->id,
                description: "Assigned {$user->name} as dean of {$college->name}."
            );

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            return [
                'toast' => [
                    'message' => 'Failed to assign dean. Please try again.',
                    'type' => 'error',
                ],
            ];
        }

        return [
            'toast' => [
                'message' => "{$user->name} is now dean of {$college->name}.",
                'type' => 'success',
            ],
        ];
    }

    public function removeDean(int $collegeId, int $userId): array
    {
        $college = College::findOrFail($collegeId);
        $user = User::findOrFail($userId);

        try {
            DB::beginTransaction();

            UserAssignment::where('college_id', $collegeId)
                ->where('user_id', $userId)
                ->where('context', 'dean')
                ->delete();

            AuditLog::record(
                action: 'removed',
                module: 'Organizational Hierarchy',
                referenceId: $college->id,
                description: "Removed {$user->name} as dean of {$college->name}."
            );

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            return [
                'toast' => [
                    'message' => 'Failed to remove dean assignment. Please try again.',
                    'type' => 'error',
                ],
            ];
        }

        return [
            'toast' => [
                'message' => 'Dean assignment removed.',
                'type' => 'success',
            ],
        ];
    }

    public function assignChair(int $departmentId, int $userId, ?User $actor): array
    {
        $department = Department::findOrFail($departmentId);
        $user = User::findOrFail($userId);

        if ($toast = $this->checker->checkActorCanManageChair($actor, $department, 'assign')) {
            return ['toast' => $toast];
        }

        if ($toast = $this->checker->checkTargetHasRoleOrAdmin($user, 'chair', 'User must have chair role assigned.')) {
            return ['toast' => $toast];
        }

        if ($toast = $this->checker->checkTargetNotBothDeanAndChairForChairAssign($user)) {
            return ['toast' => $toast];
        }

        if ($toast = $this->checker->checkTargetNotAlreadyChair($user)) {
            return ['toast' => $toast];
        }

        if ($toast = $this->checker->checkAlreadyAssigned($user, 'chair', null, $department->id, "User is already chair of {$department->name}.")) {
            return ['toast' => $toast];
        }

        try {
            DB::beginTransaction();

            UserAssignment::create([
                'user_id' => $user->id,
                'college_id' => null,
                'department_id' => $department->id,
                'context' => 'chair',
            ]);

            $user->ensureFacultyRoleAndAssignment(null, $department->id);

            AuditLog::record(
                action: 'assigned',
                module: 'Organizational Hierarchy',
                referenceId: $department->id,
                description: "Assigned {$user->name} as chair of {$department->name}."
            );

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            return [
                'toast' => [
                    'message' => 'Failed to assign chair. Please try again.',
                    'type' => 'error',
                ],
            ];
        }

        return [
            'toast' => [
                'message' => "{$user->name} is now chair of {$department->name}.",
                'type' => 'success',
            ],
        ];
    }

    public function removeChair(int $departmentId, int $userId, ?User $actor): array
    {
        $department = Department::findOrFail($departmentId);
        $user = User::findOrFail($userId);

        if ($toast = $this->checker->checkActorCanManageChair($actor, $department, 'remove')) {
            return ['toast' => $toast];
        }

        try {
            DB::beginTransaction();

            UserAssignment::where('department_id', $departmentId)
                ->where('user_id', $userId)
                ->where('context', 'chair')
                ->delete();

            AuditLog::record(
                action: 'removed',
                module: 'Organizational Hierarchy',
                referenceId: $department->id,
                description: "Removed {$user->name} as chair of {$department->name}."
            );

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            return [
                'toast' => [
                    'message' => 'Failed to remove chair assignment. Please try again.',
                    'type' => 'error',
                ],
            ];
        }

        return [
            'toast' => [
                'message' => 'Chair assignment removed.',
                'type' => 'success',
            ],
        ];
    }

    public function assignFaculty(int $departmentId, int $userId, ?User $actor): array
    {
        $department = Department::findOrFail($departmentId);
        $user = User::findOrFail($userId);

        if ($toast = $this->checker->checkActorCanManageFaculty($actor, $department, 'assign')) {
            return ['toast' => $toast];
        }

        if ($toast = $this->checker->checkTargetHasRoleOrAdmin($user, 'faculty', 'User must have faculty role assigned.')) {
            return ['toast' => $toast];
        }

        if ($toast = $this->checker->checkAlreadyAssigned($user, 'faculty', null, $department->id, "User is already faculty of {$department->name}.", 'info')) {
            return ['toast' => $toast];
        }

        try {
            DB::beginTransaction();

            UserAssignment::create([
                'user_id' => $user->id,
                'college_id' => null,
                'department_id' => $department->id,
                'context' => 'faculty',
            ]);

            AuditLog::record(
                action: 'assigned',
                module: 'Organizational Hierarchy',
                referenceId: $department->id,
                description: "Assigned {$user->name} as faculty in {$department->name}."
            );

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            return [
                'toast' => [
                    'message' => 'Failed to assign faculty. Please try again.',
                    'type' => 'error',
                ],
            ];
        }

        return [
            'toast' => [
                'message' => "{$user->name} assigned as faculty to {$department->name}.",
                'type' => 'success',
            ],
        ];
    }

    public function removeFaculty(int $departmentId, int $userId, ?User $actor): array
    {
        $department = Department::findOrFail($departmentId);
        $user = User::findOrFail($userId);

        if ($toast = $this->checker->checkActorCanManageFaculty($actor, $department, 'remove')) {
            return ['toast' => $toast];
        }

        try {
            DB::beginTransaction();

            UserAssignment::where('department_id', $departmentId)
                ->where('user_id', $userId)
                ->where('context', 'faculty')
                ->delete();

            AuditLog::record(
                action: 'removed',
                module: 'Organizational Hierarchy',
                referenceId: $department->id,
                description: "Removed {$user->name} as faculty from {$department->name}."
            );

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            return [
                'toast' => [
                    'message' => 'Failed to remove faculty assignment. Please try again.',
                    'type' => 'error',
                ],
            ];
        }

        return [
            'toast' => [
                'message' => 'Faculty assignment removed.',
                'type' => 'success',
            ],
        ];
    }
}
