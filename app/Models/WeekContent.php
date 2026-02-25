<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeekContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'syllabus_week_id',
        'component_type',
        'course_outcome_id',
        'learning_outcomes',
        'assessment_task',
        'topics',
        'tla',
    ];

    // Used in:
    public function syllabusWeek()
    {
        return $this->belongsTo(SyllabusWeek::class);
    }

    // Used in:
    public function courseOutcome()
    {
        return $this->belongsTo(CourseOutcome::class, 'course_outcome_id');
    }
}
