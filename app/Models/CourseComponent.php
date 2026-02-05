<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'syllabus_id',
        'type',
        'units',
        'class_hours',
        'schedule',
        'instructor_name',
        'instructor_email',
        'phone',
        'office',
        'consultation_hours',
        'performance_standard',
    ];

    protected $casts = [
        'units' => 'integer',
    ];

    public function syllabus()
    {
        return $this->belongsTo(Syllabus::class);
    }

    // Scope: Get only LEC components
    public function scopeLecture($query)
    {
        return $query->where('type', 'LEC');
    }

    // Scope: Get only LAB components
    public function scopeLaboratory($query)
    {
        return $query->where('type', 'LAB');
    }

    // Helper: Check if component is lecture
    public function isLecture()
    {
        return $this->type === 'LEC';
    }

    // Helper: Check if component is laboratory
    public function isLaboratory()
    {
        return $this->type === 'LAB';
    }

    // Helper: Get formatted schedule
    // E.g., "Mon, Wed 10:00-11:00" => "Mon, Wed 10:00-11:00"
    public function getFormattedSchedule()
    {
        return $this->schedule ? str_replace(',', ', ', $this->schedule) : 'TBA';
    }
}
