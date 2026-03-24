<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'account_status', // active, pending, rejected, disabled
        'phone_number',
        'office',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Used in: approve() - AccountApprovalController; 
    //          assignRole() - AccountApprovalController
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function otps()
    {
        return $this->hasMany(UserOtp::class);
    }

    // Used in: assignDean() - OrganizationalHierarchyController; 
    //          assignChair() - OrganizationalHierarchyController; 
    //          assignFaculty() - OrganizationalHierarchyController
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('name', $role)->exists();
    }

    // Used in: assignDean() - OrganizationalHierarchyController; 
    //          assignChair() - OrganizationalHierarchyController; 
    //          assignFaculty() - OrganizationalHierarchyController
    public function assignments()
    {
        return $this->hasMany(UserAssignment::class);
    }

    // Used in: hierarchyView() - OrganizationalHierarchyController
    public function isDean(): bool
    {
        return $this->assignments()->where('context', 'dean')->exists();
    }

    // Used in: objective_index() - ObjectiveController;
    //          assignChair() - OrganizationalHierarchyController; 
    //          preselectFromUserAssignments() - ProgramSelector
    public function getPrimaryDepartmentAssignment(): ?UserAssignment
    {
        // Chair assignment takes priority over faculty
        $chairAssignment = $this->assignments()
            ->where('context', 'chair')
            ->whereNotNull('department_id')
            ->with('department.college')
            ->first();

        if ($chairAssignment) {
            return $chairAssignment;
        }

        return $this->assignments()
            ->where('context', 'faculty')
            ->whereNotNull('department_id')
            ->with('department.college')
            ->first();
    }

    // Used in: goal_index() - GoalController; 
    //          assignDean() - OrganizationalHierarchyController; 
    //          preselectFromUserAssignments() - ProgramSelector
    public function getPrimaryCollegeAssignment(): ?UserAssignment
    {
        return $this->assignments()
            ->where('context', 'dean')
            ->whereNotNull('college_id')
            ->with('college')
            ->first();
    }

    // Used in: assignDean() - OrganizationalHierarchyController; 
    //          assignChair() - OrganizationalHierarchyController
    public function isAssignedAsDean(): bool
    {
        return $this->assignments()->where('context', 'dean')->exists();
    }

    // Used in: assignDean() - OrganizationalHierarchyController; 
    //          assignChair() - OrganizationalHierarchyController
    public function isAssignedAsChair(): bool
    {
        return $this->assignments()->where('context', 'chair')->exists();
    }

    // Used in: assignDean() - OrganizationalHierarchyController; 
    //          assignChair() - OrganizationalHierarchyController
    public function ensureFacultyRoleAndAssignment(?int $collegeId = null, ?int $departmentId = null): void
    {
        $facultyRole = Role::where('name', 'faculty')->firstOrCreate(['name' => 'faculty']);

        if (!$this->roles()->where('role_id', $facultyRole->id)->exists()) {
            $this->roles()->attach($facultyRole->id);
        }

        UserAssignment::firstOrCreate([
            'user_id'       => $this->id,
            'college_id'    => $collegeId,
            'department_id' => $departmentId,
            'context'       => 'faculty',
        ]);
    }
}
