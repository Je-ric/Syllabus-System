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

    // Used in: eagerLoad() - SyllabusPreviewService; 
    //          loadRows() - CourseEvaluationService; 
    //          wizard() - SyllabusController
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Used in: eagerLoad() - SyllabusPreviewService; 
    //          mount() - AcademicCalendarStep (Livewire)
    public function academicCalendar()
    {
        return $this->belongsTo(AcademicCalendar::class, 'academic_calendar_id');
    }

    // Used in: eagerLoad() - SyllabusPreviewService
    public function preparer()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    // Used in: eagerLoad() - SyllabusPreviewService; 
    //          setConcurredBy() - SyllabusApprovalService
    public function deanConcurred()
    {
        return $this->belongsTo(User::class, 'concurred_by');
    }

    // Used in: eagerLoad() - SyllabusPreviewService
    public function dean()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Used in: delete() - SyllabusDeleteService; 
    //          getCurrentRevisionNumber() - Syllabus; 
    //          mount() - SyllabusRevisionHistoryService
    public function revisions()
    {
        return $this->hasMany(SyllabusRevision::class);
    }

    // Used in: delete() - SyllabusDeleteService; 
    //          eagerLoad() - SyllabusPreviewService; 
    //          mount() - SyllabusReviewService
    public function reviewers()
    {
        return $this->hasMany(SyllabusReviewer::class);
    }

    // Used in: delete() - SyllabusDeleteService; 
    //          eagerLoad() - SyllabusPreviewService; 
    //          loadRows() - CourseEvaluationService
    public function components()
    {
        return $this->hasMany(CourseComponent::class);
    }

    // Used in: delete() - SyllabusDeleteService; 
    //          eagerLoad() - SyllabusPreviewService; 
    //          all() - CourseOutcomeService
    public function courseOutcomes()
    {
        return $this->hasMany(CourseOutcome::class);
    }

    // Used in: delete() - SyllabusDeleteService; 
    //          eagerLoad() - SyllabusPreviewService; 
    //          populateInputs() - WeekContentService; 
    //          save() - WeekContentService
    public function weeks()
    {
        return $this->hasMany(SyllabusWeek::class);
    }

    // Used in: delete() - SyllabusDeleteService; 
    //          buildReferences() - SyllabusPreviewService; 
    //          save() - WeekContentService
    public function references()
    {
        return $this->hasMany(Reference::class);
    }

    // Used in: delete() - SyllabusDeleteService; 
    //          buildReferences() - SyllabusPreviewService; 
    //          save() - WeekContentService
    public function onlineMaterials()
    {
        return $this->hasMany(OnlineMaterial::class);
    }

    // Used in: delete() - SyllabusDeleteService; 
    //          sharedData() - SyllabusPreviewService; 
    //          injectVersionsDrawer() - SyllabusSnapshotService
    public function completeSyllabi()
    {
        return $this->hasMany(CompleteSyllabus::class);
    }

    // Used in: ReviewStep (Livewire) — loadReviewForm();
    //          SyllabusReviewFormService — findOrCreate()
    public function reviewForm()
    {
        return $this->hasOne(SyllabusReviewForm::class);
    }

    // Used in: getLecComponent() - Syllabus; 
    //          getLabComponent() - Syllabus; 
    //          eagerLoad() - SyllabusPreviewService
    public function getLecComponent()
    {
        return $this->components()->where('type', 'LEC')->first();
    }

    // Used in: mount() - ComponentsStep (Livewire)
    public function getLabComponent()
    {
        return $this->components()->where('type', 'LAB')->first();
    }

    // Used in: edit() - SyllabusController; 
    //          update() - SyllabusController
    public function isEditable(): bool
    {
        return in_array($this->status, ['draft', 'for_revision']);
    }

    // Used in: mount() - SyllabusRevisionHistoryService
    public function getCurrentRevisionNumber(): int
    {
        return (int) ($this->revisions()->max('revision_no') ?? 0);
    }

    // Used in: (available — wizard step list for UI rendering)
    public function getWizardSteps(): array
    {
        return [
            'academic_calendar' => 'Academic Calendar',
            'course_components' => 'Course Components',
            'course_outcomes'   => 'Course Outcomes',
            'weekly_coverage'   => 'Weekly Coverage',
            'course_evaluation' => 'Course Evaluation',
            'review'            => 'Review',
        ];
    }
}
