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
        'cais_college_id',
        'cais_department_id',
        'context', // dean | chair | faculty
    ];

    protected $casts = [
        'cais_college_id'    => 'integer',
        'cais_department_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function college()
    {
        return $this->belongsTo(College::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // ── Scopes ──────────────────────────────────────────────────────────────────

    public function scopeContext($query, string $context)
    {
        return $query->where('context', $context);
    }

    public function scopeDean($query)
    {
        return $query->where('context', 'dean');
    }

    public function scopeChair($query)
    {
        return $query->where('context', 'chair');
    }

    public function scopeFaculty($query)
    {
        return $query->where('context', 'faculty');
    }

    public function scopeForCollege($query, int $collegeId)
    {
        return $query->where('college_id', $collegeId);
    }

    public function scopeForCaisCollege($query, int $caisCollegeId)
    {
        return $query->where('cais_college_id', $caisCollegeId);
    }

    public function scopeForDepartment($query, int $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeForCaisDepartment($query, int $caisDepartmentId)
    {
        return $query->where('cais_department_id', $caisDepartmentId);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ── Helpers ─────────────────────────────────────────────────────────────────

    public static function findAssignment(string $context, int $userId, ?int $collegeId = null, ?int $departmentId = null): ?self
    {
        $query = static::where('context', $context)->where('user_id', $userId);

        if ($collegeId !== null) {
            $query->where('college_id', $collegeId);
        }
        if ($departmentId !== null) {
            $query->where('department_id', $departmentId);
        }

        return $query->first();
    }

    public static function removeAssignment(string $context, int $userId, ?int $collegeId = null, ?int $departmentId = null): int
    {
        $query = static::where('context', $context)->where('user_id', $userId);

        if ($collegeId !== null) {
            $query->where('college_id', $collegeId);
        }
        if ($departmentId !== null) {
            $query->where('department_id', $departmentId);
        }

        return $query->delete();
    }
}
