<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyllabusReviewNatureOfChange extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_form_id',
        'change_type',
    ];

    protected $casts = [
        'review_form_id' => 'integer',
    ];

    // Used in: SyllabusReviewFormService — syncNatureOfChange()
    public function reviewForm()
    {
        return $this->belongsTo(SyllabusReviewForm::class, 'review_form_id');
    }
}
