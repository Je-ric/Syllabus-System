<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class College extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // college has many goals
    // Used in:
    public function goals()
    {
        return $this->hasMany(CollegeGoal::class);
    }

    // college has many departments
    // Used in:
        // storeProgram() - AcademicStructureController;
        // updateProgram() - AcademicStructureController
    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    // college has one dean through user assignments
    // Used in:
    public function deanAssignment()
    {
        return UserAssignment::where('college_id', $this->id)
            ->where('context', 'dean')
            ->with('user')
            ->first();
    }

    // Helper: Get next goal code
    // Used in:
        // goal_store() - GoalController
    public function getNextGoalCode()
    {
        $count = $this->goals()->count();
        return chr(ord('a') + $count);
    }

    // Helper: Resequence goal codes after deletion
    // Used in:
        // goal_destroy() - GoalController
    public function resequenceGoalCodes()
    {
        $goals = $this->goals()->orderBy('college_goals_code')->get();

        $count = 0;
        foreach ($goals as $goal) {
            $newCode = chr(ord('a') + $count);
            if ($goal->college_goals_code !== $newCode) {
                $goal->update(['college_goals_code' => $newCode]);
            }
            $count++;
        }
    }
}
