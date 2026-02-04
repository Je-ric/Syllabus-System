<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'type',
        'units',
        'class_hours',
        'schedule',
        'instructor_name',
        'instructor_email',
        'phone',
        'office',
        'consultation_hours',
        'performance_standard',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
