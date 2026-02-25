<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineMaterial extends Model
{
    protected $table = 'syllabus_materials';

    protected $fillable = [
        'syllabus_id',
        'syllabus_week_id',
        'material_name',
        'url',
    ];

    public function syllabus()
    {
        return $this->belongsTo(Syllabus::class);
    }
}
