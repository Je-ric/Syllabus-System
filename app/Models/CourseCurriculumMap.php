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

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function programOutcome()
    {
        return $this->belongsTo(ProgramOutcome::class);
    }
}
