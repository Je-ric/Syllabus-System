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
        'cais_semester_id',
    ];

    protected $casts = [
        'start_date'       => 'date',
        'end_date'         => 'date',
        'cais_semester_id' => 'integer',
    ];

    // Used in: index() - AcademicCalendarController; 
    //          index() - AcademicCalendarEventController; 
    //          destroy() - AcademicCalendarController; 
    //          mount() - AcademicCalendarForm (Livewire)
    public function events()
    {
        return $this->hasMany(AcademicCalendarEvent::class);
    }

    // Used in: destroy() - AcademicCalendarController (checks linked syllabi)
    public function syllabi()
    {
        return $this->hasMany(Syllabus::class, 'academic_calendar_id');
    }

    // Used in: mount() - AcademicCalendarStep (Livewire)
    public function getFormattedSemester(): string
    {
        return $this->semester . ' Sem ' . $this->academic_year;
    }
}
