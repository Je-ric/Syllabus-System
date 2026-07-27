<?php

namespace App\Services\CaisAPI;

use Illuminate\Support\Facades\Cache;

/**
 * CAIS department data.
 * Used by: admin user assignment, department objectives, org hierarchy.
 */
class CaisDepartmentService extends CaisHttpClient
{
    public function getDepartments(?int $caisCollegeId = null): array
    {
        $all = Cache::remember('cais.departments', config('cais.cache.departments'), function () {
            return data_get($this->get('departments'), 'departments', []);
        });

        if ($caisCollegeId === null) {
            return $all;
        }

        return collect($all)->where('college_id', $caisCollegeId)->values()->all();
    }

    public function getDepartment(int $caisDepartmentId): array
    {
        return collect($this->getDepartments())
            ->first(fn ($d) => data_get($d, 'dept_id', data_get($d, 'department_id')) == $caisDepartmentId)
            ?? [];
    }

    public function bustDepartmentCache(): void
    {
        Cache::forget('cais.departments');
    }
}
