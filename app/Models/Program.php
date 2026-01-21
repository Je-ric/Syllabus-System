<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
                'name', 
                'bor_approval_no', 
                'bor_approval_date'
                ];

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'program_departments')
                    ->withPivot('role') // similar to Department model, programs can belong to multiple departments with different roles
                    ->withTimestamps();
    }
}
