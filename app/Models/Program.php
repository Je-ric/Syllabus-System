<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'bor_approval_no',
        'bor_approval_date',
    ];

    // Used in: storeProgram() - AcademicStructureController; 
    //          updateProgram() - AcademicStructureController; 
    //          destroyProgram() - AcademicStructureController; 
    //          savePeos() - ManagePeos; 
    //          savePos() - ManagePos; 
    //          preselectFromProgram() - ProgramSelector
    public function departments()
    {
        return $this->belongsToMany(Department::class, 'program_departments')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    // Used in: mount() - ManagePeos; 
    //          savePeos() - ManagePeos; 
    //          mount() - ManagePos; 
    //          savePos() - ManagePos; 
    //          mount() - PeoDisplay; 
    //          eagerLoad() - SyllabusPreviewService
    public function peos()
    {
        return $this->hasMany(ProgramEducationalObjective::class);
    }

    // Used in: create() - CourseController; 
    //          edit() - CourseController; 
    //          mount() - ManagePos; 
    //          savePos() - ManagePos; 
    //          toggleMapping() - ManagePos; 
    //          eagerLoad() - SyllabusPreviewService
    public function outcomes()
    {
        return $this->hasMany(ProgramOutcome::class);
    }

    // Used in: index() - CourseController; 
    //          destroyProgram() - AcademicStructureController; 
    //          updateProgram() - AcademicStructureController; 
    //          destroyDepartment() - AcademicStructureController
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    // Used in: index() - CourseController; 
    //          buildProgramSelectionData() - SyllabusController
    public function scopeWithOrderedOutcomes($query)
    {
        return $query->with(['outcomes' => fn ($q) => $q->orderBy('po_code')]);
    }

    // Used in: index() - CourseController; 
    //          buildProgramSelectionData() - SyllabusController
    // Returns courses grouped as [ year_level => [ semester => [Course, ...] ] ]
    public function getCoursesGroupedByYearAndSemester()
    {
        return $this->courses()
            ->with([
                'programOutcomes' => fn ($q) => $q
                    ->select('program_outcomes.id', 'po_code', 'po_text')
                    ->orderBy('po_code'),
            ])
            ->orderBy('year_level')
            ->orderBy('semester')
            ->orderBy('course_code')
            ->get()
            ->groupBy('year_level')
            ->map(fn ($yearCourses) => $yearCourses->groupBy('semester'));
    }
}
