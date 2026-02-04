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
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    // POs belong to many PEOs
    public function peos()
    {
        return $this->belongsToMany(
            ProgramEducationalObjective::class,
            'program_outcome_peo',
            'program_outcome_id',
            'program_eo_id'
        );
    }
}
