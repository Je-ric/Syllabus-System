<?php

namespace App\Services\CaisAPI;

use Illuminate\Support\Facades\Cache;

/**
 * CAIS faculty/user data.
 * Used by: admin org hierarchy (assign faculty), syllabus course components.
 */
class CaisUserService extends CaisHttpClient
{
    public function getUsers(): array
    {
        return Cache::remember('cais.users', config('cais.cache.user_list'), function () {
            return data_get($this->get('users'), 'users', []);
        });
    }

    public function getFacultyProfile(int $caisUserId): array
    {
        return Cache::remember("cais.user.{$caisUserId}", config('cais.cache.user_profile'), function () use ($caisUserId) {
            $payload = $this->getWithId('user_profile', $caisUserId);
            $raw     = data_get($payload, 'user', data_get($payload, 'faculty', $payload));
            return $raw ? $this->normalizeUser($raw) : [];
        });
    }

    public function getFacultyByDepartment(int $caisDepartmentId): array
    {
        return collect($this->getUsers())
            ->filter(fn ($u) => data_get($u, 'dept_id') == $caisDepartmentId)
            ->map(fn ($u) => $this->normalizeUser($u))
            ->values()
            ->all();
    }

    public function bustUserCache(?int $caisUserId = null): void
    {
        Cache::forget('cais.users');
        if ($caisUserId !== null) {
            Cache::forget("cais.user.{$caisUserId}");
        }
    }
}
