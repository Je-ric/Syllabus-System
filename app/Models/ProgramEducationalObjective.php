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

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

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
