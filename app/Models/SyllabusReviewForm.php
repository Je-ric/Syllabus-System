<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyllabusReviewForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'syllabus_id',
        'classification',
        'course_lead_name',
        'submitted_at',
        'decision',
        'decision_made_at',
        'decision_made_by',
        'required_actions',
        'target_compliance_date',
        'part_h_faculty_response',
        'part_h_faculty_response_updated_at',
        'part_h_verified_by',
        'part_h_verified_at',
        'recommended_by_chair_id',
        'recommended_by_chair_at',
        'approved_by_dean_id',
        'approved_by_dean_at',
        'filed_at',
        'filing_type',
        'review_form_snapshot',
    ];

    protected $casts = [
        'syllabus_id'                        => 'integer',
        'decision_made_by'                   => 'integer',
        'part_h_verified_by'                 => 'integer',
        'recommended_by_chair_id'            => 'integer',
        'approved_by_dean_id'               => 'integer',
        'submitted_at'                      => 'datetime',
        'decision_made_at'                  => 'datetime',
        'part_h_faculty_response_updated_at' => 'datetime',
        'part_h_verified_at'                => 'datetime',
        'recommended_by_chair_at'           => 'datetime',
        'approved_by_dean_at'               => 'datetime',
        'filed_at'                          => 'datetime',
        'target_compliance_date'            => 'date',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    // Used in: SyllabusReviewFormService — findOrCreate(), all mutations;
    //          delete() - SyllabusDeleteService
    public function syllabus()
    {
        return $this->belongsTo(Syllabus::class);
    }

    // Used in: SyllabusReviewFormService — syncNatureOfChange();
    //          delete() - SyllabusDeleteService
    public function natureOfChange()
    {
        return $this->hasMany(SyllabusReviewNatureOfChange::class, 'review_form_id');
    }

    // Used in: SyllabusReviewFormService — syncAttachments();
    //          delete() - SyllabusDeleteService
    public function attachments()
    {
        return $this->hasMany(SyllabusReviewAttachment::class, 'review_form_id');
    }

    // Used in: SyllabusReviewFormService — saveChecklistResponse(), isChecklistComplete();
    //          delete() - SyllabusDeleteService
    public function checklistResponses()
    {
        return $this->hasMany(SyllabusReviewChecklistResponse::class, 'review_form_id');
    }

    // Used in: SyllabusReviewFormService — verifyPartH();
    //          sharedData() - SyllabusPreviewService
    public function partHVerifier()
    {
        return $this->belongsTo(User::class, 'part_h_verified_by');
    }

    // Used in: SyllabusReviewFormService — recommendApproval();
    //          sharedData() - SyllabusPreviewService
    public function recommendedByChair()
    {
        return $this->belongsTo(User::class, 'recommended_by_chair_id');
    }

    // Used in: SyllabusReviewFormService — recordDeanApproval();
    //          sharedData() - SyllabusPreviewService
    public function approvedByDean()
    {
        return $this->belongsTo(User::class, 'approved_by_dean_id');
    }

    // Used in: ReviewStep (Livewire) — show decision maker;
    //          sharedData() - SyllabusPreviewService
    public function decisionMaker()
    {
        return $this->belongsTo(User::class, 'decision_made_by');
    }

    // ── Helper methods ────────────────────────────────────────────────────────

    // Used in: ReviewStep (Livewire) — conditional UI rendering;
    //          sharedData() - SyllabusPreviewService
    public function isUpdating(): bool
    {
        return $this->classification === 'updating';
    }

    // Used in: ReviewStep (Livewire) — conditional UI rendering;
    //          sharedData() - SyllabusPreviewService
    public function isRevision(): bool
    {
        return $this->classification === 'revision';
    }

    // Used in: SyllabusReviewFormService — gate before recording decision;
    //          sharedData() - SyllabusPreviewService
    public function isDecided(): bool
    {
        return $this->decision !== null;
    }

    // Used in: ReviewStep (Livewire) — show Part H panel;
    //          sharedData() - SyllabusPreviewService
    public function isPendingCompliance(): bool
    {
        return $this->decision === 'approved_with_corrections'
            && $this->part_h_verified_at === null;
    }

    // Used in: SyllabusReviewFormService — post-reclassification reset;
    //          sharedData() - SyllabusPreviewService
    public function isReclassified(): bool
    {
        return $this->decision === 'reclassified_as_revision';
    }

    // Used in: ReviewStep (Livewire) — final status display;
    //          sharedData() - SyllabusPreviewService
    public function isFullyApproved(): bool
    {
        return $this->approved_by_dean_at !== null;
    }
}
