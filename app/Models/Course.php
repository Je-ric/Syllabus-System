<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $casts = [
        'cais_course_id' => 'integer',
    ];

    protected $fillable = [
        'cais_course_id',
        'program_id',
        'course_code',
        'course_title',
        'course_description',
        'credit_units',
        'year_level',
        'semester',
        'has_lec_lab',
        'passing_mark',
        'lec_class_hours',
        'lab_class_hours',
        'prerequisite',
        'corequisite',
        'status',
        'created_by',
    ];

    // Used in: edit() - CourseController; 
    //          logAction() - CourseService; 
    //          sharedData() - SyllabusPreviewService; 
    //          getCoursesGroupedByYearAndSemester() - Program
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    // Used in: update() - CourseService; 
    //          deleteCourse() - CourseService; 
    //          edit() - CourseController; 
    //          deletePo() - ProgramController
    public function programOutcomes()
    {
        return $this->belongsToMany(ProgramOutcome::class, 'course_curriculum_maps')
                    ->withPivot('ied');
    }

    // Used in: (available — who created the course)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Used in: deleteCourse() - CourseService; 
    //          updateCourse() - CourseService; 
    //          destroy() - SyllabusController; 
    //          destroyProgram() - AcademicStructureController
    public function syllabi()
    {
        return $this->hasMany(Syllabus::class);
    }

    // Used in: store() - CourseController; 
    //          createCourse() - CourseService
    public function syncPoMappings(array $poMapping): void
    {
        $this->programOutcomes()->detach();

        foreach ($poMapping as $outcomeId => $iedLevel) {
            if (in_array($iedLevel, ['I', 'E', 'D'])) {
                $this->programOutcomes()->attach($outcomeId, ['ied' => $iedLevel]);
            }
        }
    }
}
