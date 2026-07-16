<?php

namespace App\Models;

// use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    // use HasFactory, Notifiable, HasApiTokens;
    use HasFactory, Notifiable;

    protected $fillable = [
        'cais_user_id',
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
            'password'          => 'hashed',
            'cais_user_id'      => 'integer',
        ];
    }

    /**
     * Whether this account is linked to a CAIS user.
     * Used by CaisTeachingLoadController and syllabus wizard pre-fill.
     */
    public function hasCaisLink(): bool
    {
        return $this->cais_user_id !== null;
    }

    /**
     * Sync CAIS user details onto this local user row.
     * Called on every successful CAIS-verified login to keep data fresh.
     * Only updates fields CAIS owns — phone_number and office stay user-managed.
     * $caisUser = normalized array from CaisApiService::verifyUser() / normalizeUser().
     */
    public function syncFromCais(array $caisUser): void
    {
        $updates = [];

        $caisId = data_get($caisUser, 'cais_user_id');
        if ($caisId && $this->cais_user_id !== $caisId) {
            $updates['cais_user_id'] = $caisId;
        }

        $firstName = data_get($caisUser, 'first_name', '');
        $lastName  = data_get($caisUser, 'last_name', '');
        $fullName  = trim("{$firstName} {$lastName}");
        if ($fullName && $this->name !== $fullName) {
            $updates['name'] = $fullName;
        }

        if (! empty($updates)) {
            $this->update($updates);
        }
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

    public function consultationHours()
    {
        return $this->hasMany(UserConsultationHour::class)->orderByRaw(
            "FIELD(day,'Monday','Tuesday','Wednesday','Thursday','Friday')"
        );
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
