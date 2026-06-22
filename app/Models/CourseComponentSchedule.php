<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseComponentSchedule extends Model
{
    protected $fillable = [
        'course_component_id',
        'day',
        'time',
    ];

    public function component()
    {
        return $this->belongsTo(CourseComponent::class, 'course_component_id');
    }
}
