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
        'cais_department_id',
        'cais_college_id',
    ];

    protected $casts = [
        'cais_department_id' => 'integer',
        'cais_college_id'    => 'integer',
    ];

    // Used in: index() - AcademicStructureController;
    //          sharedData() - SyllabusPreviewService;
    //          preselectFromProgram() - ProgramSelector;
    //          preselectFromUserAssignments() - ProgramSelector
    public function college()
    {
        return $this->belongsTo(College::class);
    }

    // Used in: objective_index() - ObjectiveController;
    //          objective_store() - ObjectiveController;
    //          destroyDepartment() - AcademicStructureController;
    //          resequenceObjectiveCodes() - Department
    public function objectives()
    {
        return $this->hasMany(DepartmentObjective::class);
    }

    // Used in: index() - AcademicStructureController;
    //          destroyDepartment() - AcademicStructureController;
    //          destroyCollege() - AcademicStructureController;
    //          updateProgram() - AcademicStructureController
    public function programs()
    {
        return $this->belongsToMany(Program::class, 'program_departments')
                    ->withPivot('role', 'cais_department_id')
                    ->withTimestamps();
    }

    // Used in: departmentsIndexData() - OrganizationalHierarchyService
    // Returns the chair User via UserAssignment (context = 'chair'); chair_user_id column is not used
    public function deptChair()
    {
        $assignment = UserAssignment::where('department_id', $this->id)
            ->where('context', 'chair')
            ->with('user')
            ->first();

        return $assignment ? $assignment->user : null;
    }

    // Used in: objective_index() - ObjectiveController
    public function userAssignments()
    {
        return $this->hasMany(UserAssignment::class);
    }

    // Used in: index() - AcademicStructureController
    public function scopeWithRelations($query)
    {
        return $query->with('college', 'programs');
    }

    // Used in: objective_store() - ObjectiveController
    public function getNextObjectiveCode(): string
    {
        $count = $this->objectives()->count();
        if ($count < 26) {
            return chr(ord('a') + $count);
        }
        $first  = chr(ord('a') + intdiv($count, 26) - 1);
        $second = chr(ord('a') + ($count % 26));
        return $first . $second;
    }

    // Used in: objective_destroy() - ObjectiveController
    public function resequenceObjectiveCodes(): void
    {
        $objectives = $this->objectives()->orderBy('id')->lockForUpdate()->get();

        foreach ($objectives as $i => $objective) {
            $newCode = $i < 26
                ? chr(ord('a') + $i)
                : chr(ord('a') + intdiv($i, 26) - 1) . chr(ord('a') + ($i % 26));
            if ($objective->dept_obj_code !== $newCode) {
                $objective->update(['dept_obj_code' => $newCode]);
            }
        }
    }
}
