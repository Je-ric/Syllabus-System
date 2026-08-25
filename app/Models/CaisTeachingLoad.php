<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaisTeachingLoad extends Model
{
    protected $fillable = [
        'external_id',
        'external_user_id',
        'external_semester_id',
        'external_schedule_id',
        'user_id',
        'cais_semester_id',
        'cais_class_schedule_id',
        'is_deleted',
        'synced_at',
    ];

    protected $casts = [
        'external_id'            => 'integer',
        'external_user_id'       => 'integer',
        'external_semester_id'   => 'integer',
        'external_schedule_id'   => 'integer',
        'user_id'                => 'integer',
        'cais_semester_id'       => 'integer',
        'cais_class_schedule_id' => 'integer',
        'is_deleted'             => 'boolean',
        'synced_at'              => 'datetime',
    ];

    // Used in: syllabus wizard step 2 — get the faculty user this load belongs to;
    //          WorkloadSyncService — syncing teaching loads
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Used in: syllabus wizard step 2 — get semester context;
    //          WorkloadSyncService — syncing teaching loads
    public function caisSemester()
    {
        return $this->belongsTo(CaisSemester::class);
    }

    // Used in: syllabus wizard step 2 — get schedule details (subject, room, time);
    //          WorkloadSyncService — syncing teaching loads
    public function classSchedule()
    {
        return $this->belongsTo(CaisClassSchedule::class, 'cais_class_schedule_id');
    }

    // Used in: WorkloadSyncService — exclude soft-deleted CAIS loads from queries
    public function scopeActive($query)
    {
        return $query->where('is_deleted', false);
    }
}
