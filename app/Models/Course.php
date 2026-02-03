<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'program_id',
        'course_code',
        'course_title',
        'course_description',
        'credit_units',
        'year_level',
        'semester',
        'has_lec_lab',
        'prerequisite',
        'corequisite',
        'created_by',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function programOutcomes()
    {
        return $this->belongsToMany(ProgramOutcome::class, 'course_curriculum_maps')
            ->withPivot('ied');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
