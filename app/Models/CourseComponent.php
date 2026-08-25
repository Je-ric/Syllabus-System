<?php

namespace App\Models;

use App\Models\User;
use App\Models\CourseComponentSchedule;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'syllabus_id',
        'user_id',
        'type',
        'class_hours',
        'performance_standard',
    ];

    // Used in: loadRows() - CourseEvaluationService;
    //          eagerLoad() - SyllabusPreviewService;
    //          delete() - SyllabusDeleteService
    public function syllabus()
    {
        return $this->belongsTo(Syllabus::class);
    }

    // Used in: (available for future use — faculty assigned to this component)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Used in: (available for future use — schedule details for this component)
    public function schedules()
    {
        return $this->hasMany(CourseComponentSchedule::class);
    }

    // Used in: eagerLoad() - SyllabusPreviewService
    public function course()
    {
        // return $this->belongsTo(Course::class);
        return $this->hasOneThrough(Course::class, Syllabus::class, 'id', 'id', 'syllabus_id', 'course_id');
    }
}
