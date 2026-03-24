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
        'abridged_path',
        'evaluation_path',
        'version',
        'approved_at',
        'approved_by',
        'checksum',
        'checksum_abridged',
        'checksum_evaluation',
    ];

    // Used in: previewSaved*() - SyllabusController; 
    //          injectVersionsDrawer() - SyllabusSnapshotService; 
    //          sharedData() - SyllabusPreviewService
    public function syllabus()
    {
        return $this->belongsTo(Syllabus::class);
    }

    // Used in: (available for future use — course context on snapshot)
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Used in: (available for future use — who approved the snapshot)
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
