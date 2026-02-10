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

    protected $casts = [
        'revision_date' => 'date',
        'revision_no' => 'integer',
    ];

    // Used in:
    public function syllabus()
    {
        return $this->belongsTo(Syllabus::class);
    }
}
