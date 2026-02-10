<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
                'college_id',
                'name',
                'chair_user_id',
                ];

    // many departments to one college
    // Used in:
    public function college()
    {
        return $this->belongsTo(College::class);
    }

    // department has many objectives
    // Used in:
    public function objectives()
    {
        return $this->hasMany(DepartmentObjective::class);
    }

    // department belongs to many programs, pero ideally 1 - 1
    // Used in:
    public function programs()
    {
        return $this->belongsToMany(Program::class, 'program_departments')
                    ->withPivot('role') // there are cases na programs can belong to multiple departments with different roles
                    ->withTimestamps();
    }

    // each department has one chair (user), pero unused since not sure sa approach
    // Used in:
    public function chair()
    {
        return $this->belongsTo(User::class, 'chair_user_id');
    }

    // department has one chair through user assignments
    // Used in:
    public function deptChair()
    {
        $assignment = UserAssignment::where('department_id', $this->id)
            ->where('context', 'chair')
            ->with('user')
            ->first();

        return $assignment ? $assignment->user : null;
    }

    // Query scope: Load department with relationships
    // Used in:
    public function scopeWithRelations($query)
    {
        return $query->with('college', 'programs');
    }

    // Helper: Get next objective code
    // Used in: objective_store() - ObjectiveController
    public function getNextObjectiveCode()
    {
        $count = $this->objectives()->count();
        return chr(ord('a') + $count);
    }

    // Helper: Resequence objective codes after deletion
    // Used in: objective_destroy() - ObjectiveController
    public function resequenceObjectiveCodes()
    {
        $objectives = $this->objectives()->orderBy('dept_obj_code')->get();

        $count = 0;
        foreach ($objectives as $objective) {
            $newCode = chr(ord('a') + $count);
            if ($objective->dept_obj_code !== $newCode) {
                $objective->update(['dept_obj_code' => $newCode]);
            }
            $count++;
        }
    }
}
