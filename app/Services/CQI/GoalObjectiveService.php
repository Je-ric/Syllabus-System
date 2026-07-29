<?php

namespace App\Services\CQI;

use App\Models\AuditLog;
use App\Models\College;
use App\Models\CollegeGoal;
use App\Models\Department;
use App\Models\DepartmentObjective;
use App\Models\User;
use Illuminate\Support\Facades\DB;

// Used in: GoalController, ObjectiveController

class GoalObjectiveService
{
    public function getAccessibleGoalColleges(User $user)
    {
        $assignment = $user->getPrimaryCollegeAssignment();

        if ($assignment?->college) {
            return College::where('id', $assignment->college_id)
                ->orderBy('name')
                ->get();
        }

        if ($user->hasRole('dean')) {
            return College::orderBy('name')->get();
        }

        return collect();
    }

    public function getAccessibleObjectiveColleges(User $user)
    {
        $assignment = $user->getPrimaryDepartmentAssignment();

        if ($assignment?->department?->college) {
            return College::where('id', $assignment->department->college_id)
                ->orderBy('name')
                ->get();
        }

        if ($user->hasRole('chair')) {
            return College::orderBy('name')->get();
        }

        return collect();
    }

    public function getAccessibleDepartments(User $user, int $collegeId)
    {
        $assignment = $user->getPrimaryDepartmentAssignment();

        if ($assignment?->department && (int) $assignment->department->college_id === (int) $collegeId) {
            return Department::where('college_id', $collegeId)
                ->whereHas('userAssignments', fn ($q) => $q->where('user_id', $user->id)->whereIn('context', ['chair', 'faculty']))
                ->orderBy('name')
                ->get();
        }

        if ($user->hasRole('chair')) {
            return Department::where('college_id', $collegeId)
                ->orderBy('name')
                ->get();
        }

        return collect();
    }

    public function canManageGoal(User $user, College $college): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        $assignment = $user->getPrimaryCollegeAssignment();
        if ($assignment) {
            return (int) $assignment->college_id === (int) $college->id;
        }

        return $user->hasRole('dean');
    }

    public function canManageObjective(User $user, Department $department): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        $assignment = $user->getPrimaryDepartmentAssignment();
        if ($assignment) {
            return (int) $assignment->department_id === (int) $department->id;
        }

        return $user->hasRole('chair');
    }

    // GOALS

    public function storeGoal(College $college, string $text): CollegeGoal
    {
        $goal = CollegeGoal::create([
            'college_id'         => $college->id,
            'college_goals_code' => $college->getNextGoalCode(),
            'goal_text'          => $text,
        ]);

        AuditLog::record(
            action: 'created',
            module: 'Goal',
            referenceId: $goal->id,
            description: "Created goal {$goal->college_goals_code} for college {$college->name}."
        );

        return $goal;
    }

    public function updateGoal(CollegeGoal $goal, string $text): void
    {
        $goal->update(['goal_text' => $text]);

        AuditLog::record(
            action: 'updated',
            module: 'Goal',
            referenceId: $goal->id,
            description: "Updated goal {$goal->college_goals_code} for college {$goal->college?->name}."
        );
    }

    // @throws \Throwable
    public function destroyGoal(CollegeGoal $goal): void
    {
        DB::beginTransaction();
        try {
            $college  = $goal->college;
            $goalCode = $goal->college_goals_code;

            $goal->delete();
            $college->resequenceGoalCodes();

            AuditLog::record(
                action: 'deleted',
                module: 'Goal',
                referenceId: $goal->id,
                description: "Deleted goal {$goalCode} for college {$college->name}."
            );

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // OBJECTIVES

    public function storeObjective(Department $department, string $text): DepartmentObjective
    {
        $objective = DepartmentObjective::create([
            'department_id'  => $department->id,
            'dept_obj_code'  => $department->getNextObjectiveCode(),
            'objective_text' => $text,
        ]);

        AuditLog::record(
            action: 'created',
            module: 'Objective',
            referenceId: $objective->id,
            description: "Created objective {$objective->dept_obj_code} for department {$department->name}, college {$department->college?->name}."
        );

        return $objective;
    }

    public function updateObjective(DepartmentObjective $objective, string $text): void
    {
        $objective->update(['objective_text' => $text]);

        $department  = $objective->department;
        $collegeName = $department?->college?->name ?? 'N/A';

        AuditLog::record(
            action: 'updated',
            module: 'Objective',
            referenceId: $objective->id,
            description: "Updated objective {$objective->dept_obj_code} for department {$department?->name}, college {$collegeName}."
        );
    }

    // @throws \Throwable
    public function destroyObjective(DepartmentObjective $objective): void
    {
        DB::beginTransaction();
        try {
            $department    = $objective->department;
            $objectiveCode = $objective->dept_obj_code;

            $objective->delete();
            $department->resequenceObjectiveCodes();

            AuditLog::record(
                action: 'deleted',
                module: 'Objective',
                referenceId: $objective->id,
                description: "Deleted objective {$objectiveCode} for department {$department->name}, college {$department->college?->name}."
            );

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
