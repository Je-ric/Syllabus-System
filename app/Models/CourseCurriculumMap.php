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
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // courses map to many program outcomes
    public function programOutcome()
    {
        return $this->belongsTo(ProgramOutcome::class);
    }
}
