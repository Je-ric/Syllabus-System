<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'college_id',
        'department_id',
        'context', // dean | chair | faculty
    ];

    // Used in: collegesIndexData() - OrganizationalHierarchyService; 
    //          departmentsIndexData() - OrganizationalHierarchyService; 
    //          hierarchyView() - OrganizationalHierarchyController; 
    //          preselectFromUserAssignments() - ProgramSelector
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Used in: getPrimaryCollegeAssignment() - User; 
    //          preselectFromUserAssignments() - ProgramSelector
    public function college()
    {
        return $this->belongsTo(College::class);
    }

    // Used in: getPrimaryDepartmentAssignment() - User; 
    //          hierarchyView() - OrganizationalHierarchyController; 
    //          preselectFromUserAssignments() - ProgramSelector
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
