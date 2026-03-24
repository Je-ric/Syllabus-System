<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\College;
use App\Models\CollegeGoal;
use App\Models\Department;
use App\Models\DepartmentObjective;
use Illuminate\Support\Facades\DB;

// Used in: GoalController, ObjectiveController

class GoalObjectiveService
{
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
