<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseOutcome extends Model
{
    use HasFactory;

    protected $fillable = [
        'syllabus_id',
        'co_code',
        'description',
    ];

    // Used in: all() - CourseOutcomeService;
    //          create() - CourseOutcomeService;
    //          delete() - CourseOutcomeService;
    //          eagerLoad() - SyllabusPreviewService;
    //          buildCoPoLetterMap() - SyllabusPreviewService
    public function syllabus()
    {
        return $this->belongsTo(Syllabus::class);
    }

    // Used in: buildCoPoLetterMap() - SyllabusPreviewService;
    //          deletePo() - ProgramController (existence check);
    //          syncPoMappings() - CourseOutcome
    public function programOutcomes()
    {
        return $this->belongsToMany(
            ProgramOutcome::class,
            'course_outcome_po'
        )
        ->withPivot('ied')
        ->withTimestamps();
    }
}
