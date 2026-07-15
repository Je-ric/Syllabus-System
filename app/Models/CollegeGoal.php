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
        'goal_text',
        'cais_college_id',
    ];

    protected $casts = [
        'cais_college_id' => 'integer',
    ];

    // Used in: goal_update() - GoalController;
    //          goal_destroy() - GoalController;
    //          sharedData() - SyllabusPreviewService
    public function college()
    {
        return $this->belongsTo(College::class);
    }
}
