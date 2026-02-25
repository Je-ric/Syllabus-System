<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reference extends Model
{
    protected $table = 'syllabus_references';

    protected $fillable = [
        'syllabus_id',
        'syllabus_week_id',
        'reference_text',
    ];

    public function syllabus()
    {
        return $this->belongsTo(Syllabus::class);
    }
}
