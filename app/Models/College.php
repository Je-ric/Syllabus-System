<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class College extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cais_college_id',
    ];

    protected $casts = [
        'cais_college_id' => 'integer',
    ];

    // Used in: goal_index() - GoalController;
    //          destroyCollege() - AcademicStructureController
    //          sharedData() - SyllabusPreviewService;
    public function goals()
    {
        return $this->hasMany(CollegeGoal::class);
    }

    // Used in: index() - AcademicStructureController;
    //          storeProgram() - AcademicStructureController;
    //          updateProgram() - AcademicStructureController;
    //          destroyCollege() - AcademicStructureController;
    //          collegesIndexData() - OrganizationalHierarchyService
    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    // Used in: collegesIndexData() - OrganizationalHierarchyService
    public function deanAssignment()
    {
        return UserAssignment::where('college_id', $this->id)
            ->where('context', 'dean')
            ->with('user')
            ->first();
    }

    // Used in: goal_index() - GoalController
    public function userAssignments()
    {
        return $this->hasMany(UserAssignment::class);
    }

    // Used in: goal_store() - GoalController
    public function getNextGoalCode(): string
    {
        $count = $this->goals()->count();
        if ($count < 26) {
            return chr(ord('a') + $count);
        }
        $first  = chr(ord('a') + intdiv($count, 26) - 1);
        $second = chr(ord('a') + ($count % 26));
        return $first . $second;
    }

    // Used in: goal_destroy() - GoalController
    public function resequenceGoalCodes(): void
    {
        $goals = $this->goals()->orderBy('id')->lockForUpdate()->get();

        foreach ($goals as $i => $goal) {
            $newCode = $i < 26
                ? chr(ord('a') + $i)
                : chr(ord('a') + intdiv($i, 26) - 1) . chr(ord('a') + ($i % 26));
            if ($goal->college_goals_code !== $newCode) {
                $goal->update(['college_goals_code' => $newCode]);
            }
        }
    }
}
