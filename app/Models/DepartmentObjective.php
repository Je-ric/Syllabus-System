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
                'objective_text'
                ];

    // many objectives to one department
    // Used in:
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
