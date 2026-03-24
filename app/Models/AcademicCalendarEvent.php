<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicCalendarEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_calendar_id',
        'type',
        'name',
        'date',
    ];

    public const TYPES = [
        'holiday',
        'exam',
        'break',
        'non_teaching',
        'other',
    ];

    // Used in: destroy() - AcademicCalendarEventController
    public function calendar()
    {
        return $this->belongsTo(AcademicCalendar::class, 'academic_calendar_id');
    }
}
