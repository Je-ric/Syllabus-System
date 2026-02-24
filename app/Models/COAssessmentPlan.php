<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class COAssessmentPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_outcome_id',
        'assessment_name',
        'assessment_desc',
        'tla',
        'learning_outcomes',
        'topic',
    ];

    // Used in:
    public function courseOutcome()
    {
        return $this->belongsTo(CourseOutcome::class);
    }

    // Used in:
    public function weekContents()
    {
        return $this->hasMany(WeekContent::class, 'co_assessment_plan_id');
    }
}
