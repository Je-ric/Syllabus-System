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

    // Used in: deletePo() - ProgramController;
    //          eagerLoad() - SyllabusPreviewService;
    //          sharedData() - SyllabusPreviewService
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    // Used in: loadPeos() - ManagePos;
    //          savePos() - ManagePos;
    //          toggleMapping() - ManagePos;
    //          deletePo() - ProgramController (detach before delete);
    //          sharedData() - SyllabusPreviewService
    public function peos()
    {
        return $this->belongsToMany(
            ProgramEducationalObjective::class,
            'program_outcome_peo',
            'program_outcome_id',
            'program_eo_id'
        );
    }

    // Used in: buildCoPoLetterMap() - SyllabusPreviewService;
    //          deletePo() - ProgramController (existence check)
    public function courseOutcomes()
    {
        return $this->belongsToMany(
            CourseOutcome::class,
            'course_outcome_po'
        )
        ->withPivot('ied')
        ->withTimestamps();
    }

    // Used in: deletePo() - ProgramController (detach before delete);
    //          sharedData() - SyllabusPreviewService
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_curriculum_maps')
                    ->withPivot('ied');
    }
}
