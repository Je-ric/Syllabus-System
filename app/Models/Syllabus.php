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

    protected $casts = [
        'approved_at' => 'datetime',
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

    // Access components through course relationship
    // Components belong to Course, not Syllabus
    public function courseComponents()
    {
        return $this->hasManyThrough(
            CourseComponent::class,
            Course::class,
            'id',           // Foreign key on Course
            'course_id',    // Foreign key on CourseComponent
            'course_id',    // Local key on Syllabus
            'id'            // Local key on Course
        );
    }

    // Helper: Get LEC component through course
    public function getLecComponent()
    {
        return $this->course->components()->where('type', 'LEC')->first();
    }

    // Helper: Get LAB component through course
    public function getLabComponent()
    {
        return $this->course->components()->where('type', 'LAB')->first();
    }

    // Helper: Check if course has lab
    public function hasLab()
    {
        return $this->course->has_lec_lab;
    }

    // Scope: Load syllabus with all related data
    public function scopeWithFullDetails($query)
    {
        return $query->with([
            'course.program',
            'course.components',
            'academicCalendar',
            'preparer',
            'chair',
            'dean',
            'revisions'
        ]);
    }

    // Helper: Get current revision number
    public function getCurrentRevisionNumber()
    {
        return $this->revisions()->max('revision_no') ?? 0;
    }

    // Helper: Check if syllabus is approved
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    // Helper: Check if syllabus is editable
    public function isEditable()
    {
        return in_array($this->status, ['draft', 'for_revision']);
    }
}
