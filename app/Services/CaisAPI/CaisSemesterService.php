<?php

namespace App\Services\CaisAPI;

use Illuminate\Support\Facades\Cache;

/**
 * CAIS semester data.
 * Used by: academic calendar create/edit, syllabus wizard step 1.
 */
class CaisSemesterService extends CaisHttpClient
{
    public function getSemesters(?string $status = null, ?string $year = null): array
    {
        $all = Cache::remember('cais.semesters', config('cais.cache.semesters'), function () {
            return data_get($this->get('semesters'), 'semesters', []);
        });

        return collect($all)
            ->when($status, fn ($c) => $c->where('semester_status', $status))
            ->when($year,   fn ($c) => $c->where('semester_year', $year))
            ->values()
            ->all();
    }

    public function getActiveSemester(): array
    {
        return Cache::remember('cais.semester.active', config('cais.cache.active_semester'), function () {
            return data_get($this->get('activeSemester'), 'semester', []);
        });
    }

    public function getSemester(int $caisSemesterId): array
    {
        return Cache::remember("cais.semester.{$caisSemesterId}", config('cais.cache.semesters'), function () use ($caisSemesterId) {
            $payload = $this->getWithId('semester', $caisSemesterId);
            return data_get($payload, 'semester', $payload);
        });
    }

    public function bustSemesterCache(?int $caisSemesterId = null): void
    {
        Cache::forget('cais.semesters');
        Cache::forget('cais.semester.active');
        if ($caisSemesterId !== null) {
            Cache::forget("cais.semester.{$caisSemesterId}");
        }
    }
}
