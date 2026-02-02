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

    public function college()
    {
        return $this->belongsTo(College::class);
    }

    public function objectives()
    {
        return $this->hasMany(DepartmentObjective::class);
    }

    public function programs()
    {
        return $this->belongsToMany(Program::class, 'program_departments')
                    ->withPivot('role') // there are cases na programs can belong to multiple departments with different roles
                    ->withTimestamps();
    }

    public function chair()
    {
        return $this->belongsTo(User::class, 'chair_user_id');
    }
}
