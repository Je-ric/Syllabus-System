<?php

namespace App\Services;

use App\Exceptions\CaisApiException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Read-only proxy to the CAIS (LMS) API.
 * Auth: X-API-KEY header (CAIS_API_KEY in .env).
 * All methods return plain arrays. Empty array = not found / no data.
 */
class CaisApiService
{
    private string $key;
    private int $timeout;

    public function __construct()
    {
        $this->key     = config('cais.key');
        $this->timeout = config('cais.timeout');
    }

    // -------------------------------------------------------------------------
    // Colleges — used by: admin user assignment, org hierarchy, college goals
    // -------------------------------------------------------------------------

    /** Get all colleges. */
    public function getColleges(): array
    {
        return Cache::remember('cais.colleges', config('cais.cache.colleges'), function () {
            return data_get($this->get('colleges'), 'colleges', []);
        });
    }

    /** Get a specific college. $caisCollegeId = college_id from CAIS. */
    public function getCollege(int $caisCollegeId): array
    {
        return collect($this->getColleges())->firstWhere('college_id', $caisCollegeId) ?? [];
    }

    /** Bust colleges cache. Call after admin triggers a manual sync. */
    public function bustCollegeCache(): void
    {
        Cache::forget('cais.colleges');
    }

    // -------------------------------------------------------------------------
    // Departments — used by: admin user assignment, department objectives, org hierarchy
    // -------------------------------------------------------------------------

    /** Get all departments. Pass $caisCollegeId to filter by college. */
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

    /** Get a specific department. $caisDepartmentId = dept_id from CAIS. */
    public function getDepartment(int $caisDepartmentId): array
    {
        return collect($this->getDepartments())
            ->first(fn ($d) => data_get($d, 'dept_id', data_get($d, 'department_id')) == $caisDepartmentId)
            ?? [];
    }

    /** Bust departments cache. */
    public function bustDepartmentCache(): void
    {
        Cache::forget('cais.departments');
    }

    // -------------------------------------------------------------------------
    // Faculty / Users — used by: admin org hierarchy (assign faculty), syllabus course components
    // -------------------------------------------------------------------------

    /** Get all faculty users. */
    public function getUsers(): array
    {
        return Cache::remember('cais.users', config('cais.cache.user_list'), function () {
            return data_get($this->get('users'), 'users', []);
        });
    }

    /**
     * Get a specific faculty profile. $caisUserId = id/user_id from CAIS.
     * Hits /api/faculty/{id} directly — returns: { cais_user_id, first_name, last_name, email, user_type }
     */
    public function getFacultyProfile(int $caisUserId): array
    {
        return Cache::remember("cais.user.{$caisUserId}", config('cais.cache.user_profile'), function () use ($caisUserId) {
            $payload = $this->getWithId('user_profile', $caisUserId);
            $raw     = data_get($payload, 'user', data_get($payload, 'faculty', $payload));
            return $raw ? $this->normalizeUser($raw) : [];
        });
    }

    /** Get all faculty belonging to a department. $caisDepartmentId = dept_id from CAIS. */
    public function getFacultyByDepartment(int $caisDepartmentId): array
    {
        return collect($this->getUsers())
            ->filter(fn ($u) => data_get($u, 'dept_id') == $caisDepartmentId)
            ->map(fn ($u) => $this->normalizeUser($u))
            ->values()
            ->all();
    }

    /** Bust users list cache and optionally a specific user's profile cache. */
    public function bustUserCache(?int $caisUserId = null): void
    {
        Cache::forget('cais.users');
        if ($caisUserId !== null) {
            Cache::forget("cais.user.{$caisUserId}");
        }
    }

    // -------------------------------------------------------------------------
    // Semesters — used by: academic calendar create/edit, syllabus wizard step 1
    // -------------------------------------------------------------------------

    /** Get all semesters. Filter by $status (e.g. 'active') or $year (e.g. '2024-2025'). */
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

    /** Get the current active semester. Hits /api/semesters/active directly. */
    public function getActiveSemester(): array
    {
        return Cache::remember('cais.semester.active', config('cais.cache.active_semester'), function () {
            return data_get($this->get('activeSemester'), 'semester', []);
        });
    }

    /** Get a specific semester. $caisSemesterId = semester_id from CAIS. Hits /api/semesters/{id} directly. */
    public function getSemester(int $caisSemesterId): array
    {
        return Cache::remember("cais.semester.{$caisSemesterId}", config('cais.cache.semesters'), function () use ($caisSemesterId) {
            $payload = $this->getWithId('semester', $caisSemesterId);
            return data_get($payload, 'semester', $payload);
        });
    }

    /** Bust semesters cache (list + active + optionally one specific semester). */
    public function bustSemesterCache(?int $caisSemesterId = null): void
    {
        Cache::forget('cais.semesters');
        Cache::forget('cais.semester.active');
        if ($caisSemesterId !== null) {
            Cache::forget("cais.semester.{$caisSemesterId}");
        }
    }

    // -------------------------------------------------------------------------
    // Teaching Loads — used by: syllabus wizard step 2 (course components pre-fill)
    // -------------------------------------------------------------------------

