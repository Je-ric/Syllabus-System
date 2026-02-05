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

    // Direct relationship to components (now tied to syllabus)
    public function components()
    {
        return $this->hasMany(CourseComponent::class);
    }

    // Direct relationship to course outcomes
    public function courseOutcomes()
    {
        return $this->hasMany(CourseOutcome::class);
    }

    // Helper: Get LEC component
    public function getLecComponent()
    {
        return $this->components()->where('type', 'LEC')->first();
    }

    // Helper: Get LAB component
    public function getLabComponent()
    {
        return $this->components()->where('type', 'LAB')->first();
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
            'components',
            'courseOutcomes.programOutcomes',
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

    // Helper: Get wizard steps based on course type
    public function getWizardSteps()
    {
        $steps = [
            'academic_calendar' => 'Academic Calendar',
            'course_components' => 'Course Components',
            'course_outcomes' => 'Course Outcomes',
            'co_po_mapping' => 'CO-PO Mapping',
            'review' => 'Review',
        ];

        return $steps;
    }

    // Helper: Get next step
    public function getNextStep()
    {
        $steps = array_keys($this->getWizardSteps());
        $currentIndex = array_search($this->current_step, $steps);

        if ($currentIndex === false || $currentIndex >= count($steps) - 1) {
            return null;
        }

        return $steps[$currentIndex + 1];
    }

    // Helper: Get previous step
    public function getPreviousStep()
    {
        $steps = array_keys($this->getWizardSteps());
        $currentIndex = array_search($this->current_step, $steps);

        if ($currentIndex === false || $currentIndex <= 0) {
            return null;
        }

        return $steps[$currentIndex - 1];
    }
}
