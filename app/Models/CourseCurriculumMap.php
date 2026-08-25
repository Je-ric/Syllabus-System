<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseCurriculumMap extends Model
{
    protected $fillable = [
        'course_id',
        'program_outcome_id',
        'ied',
    ];

    // Used in: (pivot model — direct queries via Course::programOutcomes() or ProgramOutcome::courses());
    //          syncPoMappings() - Course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Used in: (pivot model — direct queries via Course::programOutcomes() or ProgramOutcome::courses());
    //          syncPoMappings() - Course
    public function programOutcome()
    {
        return $this->belongsTo(ProgramOutcome::class);
    }
}
