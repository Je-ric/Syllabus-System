<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyllabusWeek extends Model
{
    use HasFactory;

    protected $fillable = [
        'syllabus_id',
        'week_no',
        'start_date',
        'end_date',
        'is_exam_week',
        'exam_type',
    ];

    public function syllabus()
    {
        return $this->belongsTo(Syllabus::class);
    }

    public function contents()
    {
        return $this->hasMany(WeekContent::class);
    }
}