    /**
     * Get teaching loads for a faculty user. $caisUserId = user_id from CAIS.
     * Pass $caisSemesterId to filter by semester.
     * Returns: [ { teaching_load_id, semester_id, user_id, sched_id, is_deleted } ]
     */
    public function getTeachingLoads(int $caisUserId, ?int $caisSemesterId = null): array
    {
        $all = Cache::remember('cais.teaching_loads', config('cais.cache.teaching_loads'), function () {
            $payload = $this->get('teaching_loads');
            return data_get($payload, 'teachingloads', data_get($payload, 'teaching_loads', []));
        });

        return collect($all)
            ->where('user_id', $caisUserId)
            ->when($caisSemesterId, fn ($c) => $c->where('semester_id', $caisSemesterId))
            ->map(fn ($tl) => $this->normalizeTeachingLoad($tl))
            ->values()
            ->all();
    }

    /**
     * Get a specific teaching load. $teachingLoadId = teaching_load_id from CAIS.
     * Hits /api/teaching-loads/{id} directly.
     */
    public function getTeachingLoad(int $teachingLoadId): array
    {
        return Cache::remember("cais.teaching_load.{$teachingLoadId}", config('cais.cache.teaching_loads'), function () use ($teachingLoadId) {
            $payload = $this->getWithId('teaching_load', $teachingLoadId);
            $raw     = data_get($payload, 'teaching_load', $payload);
            return $raw ? $this->normalizeTeachingLoad($raw) : [];
        });
    }

    /** Bust teaching loads cache (list + optionally one specific load). */
    public function bustTeachingLoadCache(?int $teachingLoadId = null): void
    {
        Cache::forget('cais.teaching_loads');
        if ($teachingLoadId !== null) {
            Cache::forget("cais.teaching_load.{$teachingLoadId}");
        }
    }

    // -------------------------------------------------------------------------
    // Schedules — used by: syllabus wizard step 2 (schedule details lookup)
    // -------------------------------------------------------------------------

    /** Get all class schedules. */
    public function getSchedules(): array
    {
        return Cache::remember('cais.schedules', config('cais.cache.schedule'), function () {
            $payload = $this->get('schedules');
            return data_get($payload, 'schedule', data_get($payload, 'schedules', []));
        });
    }

    /**
     * Get a specific class schedule. $schedId = schedId from CAIS.
     * Filtered locally from the cached list.
     * Returns: { sched_id, subject_code, subject_title, semester_id, course_id, dept_id }
     */
    public function getClassSchedule(int $schedId): array
    {
        $raw = collect($this->getSchedules())->firstWhere('schedId', $schedId) ?? [];
        return $raw ? $this->normalizeSchedule($raw) : [];
    }

    /** Bust schedules cache. */
    public function bustScheduleCache(): void
    {
        Cache::forget('cais.schedules');
    }

    // -------------------------------------------------------------------------
    // Normalizers — flatten inconsistent CAIS field names into stable CSMS keys
    // -------------------------------------------------------------------------

    private function normalizeUser(array $u): array
    {
        return [
            'cais_user_id' => data_get($u, 'id', data_get($u, 'user_id')),
            'first_name'   => data_get($u, 'first_name', data_get($u, 'user_fname')),
            'last_name'    => data_get($u, 'last_name', data_get($u, 'user_lname')),
            'email'        => data_get($u, 'email'),
            'user_type'    => data_get($u, 'user_type'),
        ];
    }

    private function normalizeSchedule(array $s): array
    {
        return [
            'sched_id'      => data_get($s, 'schedId'),
            'subject_code'  => data_get($s, 'subject_code'),
            'subject_title' => data_get($s, 'subject_title'),
            'semester_id'   => data_get($s, 'semester_id'),
            'course_id'     => data_get($s, 'course_id'),
            'dept_id'       => data_get($s, 'dept_id'),
        ];
    }

    private function normalizeTeachingLoad(array $tl): array
    {
        return [
            'teaching_load_id' => data_get($tl, 'teaching_load_id'),
            'semester_id'      => data_get($tl, 'semester_id'),
            'user_id'          => data_get($tl, 'user_id'),
            'sched_id'         => data_get($tl, 'schedId'),
            'is_deleted'       => (bool) data_get($tl, 'is_deleted'),
        ];
    }

    // -------------------------------------------------------------------------
    // HTTP internals
    // -------------------------------------------------------------------------

    /** GET a list endpoint. $endpointKey maps to config('cais.endpoints.*'). */
    private function get(string $endpointKey): array
    {
        $url = config("cais.endpoints.{$endpointKey}");

        if (empty($url)) {
            throw new CaisApiException("CAIS endpoint not configured: {$endpointKey}", 0);
        }

        return $this->request($url);
    }

    /** GET a single-record endpoint. Replaces {id} in the URL template from config. */
    private function getWithId(string $endpointKey, int $id): array
    {
        $template = config("cais.endpoints.{$endpointKey}");

        if (empty($template)) {
            throw new CaisApiException("CAIS endpoint not configured: {$endpointKey}", 0);
        }

        return $this->request(str_replace('{id}', $id, $template));
    }

    private function request(string $url): array
    {
        try {
            $response = Http::withHeaders([
                    'X-API-KEY' => $this->key,
                    'Accept'    => 'application/json',
                ])
                ->timeout($this->timeout)
                ->get($url);

            if ($response->successful()) {
                return $response->json() ?? [];
            }

            $status  = $response->status();
            $message = $response->json('message') ?? "CAIS API error: {$url}";

            Log::warning('CAIS API non-success response', ['url' => $url, 'status' => $status, 'message' => $message]);

            throw new CaisApiException($message, $status);

        } catch (ConnectionException $e) {
            Log::error('CAIS API connection failed', ['url' => $url, 'error' => $e->getMessage()]);
            throw new CaisApiException("Could not connect to CAIS: {$e->getMessage()}", 0, $e);
        }
    }
}
