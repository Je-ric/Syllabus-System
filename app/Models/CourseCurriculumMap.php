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

    // many curriculum maps to one course
    // Used in:
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // courses map to many program outcomes
    // Used in:
    public function programOutcome()
    {
        return $this->belongsTo(ProgramOutcome::class);
    }
}
