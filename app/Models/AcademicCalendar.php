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
        'is_active',
    ];

    protected $casts = [
        'start_date'       => 'date',
        'end_date'         => 'date',
        'cais_semester_id' => 'integer',
        'is_active'        => 'boolean',
    ];

    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_active', false);
    }

    public static function setActive(int $id): void
    {
        static::query()->update(['is_active' => false]);
        static::where('id', $id)->update(['is_active' => true]);
    }

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
