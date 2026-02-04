<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class College extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // college has many goals
    public function goals()
    {
        return $this->hasMany(CollegeGoal::class);
    }

    // college has many departments
    public function departments()
    {
        return $this->hasMany(Department::class);
    }
}
