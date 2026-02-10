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
    // Used in:
            // store() - AcademicCalendarEventController;
            // update() - AcademicCalendarEventController
    public function events()
    {
        return $this->hasMany(AcademicCalendarEvent::class);
    }

    // calendar has many syllabi
    // Used in:
            // store() - SyllabusController;
            // update() - SyllabusController
    public function syllabi()
    {
        return $this->hasMany(Syllabus::class, 'academic_calendar_id');
    }

    // Helper: Get formatted semester display
    // Used in:
    public function getFormattedSemester()
    {
        return $this->semester . ' Sem ' . $this->academic_year;
    }
}
