<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramOutcome extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'po_code',
        'po_text',
    ];

    // many POs to one program
    // Used in:
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    // POs belong to many PEOs
    // Used in:
        // loadPeos() - ManagePos;
        // savePos() - ManagePos
    public function peos()
    {
        return $this->belongsToMany(
            ProgramEducationalObjective::class,
            'program_outcome_peo',
            'program_outcome_id',
            'program_eo_id'
        );
    }

    // POs belong to many COs
    // Used in:
    public function courseOutcomes()
    {
        return $this->belongsToMany(
            CourseOutcome::class,
            'course_outcome_po'
        )
        ->withPivot('ied')
        ->withTimestamps();
    }

    // POs are mapped to many Courses via course_curriculum_maps
    // Used in: deletePo() - ProgramController
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_curriculum_maps')
                    ->withPivot('ied');
    }

}
