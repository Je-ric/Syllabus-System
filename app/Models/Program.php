<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Department;
use App\Models\ProgramEducationalObjective;
use App\Models\ProgramOutcome;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
                'name',
                'bor_approval_no',
                'bor_approval_date'
                ];

    // program belongs to many departments, pero again ideally 1 - 1
    // Used in:
        // storeProgram() - AcademicStructureController;
        // updateProgram() - AcademicStructureController
    public function departments()
    {
        return $this->belongsToMany(Department::class, 'program_departments')
                    ->withPivot('role') // similar to Department model, programs can belong to multiple departments with different roles
                    ->withTimestamps();
    }

    // each program has many PEOs
    // Used in: loadPeos() - ManagePeos; savePeos() - ManagePeos; loadPeos() - ManagePos; savePos() - ManagePos; loadPeos() - PeoDisplay
    public function peos()
    {
        return $this->hasMany(ProgramEducationalObjective::class);
    }

    // each program has many POs
    // Used in: create() - CourseController; edit() - CourseController; loadPos() - ManagePos; loadMapping() - ManagePos; savePos() - ManagePos
    public function outcomes()
    {
        return $this->hasMany(ProgramOutcome::class);
    }

    // each program has many courses
    // Used in:
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    // Query: Load program with ordered outcomes
    // Used in:
    public function scopeWithOrderedOutcomes($query)
    {
        return $query->with(['outcomes' => fn($q) => $q->orderBy('po_code')]);
    }

    // Helper: Get courses grouped by year and semester
    // Used in: index() - CourseController; create() - SyllabusController; showCourses() - SyllabusController
    public function getCoursesGroupedByYearAndSemester()
    {
        return $this->courses()
            ->with([
                'programOutcomes' => fn($q) => $q
                    ->select('program_outcomes.id', 'po_code', 'po_text')
                    ->orderBy('po_code'),
            ])
            ->orderBy('year_level')
            ->orderBy('semester')
            ->orderBy('course_code')
            ->get()
            ->groupBy('year_level')
            ->map(fn($yearCourses) => $yearCourses->groupBy('semester'));
    }

        //     [
        //       1 => [
        //         1 => [Course, Course],
        //         2 => [Course]
        //       ],
        //       2 => [
        //         1 => [Course]
        //       ]
        //     ]



}
