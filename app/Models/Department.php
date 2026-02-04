<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
                'college_id',
                'name',
                'chair_user_id',
                ];

    // many departments to one college
    public function college()
    {
        return $this->belongsTo(College::class);
    }

    // department has many objectives
    public function objectives()
    {
        return $this->hasMany(DepartmentObjective::class);
    }

    // department belongs to many programs, pero ideally 1 - 1
    public function programs()
    {
        return $this->belongsToMany(Program::class, 'program_departments')
                    ->withPivot('role') // there are cases na programs can belong to multiple departments with different roles
                    ->withTimestamps();
    }

    // each department has one chair (user), pero unused since not sure sa approach
    public function chair()
    {
        return $this->belongsTo(User::class, 'chair_user_id');
    }
}
