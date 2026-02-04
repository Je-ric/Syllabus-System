<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Department;
use App\Models\ProgramEducationalObjective;
use App\Models\ProgramOutcome;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
                'name',
                'bor_approval_no',
                'bor_approval_date'
                ];

    // program belongs to many departments, pero again ideally 1 - 1
    public function departments()
    {
        return $this->belongsToMany(Department::class, 'program_departments')
                    ->withPivot('role') // similar to Department model, programs can belong to multiple departments with different roles
                    ->withTimestamps();
    }

    // each program has many PEOs
    public function peos()
    {
        return $this->hasMany(ProgramEducationalObjective::class);
    }

    // each program has many POs
    public function outcomes()
    {
        return $this->hasMany(ProgramOutcome::class);
    }

    // each program has many courses
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

}
