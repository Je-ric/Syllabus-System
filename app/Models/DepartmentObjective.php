<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentObjective extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'dept_obj_code',
        'objective_text',
        'cais_department_id',
    ];

    protected $casts = [
        'cais_department_id' => 'integer',
    ];

    // Used in: objective_update() - ObjectiveController;
    //          objective_destroy() - ObjectiveController;
    //          sharedData() - SyllabusPreviewService
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
