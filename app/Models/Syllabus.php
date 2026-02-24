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
        'current_step',
        'prepared_by',
        'concurred_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    // Used in:
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Used in:
    public function academicCalendar()
    {
        return $this->belongsTo(AcademicCalendar::class, 'academic_calendar_id');
    }

    // Used in:
    public function preparer()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    // Used in:
    public function chair()
    {
        return $this->belongsTo(User::class, 'concurred_by');
    }

    // Used in:
    public function dean()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Used in:
    public function revisions()
    {
        return $this->hasMany(SyllabusRevision::class);
    }

    // Direct relationship to components (now tied to syllabus)
    // Used in:
    public function components()
    {
        return $this->hasMany(CourseComponent::class);
    }

    // Direct relationship to course outcomes
    // Used in:
    public function courseOutcomes()
    {
        return $this->hasMany(CourseOutcome::class);
    }

    // Used in:
    public function weeks()
    {
        return $this->hasMany(SyllabusWeek::class);
    }

    // Helper: Get LEC component
    // Used in: loadExistingData() - SyllabusWizard
    public function getLecComponent()
    {
        return $this->components()->where('type', 'LEC')->first();
    }

    // Helper: Get LAB component
    // Used in: loadExistingData() - SyllabusWizard
    public function getLabComponent()
    {
        return $this->components()->where('type', 'LAB')->first();
    }

    // Helper: Check if course has lab
    // Used in:
    public function hasLab()
    {
        return $this->course->has_lec_lab;
    }

    // Scope: Load syllabus with all related data
    // Used in:
    public function scopeWithFullDetails($query)
    {
        return $query->with([
            'course.program',
            'components',
            'courseOutcomes',
            'academicCalendar',
            'preparer',
            'chair',
            'dean',
            'revisions'
        ]);
    }

    // Helper: Get current revision number
    // Used in:
    public function getCurrentRevisionNumber()
    {
        return $this->revisions()->max('revision_no') ?? 0;
    }

    // Helper: Check if syllabus is approved
    // Used in:
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    // Helper: Check if syllabus is editable
    // Used in: edit() - SyllabusController; update() - SyllabusController
    public function isEditable()
    {
        return in_array($this->status, ['draft', 'for_revision']);
    }

    // Helper: Get wizard steps based on course type
    // Used in:
    public function getWizardSteps()
    {
        $steps = [
            'academic_calendar' => 'Academic Calendar',
            'course_components' => 'Course Components',
            'course_outcomes' => 'Course Outcomes',
            'weekly_coverage' => 'Weekly Coverage',
            'review' => 'Review',
        ];

        return $steps;
    }

}
