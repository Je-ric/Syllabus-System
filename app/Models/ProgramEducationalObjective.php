<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramEducationalObjective extends Model
{
    use HasFactory;

    protected $table = 'program_eos';

    protected $fillable = [
        'program_id',
        'peo_code',
        'peo_text',
    ];

    // Used in: deletePeo() - ProgramController; 
    //          eagerLoad() - SyllabusPreviewService
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    // Used in: deletePeo() - ProgramController (detach before delete); 
    //          toggleMapping() - ManagePos
    public function outcomes()
    {
        return $this->belongsToMany(
            ProgramOutcome::class,
            'program_outcome_peo',
            'program_eo_id',
            'program_outcome_id'
        );
    }
}
