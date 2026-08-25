<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineMaterial extends Model
{
    protected $table = 'syllabus_materials';

    protected $fillable = [
        'syllabus_id',
        'syllabus_week_id',
        'component_type',
        'material_name',
        'url',
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
