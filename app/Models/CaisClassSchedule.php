<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaisClassSchedule extends Model
{
    protected $fillable = [
        'external_id',
        'external_semester_id',
        'external_department_id',
        'external_course_id',
        'cais_semester_id',
        'department_id',
        'course_id',
        'subject_code',
        'subject_title',
        'units',
        'section',
        'room',
        'time',
        'class_type',
        'lab_type',
        'synced_at',
    ];

    protected $casts = [
        'external_id'            => 'integer',
        'external_semester_id'   => 'integer',
        'external_department_id' => 'integer',
        'external_course_id'     => 'integer',
        'cais_semester_id'       => 'integer',
        'department_id'          => 'integer',
        'course_id'              => 'integer',
        'units'                  => 'decimal:2',
        'synced_at'              => 'datetime',
    ];

    // Used in: syllabus wizard step 2 — pre-fill schedule, room, time, subject details
    public function caisSemester()
    {
        return $this->belongsTo(CaisSemester::class);
    }

    // Used in: filtering schedules by department
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Used in: WorkloadSyncService — matched local CSMS course for this schedule
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Used in: CaisTeachingLoad — loads assigned to this schedule
    public function teachingLoads()
    {
        return $this->hasMany(CaisTeachingLoad::class);
    }
}
