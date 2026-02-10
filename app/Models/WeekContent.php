<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\COAssessmentPlan;

class WeekContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'syllabus_week_id',
        'component_type',
        'co_assessment_plan_id',
        'learning_outcomes',
        'topics',
    ];

    public function syllabusWeek()
    {
        return $this->belongsTo(SyllabusWeek::class);
    }

    public function assessmentPlan()
    {
        return $this->belongsTo(COAssessmentPlan::class, 'co_assessment_plan_id');
    }
}
