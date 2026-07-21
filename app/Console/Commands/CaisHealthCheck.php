<?php

namespace App\Console\Commands;

use App\Exceptions\CaisApiException;
use App\Services\CaisApiService;
use Illuminate\Console\Command;

class CaisHealthCheck extends Command
{
    // command - php artisan cais:health
    // change ids below to match Postman mock data if needed
    protected $signature = 'cais:health
                            {--user-id=25 : CAIS user ID to use for teaching-loads and faculty profile checks}
                            {--semester-id=1 : CAIS semester ID to use for the single-semester check}
                            {--fresh : Bust all caches before running checks}';

    protected $description = 'Ping every configured CAIS endpoint and verify connectivity.';

    public function handle(CaisApiService $cais): int
    {
        $userId     = (int) $this->option('user-id');
        $semesterId = (int) $this->option('semester-id');
        $fresh      = (bool) $this->option('fresh');

        // ── Config summary ────────────────────────────────────────────────────
        $this->info('CAIS API Health Check');
        $this->line('API Key  : ' . (config('cais.key') ? '*** (set)' : '<fg=red>(NOT SET — check CAIS_API_KEY in .env)</>'));
        $this->line('Timeout  : ' . config('cais.timeout') . 's');
        $this->newLine();

        $this->line('Configured endpoints:');
        $allConfigured = true;
        foreach (config('cais.endpoints') as $key => $url) {
            if (empty($url)) {
                $this->line('  <fg=red>✗</> ' . str_pad($key, 20) . ' (NOT SET)');
                $allConfigured = false;
            } else {
                $this->line('  <fg=green>✓</> ' . str_pad($key, 20) . ' ' . $url);
            }
        }
        $this->newLine();

        if (! $allConfigured) {
            $this->warn('One or more endpoints are not configured. Fix your .env before running checks.');
            return self::FAILURE;
        }

        // ── Optional cache bust ───────────────────────────────────────────────
        if ($fresh) {
            $this->line('Busting all CAIS caches...');
            $cais->bustCollegeCache();
            $cais->bustDepartmentCache();
            $cais->bustUserCache();
            $cais->bustSemesterCache();
            $cais->bustTeachingLoadCache();
            $cais->bustScheduleCache();
            $this->line('Done.');
            $this->newLine();
        }

        // ── Endpoint checks ───────────────────────────────────────────────────
        // Format: 'Label' => [type, callable]
        //   type 'list'   — expects an array, shows count
        //   type 'single' — expects an array, shows keys or "empty"
        $checks = [
            // List endpoints
            'Colleges (list)'           => ['list',   fn () => $cais->getColleges()],
            'Departments (list)'        => ['list',   fn () => $cais->getDepartments()],
            'Faculty / Users (list)'    => ['list',   fn () => $cais->getUsers()],
            'Semesters (list)'          => ['list',   fn () => $cais->getSemesters()],
            'Schedules (list)'          => ['list',   fn () => $cais->getSchedules()],
            'Teaching Loads (list)'     => ['list',   fn () => $cais->getTeachingLoads($userId)],

            // Single / dedicated endpoints
            'Active Semester'           => ['single', fn () => $cais->getActiveSemester()],
            "Semester ID {$semesterId}" => ['single', fn () => $cais->getSemester($semesterId)],
            "Faculty Profile ID {$userId}" => ['single', fn () => $cais->getFacultyProfile($userId)],
            "Teaching Load ID 1"        => ['single', fn () => $cais->getTeachingLoad(1)],
        ];

        $passed = 0;
        $failed = 0;

        foreach ($checks as $label => [$type, $check]) {
            try {
                $result = $check();

                if ($type === 'list') {
                    $count = count($result);
                    $note  = "{$count} record(s)";
                } else {
                    $note = empty($result) ? '<fg=yellow>empty response</>' : implode(', ', array_keys($result));
                }

                $this->line("  <fg=green>✓</> {$label} — {$note}");
                $passed++;

            } catch (CaisApiException $e) {
                $this->line("  <fg=red>✗</> {$label} — [{$e->getStatusCode()}] {$e->getMessage()}");
                $failed++;
            }
        }

        // ── Summary ───────────────────────────────────────────────────────────
        $this->newLine();
        $this->line("Results: <fg=green>{$passed} passed</>, <fg=red>{$failed} failed</>");

        if ($failed === 0) {
            $this->info('All checks passed.');
            return self::SUCCESS;
        }

        $this->warn('One or more checks failed. Review the output above.');
        return self::FAILURE;
    }
}
