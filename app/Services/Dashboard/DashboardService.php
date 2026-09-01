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
    public function __construct() {}

    public function resolveDashboardType(User $user): string
    {
        // Admin takes priority — admins always have faculty role too (every active account does)
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

        $now = now();
        $startDate = $calendar->start_date;
        $endDate = $calendar->end_date;
        
        // Calculate current week number of semester
        $currentWeek = 1;
        $daysRemaining = 0;
        $totalWeeks = 1;

        if ($startDate && $endDate) {
            $totalWeeks = (int) ceil($startDate->diffInDays($endDate) / 7);
            if ($totalWeeks < 1) {
                $totalWeeks = 1;
            }

            if ($now->gte($startDate) && $now->lte($endDate)) {
                // Current date is within semester
                $daysPassed = (int) $startDate->diffInDays($now) + 1;
                $currentWeek = (int) ceil($daysPassed / 7);
                $daysRemaining = (int) $now->diffInDays($endDate);
            } elseif ($now->lt($startDate)) {
                // Semester hasn't started yet
                $currentWeek = 0;
                $daysRemaining = (int) $now->diffInDays($startDate);
            } else {
                // Semester has ended
                $currentWeek = $totalWeeks;
                $daysRemaining = 0;
            }
        }

        return [
            'label'           => $calendar->getFormattedSemester(),
            'academic_year'   => $calendar->academic_year,
            'semester'        => $calendar->semester,
            'start_date'      => $calendar->start_date?->format('M d, Y'),
            'end_date'        => $calendar->end_date?->format('M d, Y'),
            'current_week'    => $currentWeek,
            'total_weeks'     => $totalWeeks,
            'current_date'    => $now->format('F j, Y'),
            'days_remaining'  => (int) $daysRemaining,
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

        $syllabusCounts = Syllabus::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $pendingApprovals = User::where('account_status', 'pending')->count();

        $upcomingEvents = $this->getUpcomingEvents();

        $recentSyllabi = Syllabus::query()
            ->with('course:id,course_code,course_title', 'preparer:id,name')
            ->orderByDesc('updated_at')
            ->take(8)
            ->get()
            ->map(function ($syllabus) {
                return [
                    'id'          => $syllabus->id,
                    'course_code' => $syllabus->course?->course_code,
                    'title'       => $syllabus->course?->course_title,
                    'status'      => $syllabus->status,
                    'status_label'=> $this->getStatusLabel($syllabus->status),
                    'updated_at'  => $syllabus->updated_at?->diffForHumans(),
                    'preparer'    => $syllabus->preparer?->name,
                ];
            })
            ->all();

        return [
            'pending_approvals'  => $pendingApprovals,
            'total_users'        => User::count(),
            'role_counts'        => [
                'faculty' => (int) ($roleCounts['faculty'] ?? 0),
                'chair'   => (int) ($roleCounts['chair']   ?? 0),
                'dean'    => (int) ($roleCounts['dean']    ?? 0),
                'admin'   => (int) ($roleCounts['admin']   ?? 0),
            ],
            'structure_counts'   => [
                'colleges'    => College::count(),
                'departments' => Department::count(),
                'programs'    => Program::count(),
                'courses'     => Course::where('status', 'active')->count(),
            ],
            'syllabus_counts'    => [
                'draft'        => (int) ($syllabusCounts['draft']        ?? 0),
                'under_review' => (int) ($syllabusCounts['under_review'] ?? 0),
                'for_revision' => (int) ($syllabusCounts['for_revision'] ?? 0),
                'approved'     => (int) ($syllabusCounts['approved']     ?? 0),
            ],
            'upcoming_events'    => $upcomingEvents,
            'recent_syllabi'     => $recentSyllabi,
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
                'courses_with_syllabus_count' => 0,
                'courses_without_ied' => [],
                'upcoming_events' => [],
                'recent_syllabi' => [],
            ];
        }

        $programIds = DB::table('program_departments')
            ->where('department_id', $department->id)
            ->pluck('program_id')
            ->all();
        $scopeStats = $this->buildScopeStats($programIds, (int) $department->id, null);

        // Get courses with syllabus count for the department
        $coursesWithSyllabusCount = Course::query()
            ->where('status', 'active')
            ->whereIn('program_id', $programIds)
            ->whereHas('syllabi')
            ->count();

        // Get total courses count for the department
        $totalCoursesCount = Course::query()
            ->where('status', 'active')
            ->whereIn('program_id', $programIds)
            ->count();

        // Get courses without IED mapping for the department
        $coursesWithoutIed = Course::query()
            ->where('status', 'active')
            ->whereIn('program_id', $programIds)
            ->whereDoesntHave('programOutcomes')
            ->with('program')
            ->get(['id', 'course_code', 'course_title', 'program_id'])
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'course_code' => $course->course_code ?? 'Unknown',
                    'title' => $course->course_title ?? 'Untitled',
                    'program' => $course->program?->name ?? 'Unknown',
                ];
            })
            ->all();

        $upcomingEvents = $this->getUpcomingEvents();

        // Get recent syllabus activity for the department
        $recentSyllabi = Syllabus::query()
            ->whereHas('course', function ($query) use ($programIds) {
                $query->where('status', 'active')->whereIn('program_id', $programIds);
            })
            ->with('course:id,course_code,course_title')
            ->orderByDesc('updated_at')
            ->take(5)
            ->get()
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
            'courses_with_syllabus_count' => $coursesWithSyllabusCount,
            'total_courses_count' => $totalCoursesCount,
            'courses_without_ied' => $coursesWithoutIed,
            'upcoming_events' => $upcomingEvents,
            'recent_syllabi' => $recentSyllabi,
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
                'has_draft'      => false,
                'latest_draft_id'=> null,
                'draft_syllabi_count' => 0,
                'under_review_count' => 0,
                'for_revision_count' => 0,
                'approved_count' => 0,
                'recent_syllabi' => [],
                'courses_without_ied' => [],
                'courses_with_syllabus_count' => 0,
                'upcoming_events' => [],
            ];
        }

        // Get programs from faculty's department assignments
        $departmentIds = $user->assignments()
            ->where('context', 'faculty')
            ->whereNotNull('department_id')
            ->pluck('department_id')
            ->unique()
            ->all();

        $programIds = DB::table('program_departments')
            ->whereIn('department_id', $departmentIds)
            ->pluck('program_id')
            ->unique()
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
                'stats'          => $this->emptyScopeOverview(null, $department->college_id ? (int) $department->college_id : null),
                'syllabus_stats' => $this->emptySyllabusStats(),
                'has_draft'      => false,
                'latest_draft_id'=> null,
                'draft_syllabi_count' => 0,
                'under_review_count' => 0,
                'for_revision_count' => 0,
                'approved_count' => 0,
                'recent_syllabi' => [],
                'courses_without_ied' => [],
                'courses_with_syllabus_count' => 0,
                'upcoming_events' => [],
            ];
        }

        $scopeStats = $this->buildScopeStats($programIds, null, $department->college_id ? (int) $department->college_id : null);

        // Get faculty-specific syllabus statistics and recent activity
        $facultySyllabi = Syllabus::where('prepared_by', $user->id)
            ->with('course:id,course_code,course_title')
            ->get(['id', 'status', 'updated_at', 'course_id']);

        $draftSyllabiCount = $facultySyllabi->where('status', 'draft')->count();
        $underReviewCount = $facultySyllabi->where('status', 'under_review')->count();
        $forRevisionCount = $facultySyllabi->where('status', 'for_revision')->count();
        $approvedCount = $facultySyllabi->where('status', 'approved')->count();

        // Get latest draft syllabus
        $latestDraft = $facultySyllabi->where('status', 'draft')
            ->sortByDesc('updated_at')
            ->first();

        // Get recent syllabus activity for faculty
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

        // Get courses with no IED mapping for faculty's courses
        $coursesWithoutIed = Course::query()
            ->where('status', 'active')
            ->whereDoesntHave('programOutcomes')
            ->whereHas('syllabi', function ($query) use ($user) {
                $query->where('prepared_by', $user->id);
            })
            ->with('program')
            ->get(['id', 'course_code', 'course_title', 'program_id'])
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'course_code' => $course->course_code ?? 'Unknown',
                    'title' => $course->course_title ?? 'Untitled',
                    'program' => $course->program?->name ?? 'Unknown',
                ];
            })
            ->all();

        // Get courses with syllabus count for faculty's department(s)
        $coursesWithSyllabusCount = Course::query()
            ->where('status', 'active')
            ->whereIn('program_id', $programIds)
            ->whereHas('syllabi')
            ->count();

        // Get total courses count for faculty's department(s)
        $totalCoursesCount = Course::query()
            ->where('status', 'active')
            ->whereIn('program_id', $programIds)
            ->count();

        $upcomingEvents = $this->getUpcomingEvents();

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
            'has_draft'      => $latestDraft !== null,
            'latest_draft_id'=> $latestDraft?->id,
            'draft_syllabi_count' => $draftSyllabiCount,
            'under_review_count' => $underReviewCount,
            'for_revision_count' => $forRevisionCount,
            'approved_count' => $approvedCount,
            'recent_syllabi' => $recentSyllabi,
            'courses_without_ied' => $coursesWithoutIed,
            'courses_with_syllabus_count' => $coursesWithSyllabusCount,
            'total_courses_count' => $totalCoursesCount,
            'upcoming_events' => $upcomingEvents,
        ];
    }

    /**
     * Fetch calendar events within a 30-day window centred on today.
     * Shared by faculty, chair, and dean dashboard methods.
     *
     * @return list<array<string, mixed>>
     */
    private function getUpcomingEvents(): array
    {
        $calendar = AcademicCalendar::active()->first();

        if (! $calendar) {
            return [];
        }

        return $calendar->events()
            ->whereBetween('date', [now()->subDays(30)->toDateString(), now()->addDays(30)->toDateString()])
            ->orderBy('date')
            ->get(['id', 'type', 'name', 'date'])
            ->map(function ($event) {
                $eventDate = $event->date ? \Carbon\Carbon::parse($event->date) : null;
                $isPast    = $eventDate && $eventDate->lt(now());

                return [
                    'id'        => $event->id,
                    'type'      => $event->type,
                    'name'      => $event->name,
                    'date'      => $eventDate?->format('M d, Y'),
                    'days_until'=> $eventDate ? (int) now()->diffInDays($eventDate, false) : null,
                    'is_past'   => $isPast,
                ];
            })
            ->all();
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
                'courses_with_syllabus_count' => 0,
                'courses_without_ied' => [],
                'upcoming_events' => [],
                'recent_syllabi' => [],
            ];
        }

        $programIds = DB::table('program_departments')
            ->whereIn('department_id', function($query) use ($college) {
                $query->select('id')
                    ->from('departments')
                    ->where('college_id', $college->id);
            })
            ->pluck('program_id')
            ->all();
        $scopeStats = $this->buildScopeStats($programIds, null, (int) $college->id);

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

        // Get courses with syllabus count for the college
        $coursesWithSyllabusCount = Course::query()
            ->where('status', 'active')
            ->whereIn('program_id', $programIds)
            ->whereHas('syllabi')
            ->count();

        // Get total courses count for the college
        $totalCoursesCount = Course::query()
            ->where('status', 'active')
            ->whereIn('program_id', $programIds)
            ->count();

        // Get courses without IED mapping for the college
        $coursesWithoutIed = Course::query()
            ->where('status', 'active')
            ->whereIn('program_id', $programIds)
            ->whereDoesntHave('programOutcomes')
            ->with('program')
            ->get(['id', 'course_code', 'course_title', 'program_id'])
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'course_code' => $course->course_code ?? 'Unknown',
                    'title' => $course->course_title ?? 'Untitled',
                    'program' => $course->program?->name ?? 'Unknown',
                ];
            })
            ->all();

        $upcomingEvents = $this->getUpcomingEvents();

        // Get recent syllabus activity for the college
        $recentSyllabi = Syllabus::query()
            ->whereHas('course', function ($query) use ($programIds) {
                $query->where('status', 'active')->whereIn('program_id', $programIds);
            })
            ->with('course:id,course_code,course_title')
            ->orderByDesc('updated_at')
            ->take(5)
            ->get()
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
            'college'        => [
                'id'   => $college->id,
                'name' => $college->name,
            ],
            'stats'          => $scopeStats['overview'],
            'departments'    => $departments,
            'syllabus_stats' => $scopeStats['syllabus'],
            'courses_with_syllabus_count' => $coursesWithSyllabusCount,
            'total_courses_count' => $totalCoursesCount,
            'courses_without_ied' => $coursesWithoutIed,
            'upcoming_events' => $upcomingEvents,
            'recent_syllabi' => $recentSyllabi,
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
                'syllabus' => [],
            ];
        }

        $programCount = count($programIds);

        $overview = [
            ['key' => 'programs', 'label' => 'Programs', 'value' => $programCount, 'icon' => 'bx-network-chart', 'color' => 'emerald'],
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

        return [
            'overview' => $overview,
            'syllabus' => [],
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

        return $overview;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function emptySyllabusStats(): array
    {
        return [];
    }
}
