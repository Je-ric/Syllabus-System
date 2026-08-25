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

    // Used in: populateInputs() - WeekContentService;
    //          save() - WeekContentService;
    //          reset() - WeekContentService;
    //          delete() - SyllabusDeleteService
    public function syllabusWeek()
    {
        return $this->belongsTo(SyllabusWeek::class);
    }

    // Used in: buildWeeklyCoverageRows() - SyllabusPreviewService;
    //          loadRows() - CourseEvaluationService;
    //          populateInputs() - WeekContentService;
    //          save() - WeekContentService
    public function courseOutcome()
    {
        return $this->belongsTo(CourseOutcome::class, 'course_outcome_id');
    }

    // Used in: delete() - SyllabusDeleteService;
    //          loadRows() - CourseEvaluationService;
    //          persist() - CourseEvaluationService;
    //          buildEvaluationRows() - SyllabusPreviewService
    public function evaluation()
    {
        return $this->hasOne(SyllabusEvaluationItem::class);
    }
}
