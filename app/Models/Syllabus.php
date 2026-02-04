<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Syllabus extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'academic_calendar_id',
        'status',
        'prepared_by',
        'concurred_by',
        'approved_by',
        'approved_at',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function academicCalendar()
    {
        return $this->belongsTo(AcademicCalendar::class, 'academic_calendar_id');
    }

    public function preparer()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function chair()
    {
        return $this->belongsTo(User::class, 'concurred_by');
    }

    public function dean()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function revisions()
    {
        return $this->hasMany(SyllabusRevision::class);
    }

    public function components()
    {
        return $this->hasMany(CourseComponent::class, 'course_id', 'course_id');
    }
}
