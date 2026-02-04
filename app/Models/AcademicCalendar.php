<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicCalendar extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year',
        'semester',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // calendar has many events
    public function events()
    {
        return $this->hasMany(AcademicCalendarEvent::class);
    }

    // calendar has many syllabi
    public function syllabi()
    {
        return $this->hasMany(Syllabus::class, 'academic_calendar_id');
    }

    // Helper: Get formatted semester display
    public function getFormattedSemester()
    {
        return $this->semester . ' Sem ' . $this->academic_year;
    }
}
