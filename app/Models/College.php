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
    public function getNextGoalCode(): string
    {
        $count = $this->goals()->count();
        if ($count < 26) {
            return chr(ord('a') + $count);
        }
        // 26+ goals: aa, ab, ac, ...
        $first  = chr(ord('a') + intdiv($count, 26) - 1);
        $second = chr(ord('a') + ($count % 26));
        return $first . $second;
    }

    // Helper: Resequence goal codes after deletion
    // Used in:
        // goal_destroy() - GoalController
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
