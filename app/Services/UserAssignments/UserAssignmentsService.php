<?php

namespace App\Services\UserAssignments;

use App\Models\AuditLog;
use App\Models\College;
use App\Models\Department;
use App\Models\User;
use App\Models\UserAssignment;
use App\Notifications\RoleAssignmentNotification;
use Closure;
use Illuminate\Support\Facades\DB;
use Throwable;

class UserAssignmentsService
{
    public function __construct(
        protected UserAssignmentsChecker $checker
    ) {
    }

    public function collegesIndexData(): array
    {
        $colleges = College::with('departments')
            ->orderBy('name')
            ->get();

        $potentialDeans = $this->checker->getPotentialUsers(['admin', 'dean'], 'dean');

        $deanAssignments = UserAssignment::dean()
            ->with('user')
            ->get()
            ->groupBy('college_id');

        return compact('colleges', 'potentialDeans', 'deanAssignments');
    }

    public function departmentsIndexData(?User $actor, int $collegeId): array
    {
        $college = College::with('departments')->findOrFail($collegeId);

        $scoped = $this->checker->scopeCollegeForDepartmentsIndex($actor, $college);
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

        $chairAssignments = UserAssignment::chair()
            ->whereIn('department_id', $departmentIds)
            ->with('user')
            ->get()
            ->groupBy('department_id');

        $facultyAssignments = UserAssignment::faculty()
            ->whereIn('department_id', $departmentIds)
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

    // ── ASSIGN METHODS ───────────────────────────────────────────────────────────

    public function assignDean(int $collegeId, int $userId): array
    {
        $college = College::findOrFail($collegeId);
        $user = User::findOrFail($userId);

        if ($toast = $this->runChecks($user, [
            ['checkTargetHasRoleOrAdmin', ['dean', 'User must have dean role assigned.']],
            ['checkTargetNotBothDeanAndChairForDeanAssign'],
            ['checkTargetNotAlreadyDean'],
            ['checkAlreadyAssigned', ['dean', $college->id, null, "User is already dean of {$college->name}."]],
        ])) {
            return $toast;
        }

        return $this->runInTransaction(
            fn() => $this->createDeanAssignment($user, $college),
            'Failed to assign dean. Please try again.',
            "{$user->name} is now dean of {$college->name}."
        );
    }

    public function assignChair(int $departmentId, int $userId, ?User $actor): array
    {
        $department = Department::findOrFail($departmentId);
        $user = User::findOrFail($userId);

        if ($toast = $this->checker->checkActorCanManageChair($actor, $department, 'assign')) {
            return ['toast' => $toast];
        }

        if ($toast = $this->runChecks($user, [
            ['checkTargetHasRoleOrAdmin', ['chair', 'User must have chair role assigned.']],
            ['checkTargetNotBothDeanAndChairForChairAssign'],
            ['checkTargetNotAlreadyChair'],
            ['checkAlreadyAssigned', ['chair', null, $department->id, "User is already chair of {$department->name}."]],
        ])) {
            return $toast;
        }

        return $this->runInTransaction(
            fn() => $this->createChairAssignment($user, $department),
            'Failed to assign chair. Please try again.',
            "{$user->name} is now chair of {$department->name}."
        );
    }

    public function assignFaculty(int $departmentId, int $userId, ?User $actor): array
    {
        $department = Department::findOrFail($departmentId);
        $user = User::findOrFail($userId);

        if ($toast = $this->checker->checkActorCanManageFaculty($actor, $department, 'assign')) {
            return ['toast' => $toast];
        }

        if ($toast = $this->runChecks($user, [
            ['checkTargetHasRoleOrAdmin', ['faculty', 'User must have faculty role assigned.']],
            ['checkAlreadyAssigned', ['faculty', null, $department->id, "User is already faculty of {$department->name}.", 'info']],
        ])) {
            return $toast;
        }

        return $this->runInTransaction(
            fn() => $this->createFacultyAssignment($user, $department),
            'Failed to assign faculty. Please try again.',
            "{$user->name} assigned as faculty to {$department->name}."
        );
    }

    public function bulkAssignFaculty(int $departmentId, array $userIds, ?User $actor): array
    {
        $department = Department::findOrFail($departmentId);

        if ($toast = $this->checker->checkActorCanManageFaculty($actor, $department, 'assign')) {
            return ['toast' => $toast];
        }

        if (empty($userIds)) {
            return [
                'toast' => [
                    'message' => 'No users selected for assignment.',
                    'type' => 'error',
                ],
            ];
        }

        $users = User::whereIn('id', $userIds)->get();
        $assignedCount = 0;
        $skippedCount = 0;
        $failedUsers = [];

        return $this->runInTransaction(
            function() use ($department, $users, &$assignedCount, &$skippedCount, &$failedUsers) {
                foreach ($users as $user) {
                    if ($toast = $this->runChecks($user, [
                        ['checkTargetHasRoleOrAdmin', ['faculty', 'User must have faculty role assigned.']],
                        ['checkAlreadyAssigned', ['faculty', null, $department->id, '', 'info']],
                    ])) {
                        $skippedCount++;
                        continue;
                    }

                    try {
                        $this->createFacultyAssignment($user, $department);
                        $assignedCount++;
                    } catch (Throwable $e) {
                        $failedUsers[] = $user->name;
                    }
                }

                if ($assignedCount === 0) {
                    throw new \Exception('No faculty members were assigned.');
                }
            },
            'Failed to assign faculty members. Please try again.',
            $this->getBulkAssignmentMessage($assignedCount, $skippedCount, $failedUsers, $department->name)
        );
    }

    private function getBulkAssignmentMessage(int $assignedCount, int $skippedCount, array $failedUsers, string $departmentName): string
    {
        $message = "Assigned {$assignedCount} faculty member(s) to {$departmentName}";

        if ($skippedCount > 0) {
            $message .= ". {$skippedCount} skipped (already assigned)";
        }

        if (!empty($failedUsers)) {
            $message .= ". Failed: " . implode(', ', $failedUsers);
        }

        return $message . '.';
    }

    // ── REMOVE METHODS ───────────────────────────────────────────────────────────

    public function removeDean(int $collegeId, int $userId): array
    {
        $college = College::findOrFail($collegeId);
        $user = User::findOrFail($userId);

        return $this->runInTransaction(
            fn() => $this->removeAndAudit('dean', $user, $college->id, null, "Removed {$user->name} as dean of {$college->name}.", $college->name),
            'Failed to remove dean assignment. Please try again.',
            'Dean assignment removed.'
        );
    }

    public function removeChair(int $departmentId, int $userId, ?User $actor): array
    {
        $department = Department::findOrFail($departmentId);
        $user = User::findOrFail($userId);

        if ($toast = $this->checker->checkActorCanManageChair($actor, $department, 'remove')) {
            return ['toast' => $toast];
        }

        return $this->runInTransaction(
            fn() => $this->removeAndAudit('chair', $user, null, $department->id, "Removed {$user->name} as chair of {$department->name}.", $department->name),
            'Failed to remove chair assignment. Please try again.',
            'Chair assignment removed.'
        );
    }

    public function removeFaculty(int $departmentId, int $userId, ?User $actor): array
    {
        $department = Department::findOrFail($departmentId);
        $user = User::findOrFail($userId);

        if ($toast = $this->checker->checkActorCanManageFaculty($actor, $department, 'remove')) {
            return ['toast' => $toast];
        }

        return $this->runInTransaction(
            fn() => $this->removeAndAudit('faculty', $user, null, $department->id, "Removed {$user->name} as faculty from {$department->name}.", $department->name),
            'Failed to remove faculty assignment. Please try again.',
            'Faculty assignment removed.'
        );
    }

    // ── PRIVATE HELPERS ──────────────────────────────────────────────────────────

    private function runChecks(User $user, array $checks): ?array
    {
        foreach ($checks as $check) {
            $method = $check[0];
            $params = $check[1] ?? [];

            $toast = match (count($params)) {
                0 => $this->checker->$method($user),
                1 => $this->checker->$method($user, ...$params),
                2 => $this->checker->$method($user, $params[0], $params[1]),
                3 => $this->checker->$method($user, $params[0], $params[1], $params[2]),
                4 => $this->checker->$method($user, $params[0], $params[1], $params[2], $params[3]),
                default => $this->checker->$method($user, ...$params),
            };

            if ($toast !== null) {
                return ['toast' => $toast];
            }
        }

        return null;
    }

    private function runInTransaction(Closure $operation, string $errorMessage, string $successMessage): array
    {
        try {
            DB::beginTransaction();
            $operation();
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            return [
                'toast' => [
                    'message' => $errorMessage,
                    'type' => 'error',
                ],
            ];
        }

        return [
            'toast' => [
                'message' => $successMessage,
                'type' => 'success',
            ],
        ];
    }

    private function createDeanAssignment(User $user, College $college): void
    {
        UserAssignment::create([
            'user_id'       => $user->id,
            'college_id'    => $college->id,
            'department_id' => null,
            'context'       => 'dean',
        ]);

        $user->ensureFacultyRoleAndAssignment($college->id, null);

        $user->notify(new RoleAssignmentNotification('dean', 'assigned', $college->name));

        AuditLog::record(
            action: 'assigned',
            module: 'Organizational Hierarchy',
            referenceId: $college->id,
            description: "Assigned {$user->name} as dean of {$college->name}."
        );
    }

    private function createChairAssignment(User $user, Department $department): void
    {
        UserAssignment::create([
            'user_id'       => $user->id,
            'college_id'    => null,
            'department_id' => $department->id,
            'context'       => 'chair',
        ]);

        $user->ensureFacultyRoleAndAssignment(null, $department->id);

        $user->notify(new RoleAssignmentNotification('chair', 'assigned', $department->name));

        AuditLog::record(
            action: 'assigned',
            module: 'Organizational Hierarchy',
            referenceId: $department->id,
            description: "Assigned {$user->name} as chair of {$department->name}."
        );
    }

    private function createFacultyAssignment(User $user, Department $department): void
    {
        UserAssignment::create([
            'user_id'       => $user->id,
            'college_id'    => null,
            'department_id' => $department->id,
            'context'       => 'faculty',
        ]);

        $user->notify(new RoleAssignmentNotification('faculty', 'assigned', $department->name));

        AuditLog::record(
            action: 'assigned',
            module: 'Organizational Hierarchy',
            referenceId: $department->id,
            description: "Assigned {$user->name} as faculty in {$department->name}."
        );
    }

    private function removeAndAudit(string $context, User $user, ?int $collegeId, ?int $departmentId, string $description, string $placeName = ''): void
    {
        UserAssignment::removeAssignment($context, $user->id, $collegeId, $departmentId);

        if ($placeName !== '') {
            $user->notify(new RoleAssignmentNotification($context, 'removed', $placeName));
        }

        AuditLog::record(
            action: 'removed',
            module: 'Organizational Hierarchy',
            referenceId: $departmentId ?? $collegeId,
            description: $description,
        );
    }
}
