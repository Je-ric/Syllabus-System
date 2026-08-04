<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyllabusReviewChecklistResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_form_id',
        'reviewer_user_id',
        'section',
        'criterion_code',
        'response',
        'comments',
    ];

    protected $casts = [
        'review_form_id'   => 'integer',
        'reviewer_user_id' => 'integer',
    ];

    // Used in: SyllabusReviewFormService — saveChecklistResponse(), isChecklistComplete()
    public function reviewForm()
    {
        return $this->belongsTo(SyllabusReviewForm::class, 'review_form_id');
    }

    // Used in: SyllabusReviewChecklist (Livewire) — display reviewer name per response
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_user_id');
    }
}
