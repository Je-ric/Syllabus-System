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
        'prerequisite',
        'corequisite',
        'status',
        'version',
        'created_by',
    ];

    protected $casts = [
        'has_lec_lab' => 'boolean',
        'credit_units' => 'integer',
        'year_level' => 'integer',
        'semester' => 'integer',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function curriculumMaps()
    {
        return $this->hasMany(CourseCurriculumMap::class);
    }

    public function programOutcomes()
    {
        return $this->belongsToMany(
            ProgramOutcome::class,
            'course_curriculum_maps'
            )->withPivot('ied')
            ->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
