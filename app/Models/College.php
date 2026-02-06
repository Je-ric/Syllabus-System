<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class College extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // college has many goals
    public function goals()
    {
        return $this->hasMany(CollegeGoal::class);
    }

    // college has many departments
    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    // college has one dean through user assignments
    public function deanAssignment()
    {
        return UserAssignment::where('college_id', $this->id)
            ->where('context', 'dean')
            ->with('user')
            ->first();
    }

    // Helper: Get next goal code
    // used in:
        // GoalController@goal_store
    public function getNextGoalCode()
    {
        $count = $this->goals()->count();
        return chr(ord('a') + $count);
    }

    // Helper: Resequence goal codes after deletion
    // used in:
        // GoalController@goal_update
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
