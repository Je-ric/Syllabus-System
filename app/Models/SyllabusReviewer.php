<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyllabusReviewer extends Model
{
    use HasFactory;

    protected $fillable = [
        'syllabus_id',
        'user_id',
        'status',
    ];

    protected $casts = [
        'syllabus_id' => 'integer',
        'user_id'     => 'integer',
    ];

    // Used in: delete() - SyllabusDeleteService; 
    //          eagerLoad() - SyllabusPreviewService
    public function syllabus()
    {
        return $this->belongsTo(Syllabus::class);
    }

    // Used in: eagerLoad() - SyllabusPreviewService
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
