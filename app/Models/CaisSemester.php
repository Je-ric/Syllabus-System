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

    // Used in: AcademicCalendarStep (syllabus wizard) — map CAIS semester to CSMS calendar;
    //          WorkloadSyncService — syncing semesters
    public function academicCalendar()
    {
        return $this->belongsTo(AcademicCalendar::class);
    }

    // Used in: CaisClassSchedule — schedules belonging to this semester;
    //          WorkloadSyncService — syncing class schedules
    public function classSchedules()
    {
        return $this->hasMany(CaisClassSchedule::class);
    }

    // Used in: CaisTeachingLoad — teaching loads for this semester;
    //          WorkloadSyncService — syncing teaching loads
    public function teachingLoads()
    {
        return $this->hasMany(CaisTeachingLoad::class);
    }

    // Used in: WorkloadSyncService — filter active semesters
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
