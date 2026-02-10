<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseOutcome extends Model
{
    use HasFactory;

    protected $fillable = [
        'syllabus_id',
        'co_code',
        'description',
    ];

    // many outcomes to one syllabus
    // Used in:
    public function syllabus()
    {
        return $this->belongsTo(Syllabus::class);
    }

    // course outcomes map to many program outcomes
    // Used in:
    public function programOutcomes()
    {
        return $this->belongsToMany(
            ProgramOutcome::class,
            'course_outcome_po'
        )
        ->withPivot('ied')
        ->withTimestamps();
    }
}
