<?php

namespace App\Services;

use App\Services\CaisAPI\CaisAuthService;
use App\Services\CaisAPI\CaisCollegeService;
use App\Services\CaisAPI\CaisDepartmentService;
use App\Services\CaisAPI\CaisScheduleService;
use App\Services\CaisAPI\CaisSemesterService;
use App\Services\CaisAPI\CaisUserService;

/**
 * Aggregator — delegates to focused domain services.
 * Kept here so all existing callers (controllers, commands) need zero changes.
 */
class CaisApiService
{
    public function __construct(
        private readonly CaisAuthService       $auth,
        private readonly CaisCollegeService    $colleges,
        private readonly CaisDepartmentService $departments,
        private readonly CaisUserService       $users,
        private readonly CaisSemesterService   $semesters,
        private readonly CaisScheduleService   $schedules,
    ) {}

    // Auth
    public function verifyUser(string $email, string $password): ?array
    {
        return $this->auth->verifyUser($email, $password);
    }

    // Colleges
    public function getColleges(): array                          { return $this->colleges->getColleges(); }
    public function getCollege(int $id): array                    { return $this->colleges->getCollege($id); }
    public function bustCollegeCache(): void                      { $this->colleges->bustCollegeCache(); }

    // Departments
    public function getDepartments(?int $collegeId = null): array { return $this->departments->getDepartments($collegeId); }
    public function getDepartment(int $id): array                 { return $this->departments->getDepartment($id); }
    public function bustDepartmentCache(): void                   { $this->departments->bustDepartmentCache(); }

    // Users / Faculty
    public function getUsers(): array                             { return $this->users->getUsers(); }
    public function getFacultyProfile(int $id): array             { return $this->users->getFacultyProfile($id); }
    public function getFacultyByDepartment(int $deptId): array    { return $this->users->getFacultyByDepartment($deptId); }
    public function bustUserCache(?int $id = null): void          { $this->users->bustUserCache($id); }

    // Semesters
    public function getSemesters(?string $status = null, ?string $year = null): array { return $this->semesters->getSemesters($status, $year); }
    public function getActiveSemester(): array                    { return $this->semesters->getActiveSemester(); }
    public function getSemester(int $id): array                   { return $this->semesters->getSemester($id); }
    public function bustSemesterCache(?int $id = null): void      { $this->semesters->bustSemesterCache($id); }

    // Teaching Loads
    public function getTeachingLoads(int $userId, ?int $semesterId = null): array { return $this->schedules->getTeachingLoads($userId, $semesterId); }
    public function getTeachingLoad(int $id): array               { return $this->schedules->getTeachingLoad($id); }
    public function bustTeachingLoadCache(?int $id = null): void  { $this->schedules->bustTeachingLoadCache($id); }

    // Workloads
    public function getWorkloads(): array                         { return $this->schedules->getWorkloads(); }

    // Schedules
    public function getSchedules(): array                         { return $this->schedules->getSchedules(); }
    public function getClassSchedule(int $schedId): array         { return $this->schedules->getClassSchedule($schedId); }
    public function bustScheduleCache(): void                     { $this->schedules->bustScheduleCache(); }
}
