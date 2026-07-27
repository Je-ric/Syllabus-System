<?php

namespace App\Services\CaisAPI;

use Illuminate\Support\Facades\Cache;

/**
 * CAIS college data.
 * Used by: admin user assignment, org hierarchy, college goals.
 */
class CaisCollegeService extends CaisHttpClient
{
    public function getColleges(): array
    {
        return Cache::remember('cais.colleges', config('cais.cache.colleges'), function () {
            return data_get($this->get('colleges'), 'colleges', []);
        });
    }

    public function getCollege(int $caisCollegeId): array
    {
        return collect($this->getColleges())->firstWhere('college_id', $caisCollegeId) ?? [];
    }

    public function bustCollegeCache(): void
    {
        Cache::forget('cais.colleges');
    }
}
