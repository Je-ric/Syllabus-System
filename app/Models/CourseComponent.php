<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'syllabus_id',
        'type',
        'class_hours',
        'instructor_name',
        'instructor_email',
        'phone',
        'office',
        'performance_standard',
    ];

    // Used in: loadRows() - CourseEvaluationService;
    //          eagerLoad() - SyllabusPreviewService;
    //          delete() - SyllabusDeleteService
    public function syllabus()
    {
        return $this->belongsTo(Syllabus::class);
    }

    public function schedules()
    {
        return $this->hasMany(CourseComponentSchedule::class);
    }
}
