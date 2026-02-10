<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollegeGoal extends Model
{
    use HasFactory;

    protected $fillable = [
                'college_id',
                'college_goals_code',
                'goal_text'
                ];

    // many goals to one college
    // Used in:
    public function college()
    {
        return $this->belongsTo(College::class);
    }
}
