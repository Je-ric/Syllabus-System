<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyllabusReviewAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_form_id',
        'attachment_type',
        'is_submitted',
        'other_label',
    ];

    protected $casts = [
        'review_form_id' => 'integer',
        'is_submitted'   => 'boolean',
    ];

    // Used in: SyllabusReviewFormService — syncAttachments();
    //          delete() - SyllabusDeleteService
    public function reviewForm()
    {
        return $this->belongsTo(SyllabusReviewForm::class, 'review_form_id');
    }
}
