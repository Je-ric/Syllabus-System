<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaisSemester extends Model
{
    protected $fillable = [
        'external_id',
        'name',
        'number',
        'year',
        'status',
        'academic_calendar_id',
        'synced_at',
    ];

    protected $casts = [
        'external_id'          => 'integer',
        'number'               => 'integer',
        'academic_calendar_id' => 'integer',
        'synced_at'            => 'datetime',
    ];

    // Used in: AcademicCalendarStep (syllabus wizard) — map CAIS semester to CSMS calendar
    public function academicCalendar()
    {
        return $this->belongsTo(AcademicCalendar::class);
    }

    // Used in: CaisClassSchedule — schedules belonging to this semester
    public function classSchedules()
    {
        return $this->hasMany(CaisClassSchedule::class);
    }

    // Used in: CaisTeachingLoad — teaching loads for this semester
    public function teachingLoads()
    {
        return $this->hasMany(CaisTeachingLoad::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
