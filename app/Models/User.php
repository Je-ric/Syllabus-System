<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'account_status', // admin, oloi, faculty, dean
        'otp',
        'otp_expires_at',
        'phone_number',
        'office',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    // Used in:
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Used in: approve() - AccountApprovalController; assignRole() - AccountApprovalController
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }
    // Used in: assignDean() - OrganizationalHierarchyController; assignChair() - OrganizationalHierarchyController; assignFaculty() - OrganizationalHierarchyController
    public function hasRole(string $role): bool
    {
        return $this->roles()
                    ->where('name', $role)
                    ->exists();
    }

    // Used in:
    public function chairedDepartments()
    {
        return $this->hasMany(Department::class, 'chair_user_id');
    }

    // User (faculty) can prepare many syllabi
    // Used in:
    public function preparedSyllabi()
    {
        return $this->hasMany(Syllabus::class, 'prepared_by');
    }

    // User (chair) can concur many syllabi
    // Used in:
    public function concurredSyllabi()
    {
        return $this->hasMany(Syllabus::class, 'concurred_by');
    }

    // User (dean) can approve many syllabi
    // Used in:
    public function approvedSyllabi()
    {
        return $this->hasMany(Syllabus::class, 'approved_by');
    }

    // User can create many courses
    // Used in:
    public function createdCourses()
    {
        return $this->hasMany(Course::class, 'created_by');
    }

    // Used in: assignDean() - OrganizationalHierarchyController; assignChair() - OrganizationalHierarchyController; assignFaculty() - OrganizationalHierarchyController
    public function assignments()
    {
        return $this->hasMany(UserAssignment::class);
    }

    // Convenience helpers
    // Used in: hierarchyView() - OrganizationalHierarchyController
    public function isDean(): bool
    {
        return $this->assignments()->where('context', 'dean')->exists();
    }

    // Used in:
    public function isChairOfDepartment(int $departmentId): bool
    {
        return $this->assignments()
            ->where('context', 'chair')
            ->where('department_id', $departmentId)
            ->exists();
    }

    /**
     * Get the user's primary department assignment (chair or faculty)
     * Returns the first department assignment found
     *
     * @return \App\Models\UserAssignment|null
     */
    // Used in: objective_index() - ObjectiveController; assignChair() - OrganizationalHierarchyController; preselectFromUserAssignments() - ProgramSelector
    public function getPrimaryDepartmentAssignment(): ?UserAssignment
    {
        // Check for chair assignment first (most specific)
        $chairAssignment = $this->assignments()
            ->where('context', 'chair')
            ->whereNotNull('department_id')
            ->with('department.college')
            ->first();

        if ($chairAssignment) {
            return $chairAssignment;
        }

        // Check for faculty assignment
        $facultyAssignment = $this->assignments()
            ->where('context', 'faculty')
            ->whereNotNull('department_id')
            ->with('department.college')
            ->first();

        return $facultyAssignment;
    }

    /**
     * Get the user's primary college assignment (dean)
     * Returns the first college assignment found
     *
     * @return \App\Models\UserAssignment|null
     */
    // Used in: goal_index() - GoalController; assignDean() - OrganizationalHierarchyController; preselectFromUserAssignments() - ProgramSelector
    public function getPrimaryCollegeAssignment(): ?UserAssignment
    {
        return $this->assignments()
            ->where('context', 'dean')
            ->whereNotNull('college_id')
            ->with('college')
            ->first();
    }

    /**
     * Check if user is already assigned as dean (of any college)
     */
    // Used in: assignDean() - OrganizationalHierarchyController; assignChair() - OrganizationalHierarchyController
    public function isAssignedAsDean(): bool
    {
        return $this->assignments()
            ->where('context', 'dean')
            ->exists();
    }

    /**
     * Check if user is already assigned as chair (of any department)
     */
    // Used in: assignDean() - OrganizationalHierarchyController; assignChair() - OrganizationalHierarchyController
    public function isAssignedAsChair(): bool
    {
        return $this->assignments()
            ->where('context', 'chair')
            ->exists();
    }

    /**
     * Check if user is assigned as faculty to a specific department
     */
    // Used in:
    public function isFacultyOfDepartment(int $departmentId): bool
    {
        return $this->assignments()
            ->where('context', 'faculty')
            ->where('department_id', $departmentId)
            ->exists();
    }

    /**
     * Ensure user has faculty role and create faculty assignment if not exists
     */
    // Used in: assignDean() - OrganizationalHierarchyController; assignChair() - OrganizationalHierarchyController
    public function ensureFacultyRoleAndAssignment(?int $collegeId = null, ?int $departmentId = null): void
    {
        $facultyRole = Role::where('name', 'faculty')->firstOrCreate(['name' => 'faculty']);

        // Add faculty role if not already assigned
        if (!$this->roles()->where('role_id', $facultyRole->id)->exists()) {
            $this->roles()->attach($facultyRole->id);
        }

        // Create faculty assignment if not exists
        UserAssignment::firstOrCreate(
            [
                'user_id' => $this->id,
                'college_id' => $collegeId,
                'department_id' => $departmentId,
                'context' => 'faculty',
            ]
        );
    }

}
