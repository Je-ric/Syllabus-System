<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompleteSyllabus extends Model
{
    use HasFactory;

    protected $fillable = [
        'syllabus_id',
        'course_id',
        'academic_year',
        'semester',
        'pdf_path',
        'version',
        'approved_at',
        'approved_by',
        'checksum',
    ];

    public function syllabus()
    {
        return $this->belongsTo(Syllabus::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
