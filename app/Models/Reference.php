<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reference extends Model
{
    protected $table = 'syllabus_references';

    protected $fillable = [
        'syllabus_id',
        'syllabus_week_id',
        'component_type',
        'reference_text',
    ];

    // Used in: save() - WeekContentService;
    //          reset() - WeekContentService;
    //          buildReferences() - SyllabusPreviewService;
    //          delete() - SyllabusDeleteService
    public function syllabus()
    {
        return $this->belongsTo(Syllabus::class);
    }
}
