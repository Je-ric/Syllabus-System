<?php

namespace App\Services\CaisAPI;

use App\Exceptions\CaisApiException;
use Illuminate\Support\Facades\Cache;

/**
 * CAIS schedule, workload, and teaching load data.
 * All requests use the per-user Bearer token from session.
 * Used by: syllabus wizard step 2 (course components pre-fill), faculty workload page.
 */
class CaisScheduleService extends CaisHttpClient
{
    // -------------------------------------------------------------------------
    // Teaching Loads
    // -------------------------------------------------------------------------

    public function getTeachingLoads(int $caisUserId, ?int $caisSemesterId = null): array
    {
        $url = config('cais.endpoints.teaching_loads');

        $all = Cache::remember("cais.teaching_loads.{$caisUserId}", config('cais.cache.teaching_loads'), function () use ($url) {
            $payload = $this->getWithUserToken($url);
            return data_get($payload, 'teachingloads', data_get($payload, 'teaching_loads', []));
        });

        return collect($all)
            ->where('user_id', $caisUserId)
            ->when($caisSemesterId, fn ($c) => $c->where('semester_id', $caisSemesterId))
            ->map(fn ($tl) => $this->normalizeTeachingLoad($tl))
            ->values()
            ->all();
    }

    public function getTeachingLoad(int $teachingLoadId): array
    {
        return Cache::remember("cais.teaching_load.{$teachingLoadId}", config('cais.cache.teaching_loads'), function () use ($teachingLoadId) {
            $url     = str_replace('{id}', $teachingLoadId, config('cais.endpoints.teaching_load'));
            $payload = $this->getWithUserToken($url);
            $raw     = data_get($payload, 'teaching_load', $payload);
            return $raw ? $this->normalizeTeachingLoad($raw) : [];
        });
    }

    public function bustTeachingLoadCache(?int $teachingLoadId = null): void
    {
        Cache::forget('cais.teaching_loads');
        if ($teachingLoadId !== null) {
            Cache::forget("cais.teaching_load.{$teachingLoadId}");
        }
    }

    // -------------------------------------------------------------------------
    // Workloads
    // -------------------------------------------------------------------------

    public function getWorkloads(): array
    {
        $url = config('cais.endpoints.workloads');

        if (empty($url)) {
            throw new CaisApiException('CAIS workloads endpoint not configured.', 0);
        }

        return Cache::remember('cais.workloads.' . session('cais_token'), config('cais.cache.workloads'), function () use ($url) {
            return $this->getWithUserToken($url);
        });
    }

    // -------------------------------------------------------------------------
    // Schedules
    // -------------------------------------------------------------------------

    public function getSchedules(): array
    {
        return Cache::remember('cais.schedules', config('cais.cache.schedule'), function () {
            $url     = config('cais.endpoints.schedules');
            $payload = $this->getWithUserToken($url);
            return data_get($payload, 'schedule', data_get($payload, 'schedules', []));
        });
    }

    public function getClassSchedule(int $schedId): array
    {
        $raw = collect($this->getSchedules())->firstWhere('schedId', $schedId) ?? [];
        return $raw ? $this->normalizeSchedule($raw) : [];
    }

    public function bustScheduleCache(): void
    {
        Cache::forget('cais.schedules');
    }
}
