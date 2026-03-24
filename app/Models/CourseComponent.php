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
        'schedule',
        'instructor_name',
        'instructor_email',
        'phone',
        'office',
        'consultation_hours',
        'performance_standard',
    ];

    // Used in: loadRows() - CourseEvaluationService; 
    //          eagerLoad() - SyllabusPreviewService; 
    //          delete() - SyllabusDeleteService
    public function syllabus()
    {
        return $this->belongsTo(Syllabus::class);
    }

    // Helper: Get formatted schedule for display (e.g. "Mon, Wed 10:00-11:00")
    public function getFormattedSchedule(): string
    {
        return $this->schedule ? str_replace(',', ', ', $this->schedule) : 'TBA';
    }
}
