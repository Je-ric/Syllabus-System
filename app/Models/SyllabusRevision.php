<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyllabusRevision extends Model
{
    use HasFactory;

    protected $fillable = [
        'syllabus_id',
        'revision_no',
        'revision_date',
        'implementation_semester',
        'highlights',
        'contributors',
    ];

    public function syllabus()
    {
        return $this->belongsTo(Syllabus::class);
    }
}
