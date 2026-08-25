<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyllabusEvaluationItem extends Model
{
    use HasFactory;

    protected $table = 'syllabus_evaluation_items';

    protected $fillable = [
        'week_content_id',
        'syllabus_id',
        'course_id',
        'outcome_label',
        'kind',
        'exam_type',
        'weight',
    ];

    protected $casts = [
        'weight' => 'integer',
    ];

    // Used in: loadRows() - CourseEvaluationService;
    //          buildEvaluationRows() - SyllabusPreviewService;
    //          delete() - SyllabusDeleteService (via WeekContent::evaluation());
    //          persist() - CourseEvaluationService
    public function weekContent()
    {
        return $this->belongsTo(WeekContent::class);
    }
}
