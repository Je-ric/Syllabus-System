<?php

namespace App\Services\Dashboard;

use App\Models\AcademicCalendar;
use App\Models\College;
use App\Models\Course;
use App\Models\Department;
use App\Models\Program;
use App\Models\Syllabus;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function __construct(private AcademicHealthService $academicHealth) {}

    public function resolveDashboardType(User $user): string
    {
        if ($user->hasRole('admin')) {
            return 'admin';
        }

        if ($user->hasRole('dean')) {
            return 'dean';
        }

        if ($user->hasRole('chair')) {
            return 'chair';
        }

        if ($user->hasRole('faculty')) {
            return 'faculty';
        }

        return 'default';
    }

    public function getDashboardData(User $user): array
    {
        $activeSemester = $this->getActiveSemester();

        return match ($this->resolveDashboardType($user)) {
            'admin' => array_merge(
                ['type' => 'admin', 'active_semester' => $activeSemester],
                $this->getAdminDashboard(),
            ),
            'dean' => array_merge(
                ['type' => 'dean', 'active_semester' => $activeSemester],
                $this->getDeanDashboard($user),
            ),
            'chair' => array_merge(
                ['type' => 'chair', 'active_semester' => $activeSemester],
                $this->getChairDashboard($user),
            ),
            'faculty' => array_merge(
                ['type' => 'faculty', 'active_semester' => $activeSemester],
                $this->getFacultyDashboard($user),
            ),
            default => [
                'type'            => 'default',
                'active_semester' => $activeSemester,
            ],
        };
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getActiveSemester(): ?array
    {
        $calendar = AcademicCalendar::active()->first();

        if (! $calendar) {
            return null;
        }

        return [
            'label'         => $calendar->getFormattedSemester(),
            'academic_year' => $calendar->academic_year,
            'semester'      => $calendar->semester,
            'start_date'    => $calendar->start_date?->format('M d, Y'),
            'end_date'      => $calendar->end_date?->format('M d, Y'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function getAdminDashboard(): array
    {
        $roleCounts = DB::table('user_roles')
            ->join('roles', 'roles.id', '=', 'user_roles.role_id')
            ->whereIn('roles.name', ['faculty', 'chair', 'dean', 'admin'])
            ->select('roles.name', DB::raw('COUNT(DISTINCT user_roles.user_id) as total'))
            ->groupBy('roles.name')
            ->pluck('total', 'name');

        return [
            'stats' => [
                ['key' => 'users', 'label' => 'Total Users', 'value' => User::count(), 'icon' => 'bx-group', 'color' => 'slate'],
                ['key' => 'faculty', 'label' => 'Faculty', 'value' => (int) ($roleCounts['faculty'] ?? 0), 'icon' => 'bx-user', 'color' => 'emerald'],
                ['key' => 'chairs', 'label' => 'Chairpersons', 'value' => (int) ($roleCounts['chair'] ?? 0), 'icon' => 'bx-user-pin', 'color' => 'blue'],
                ['key' => 'deans', 'label' => 'Deans', 'value' => (int) ($roleCounts['dean'] ?? 0), 'icon' => 'bx-medal', 'color' => 'violet'],
                ['key' => 'admins', 'label' => 'Administrators', 'value' => (int) ($roleCounts['admin'] ?? 0), 'icon' => 'bx-crown', 'color' => 'amber'],
                ['key' => 'colleges', 'label' => 'Colleges', 'value' => College::count(), 'icon' => 'bx-buildings', 'color' => 'blue'],
                ['key' => 'departments', 'label' => 'Departments', 'value' => Department::count(), 'icon' => 'bx-sitemap', 'color' => 'slate'],
                ['key' => 'programs', 'label' => 'Programs', 'value' => Program::count(), 'icon' => 'bx-network-chart', 'color' => 'emerald'],
                ['key' => 'curricula', 'label' => 'Curriculum Maps', 'value' => DB::table('course_curriculum_maps')->count(), 'icon' => 'bx-grid-alt', 'color' => 'violet'],
                ['key' => 'courses', 'label' => 'Courses', 'value' => Course::where('status', 'active')->count(), 'icon' => 'bx-book-content', 'color' => 'blue'],
                ['key' => 'syllabi', 'label' => 'Syllabi', 'value' => Syllabus::count(), 'icon' => 'bx-notepad', 'color' => 'emerald'],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function getChairDashboard(User $user): array
    {
        $assignment = $user->getPrimaryDepartmentAssignment();
        $department = $assignment?->department;

        if (! $department) {
            return [
                'no_assignment' => true,
                'department'    => null,
                'college'       => null,
                'stats'         => [],
                'syllabus_stats'=> [],
                'health'        => ['warnings' => [], 'mapping_issues' => []],
            ];
        }

        $programIds = $this->academicHealth->programIdsForDepartment((int) $department->id);
        $scopeStats = $this->buildScopeStats($programIds, (int) $department->id, null);
        $health = $this->academicHealth->summarizeForPrograms($programIds);

        return [
            'no_assignment'  => false,
            'department'     => [
                'id'   => $department->id,
                'name' => $department->name,
            ],
            'college'        => [
                'id'   => $department->college?->id,
                'name' => $department->college?->name,
            ],
            'stats'          => $scopeStats['overview'],
            'syllabus_stats' => $scopeStats['syllabus'],
            'health'         => $health,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function getFacultyDashboard(User $user): array
    {
        $assignment = $user->getPrimaryDepartmentAssignment();
        $department = $assignment?->department;

        if (! $department) {
            return [
                'no_assignment' => true,
                'department'    => null,
                'college'       => null,
                'stats'         => [],
                'syllabus_stats'=> [],
                'health'        => ['warnings' => [], 'mapping_issues' => []],
                'has_draft'      => false,
                'latest_draft_id'=> null,
                'draft_syllabi_count' => 0,
                'under_review_count' => 0,
                'for_revision_count' => 0,
                'approved_count' => 0,
                'recent_syllabi' => [],
            ];
        }

        // Get programs where the faculty teaches courses
        // $programIds = CourseComponent::where('user_id', $user->id)
        //     ->whereHas('course', function ($query) {
        //         $query->where('status', 'active');
        //     })
        //     ->with('course')
        //     ->get()
        // Get programs where the faculty teaches courses (via their syllabus components)
        $programIds = Syllabus::where('prepared_by', $user->id)
            ->whereHas('course', fn ($q) => $q->where('status', 'active'))
            ->with('course:id,program_id')
            ->get('course_id')
            ->pluck('course.program_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($programIds)) {
            return [
                'no_assignment'  => false,
                'no_courses'     => true,
                'department'     => [
                    'id'   => $department->id,
                    'name' => $department->name,
                ],
                'college'        => [
                    'id'   => $department->college?->id,
                    'name' => $department->college?->name,
                ],
                'stats'          => $this->emptyScopeOverview(null, $department->college_id),
                'syllabus_stats' => $this->emptySyllabusStats(),
                'health'         => ['warnings' => [], 'mapping_issues' => []],
                'has_draft'      => false,
                'latest_draft_id'=> null,
                'draft_syllabi_count' => 0,
                'under_review_count' => 0,
                'for_revision_count' => 0,
                'approved_count' => 0,
                'recent_syllabi' => [],
            ];
        }

        $scopeStats = $this->buildScopeStats($programIds, null, $department->college_id);
        $health = $this->academicHealth->summarizeForPrograms($programIds);

        // Get faculty-specific syllabus statistics
        $facultySyllabi = Syllabus::where('prepared_by', $user->id)
            ->whereIn('course_id', function ($query) use ($programIds) {
                $query->whereIn('program_id', $programIds);
            })
            ->get();

        $draftSyllabiCount = $facultySyllabi->where('status', 'draft')->count();
        $underReviewCount = $facultySyllabi->where('status', 'under_review')->count();
        $forRevisionCount = $facultySyllabi->where('status', 'for_revision')->count();
        $approvedCount = $facultySyllabi->where('status', 'approved')->count();

        // Get latest draft syllabus
        $latestDraft = $facultySyllabi->where('status', 'draft')
            ->sortByDesc('updated_at')
            ->first();

        // Get recent syllabus activity
        $recentSyllabi = $facultySyllabi
            ->sortByDesc('updated_at')
            ->take(5)
            ->map(function ($syllabus) {
                return [
                    'id' => $syllabus->id,
                    'course_code' => $syllabus->course?->course_code,
                    'title' => $syllabus->course?->course_title,
                    'status' => $syllabus->status,
                    'status_label' => $this->getStatusLabel($syllabus->status),
                    'updated_at' => $syllabus->updated_at?->diffForHumans(),
                ];
            })
            ->all();

        return [
            'no_assignment'  => false,
            'no_courses'     => false,
            'department'     => [
                'id'   => $department->id,
                'name' => $department->name,
            ],
            'college'        => [
                'id'   => $department->college?->id,
                'name' => $department->college?->name,
            ],
            'stats'          => $scopeStats['overview'],
            'syllabus_stats' => $scopeStats['syllabus'],
            'health'         => $health,
            'has_draft'      => $latestDraft !== null,
            'latest_draft_id'=> $latestDraft?->id,
            'draft_syllabi_count' => $draftSyllabiCount,
            'under_review_count' => $underReviewCount,
            'for_revision_count' => $forRevisionCount,
            'approved_count' => $approvedCount,
            'recent_syllabi' => $recentSyllabi,
        ];
    }

    /**
     * Get human-readable status label
     */
    private function getStatusLabel(string $status): string
    {
        return match($status) {
            'draft' => 'Draft',
            'under_review' => 'Under Review',
            'for_revision' => 'For Revision',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function getDeanDashboard(User $user): array
    {
        $assignment = $user->getPrimaryCollegeAssignment();
        $college = $assignment?->college;

        if (! $college) {
            return [
                'no_assignment' => true,
                'college'       => null,
                'stats'         => [],
                'departments'   => [],
                'syllabus_stats'=> [],
                'health'        => ['warnings' => [], 'mapping_issues' => []],
            ];
        }

        $programIds = $this->academicHealth->programIdsForCollege((int) $college->id);
        $scopeStats = $this->buildScopeStats($programIds, null, (int) $college->id);
        $health = $this->academicHealth->summarizeForPrograms($programIds);

        $departments = Department::query()
            ->where('college_id', $college->id)
            ->withCount('programs')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Department $department) => [
                'id'            => $department->id,
                'name'          => $department->name,
                'program_count' => $department->programs_count,
            ])
            ->all();

        return [
            'no_assignment'  => false,
            'college'        => [
                'id'   => $college->id,
                'name' => $college->name,
            ],
            'stats'          => $scopeStats['overview'],
            'departments'    => $departments,
            'syllabus_stats' => $scopeStats['syllabus'],
            'health'         => $health,
        ];
    }

    /**
     * @param  array<int>  $programIds
     * @return array{overview: list<array<string, mixed>>, syllabus: list<array<string, mixed>>}
     */
    private function buildScopeStats(array $programIds, ?int $departmentId, ?int $collegeId): array
    {
        if ($programIds === []) {
            return [
                'overview' => $this->emptyScopeOverview($departmentId, $collegeId),
                'syllabus' => $this->emptySyllabusStats(),
            ];
        }

        $courseQuery = Course::query()
            ->whereIn('program_id', $programIds)
            ->where('status', 'active');

        $courseCount = (clone $courseQuery)->count();
        $courseIds = (clone $courseQuery)->pluck('id')->all();

        $syllabusQuery = Syllabus::query()->whereIn('course_id', $courseIds);
        $totalSyllabi = (clone $syllabusQuery)->count();
        $draftSyllabi = (clone $syllabusQuery)->where('status', 'draft')->count();

        $activeCalendar = AcademicCalendar::active()->first();
        $syllabiForActiveSemester = 0;
        $coursesWithoutSyllabus = 0;

        if ($activeCalendar && $courseIds !== []) {
            $syllabiForActiveSemester = Syllabus::query()
                ->whereIn('course_id', $courseIds)
                ->where('academic_calendar_id', $activeCalendar->id)
                ->count();

            $coursesWithActiveSyllabus = Syllabus::query()
                ->whereIn('course_id', $courseIds)
                ->where('academic_calendar_id', $activeCalendar->id)
                ->distinct('course_id')
                ->count('course_id');

            $coursesWithoutSyllabus = max(0, $courseCount - $coursesWithActiveSyllabus);
        }

        $programCount = count($programIds);
        $curriculumCount = DB::table('course_curriculum_maps')
            ->whereIn('course_id', $courseIds)
            ->count();

        $overview = [
            ['key' => 'programs', 'label' => 'Programs', 'value' => $programCount, 'icon' => 'bx-network-chart', 'color' => 'emerald'],
            ['key' => 'courses', 'label' => 'Active Courses', 'value' => $courseCount, 'icon' => 'bx-book-content', 'color' => 'blue'],
            ['key' => 'curricula', 'label' => 'Curriculum Maps', 'value' => $curriculumCount, 'icon' => 'bx-grid-alt', 'color' => 'violet'],
            ['key' => 'syllabi', 'label' => 'Syllabi', 'value' => $totalSyllabi, 'icon' => 'bx-notepad', 'color' => 'slate'],
        ];

        if ($departmentId !== null) {
            array_unshift($overview, [
                'key'   => 'departments',
                'label' => 'Department',
                'value' => 1,
                'icon'  => 'bx-sitemap',
                'color' => 'slate',
            ]);
        }

        if ($collegeId !== null) {
            $departmentCount = Department::where('college_id', $collegeId)->count();
            array_unshift($overview, [
                'key'   => 'departments',
                'label' => 'Departments',
                'value' => $departmentCount,
                'icon'  => 'bx-sitemap',
                'color' => 'slate',
            ]);
        }

        $syllabusStats = [
            ['key' => 'total', 'label' => 'Total Syllabi', 'value' => $totalSyllabi, 'icon' => 'bx-notepad', 'color' => 'slate'],
            ['key' => 'draft', 'label' => 'Draft Syllabi', 'value' => $draftSyllabi, 'icon' => 'bx-edit', 'color' => 'amber'],
        ];

        if ($activeCalendar) {
            $syllabusStats[] = [
                'key'   => 'active_semester',
                'label' => 'Syllabi This Semester',
                'value' => $syllabiForActiveSemester,
                'icon'  => 'bx-calendar',
                'color' => 'emerald',
            ];

            if ($coursesWithoutSyllabus > 0) {
                $syllabusStats[] = [
                    'key'   => 'missing',
                    'label' => 'Courses Without Syllabus',
                    'value' => $coursesWithoutSyllabus,
                    'icon'  => 'bx-error-circle',
                    'color' => 'rose',
                ];
            }
        }

        return [
            'overview' => $overview,
            'syllabus' => $syllabusStats,
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function emptyScopeOverview(?int $departmentId, ?int $collegeId): array
    {
        $overview = [];

        if ($collegeId !== null) {
            $overview[] = [
                'key' => 'departments', 'label' => 'Departments',
                'value' => Department::where('college_id', $collegeId)->count(),
                'icon' => 'bx-sitemap', 'color' => 'slate',
            ];
        }

        if ($departmentId !== null) {
            $overview[] = [
                'key' => 'departments', 'label' => 'Department',
                'value' => 1, 'icon' => 'bx-sitemap', 'color' => 'slate',
            ];
        }

        $overview[] = ['key' => 'programs', 'label' => 'Programs', 'value' => 0, 'icon' => 'bx-network-chart', 'color' => 'emerald'];
        $overview[] = ['key' => 'courses', 'label' => 'Active Courses', 'value' => 0, 'icon' => 'bx-book-content', 'color' => 'blue'];
        $overview[] = ['key' => 'curricula', 'label' => 'Curriculum Maps', 'value' => 0, 'icon' => 'bx-grid-alt', 'color' => 'violet'];
        $overview[] = ['key' => 'syllabi', 'label' => 'Syllabi', 'value' => 0, 'icon' => 'bx-notepad', 'color' => 'slate'];

        return $overview;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function emptySyllabusStats(): array
    {
        return [
            ['key' => 'total', 'label' => 'Total Syllabi', 'value' => 0, 'icon' => 'bx-notepad', 'color' => 'slate'],
            ['key' => 'draft', 'label' => 'Draft Syllabi', 'value' => 0, 'icon' => 'bx-edit', 'color' => 'amber'],
        ];
    }
}
