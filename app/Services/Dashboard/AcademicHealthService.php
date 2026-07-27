<?php

namespace App\Services\Dashboard;

use App\Models\AcademicCalendar;
use App\Models\Course;
use App\Models\Program;
use App\Models\ProgramOutcome;
use App\Models\Syllabus;

class AcademicHealthService
{
    /**
     * @param  array<int>  $programIds
     * @return array{warnings: list<array<string, mixed>>, mapping_issues: list<array<string, mixed>>}
     */
    public function summarizeForPrograms(array $programIds): array
    {
        if ($programIds === []) {
            return ['warnings' => [], 'mapping_issues' => []];
        }

        $programs = Program::query()
            ->whereIn('id', $programIds)
            ->withCount(['peos', 'outcomes'])
            ->orderBy('name')
            ->get(['id', 'name']);

        $warnings = [];
        $mappingIssues = [];

        $programsWithoutPeo = $programs->filter(fn (Program $program) => $program->peos_count === 0);
        if ($programsWithoutPeo->isNotEmpty()) {
            $warnings[] = $this->buildIssue(
                'missing_peo',
                'Programs without Program Educational Objectives (PEO)',
                $programsWithoutPeo->count(),
                $programsWithoutPeo->pluck('name')->take(5)->all(),
            );
        }

        $programsWithoutPo = $programs->filter(fn (Program $program) => $program->outcomes_count === 0);
        if ($programsWithoutPo->isNotEmpty()) {
            $warnings[] = $this->buildIssue(
                'missing_po',
                'Programs without Program Outcomes (PO)',
                $programsWithoutPo->count(),
                $programsWithoutPo->pluck('name')->take(5)->all(),
            );
        }

        if (! AcademicCalendar::active()->exists()) {
            $warnings[] = $this->buildIssue(
                'missing_active_calendar',
                'No active academic calendar',
                1,
                ['Set an active academic calendar for the current semester.'],
            );
        }

        $courseIds = Course::query()
            ->whereIn('program_id', $programIds)
            ->where('status', 'active')
            ->pluck('id')
            ->all();

        if ($courseIds !== []) {
            $programsWithPos = $programs->filter(fn (Program $program) => $program->outcomes_count > 0);

            if ($programsWithPos->isNotEmpty()) {
                $programIdsWithPos = $programsWithPos->pluck('id')->all();

                $coursesWithoutCurriculum = Course::query()
                    ->whereIn('program_id', $programIdsWithPos)
                    ->where('status', 'active')
                    ->whereDoesntHave('programOutcomes')
                    ->orderBy('course_code')
                    ->limit(5)
                    ->get(['course_code', 'course_title']);

                $coursesWithoutCurriculumCount = Course::query()
                    ->whereIn('program_id', $programIdsWithPos)
                    ->where('status', 'active')
                    ->whereDoesntHave('programOutcomes')
                    ->count();

                if ($coursesWithoutCurriculumCount > 0) {
                    $warnings[] = $this->buildIssue(
                        'missing_curriculum',
                        'Courses without curriculum mapping to POs',
                        $coursesWithoutCurriculumCount,
                        $coursesWithoutCurriculum
                            ->map(fn (Course $course) => "{$course->course_code} — {$course->course_title}")
                            ->all(),
                    );
                }
            }

            $syllabiWithoutCoCount = Syllabus::query()
                ->whereIn('course_id', $courseIds)
                ->whereDoesntHave('courseOutcomes')
                ->count();

            if ($syllabiWithoutCoCount > 0) {
                $sampleSyllabi = Syllabus::query()
                    ->whereIn('course_id', $courseIds)
                    ->whereDoesntHave('courseOutcomes')
                    ->with('course:id,course_code,course_title')
                    ->limit(5)
                    ->get();

                $warnings[] = $this->buildIssue(
                    'missing_co',
                    'Syllabi without Course Outcomes (CO)',
                    $syllabiWithoutCoCount,
                    $sampleSyllabi
                        ->map(fn (Syllabus $syllabus) => $syllabus->course
                            ? "{$syllabus->course->course_code} — {$syllabus->course->course_title}"
                            : "Syllabus #{$syllabus->id}")
                        ->all(),
                );
            }
        }

        $poWithoutPeoCount = ProgramOutcome::query()
            ->whereIn('program_id', $programIds)
            ->whereDoesntHave('peos')
            ->count();

        if ($poWithoutPeoCount > 0) {
            $samplePos = ProgramOutcome::query()
                ->whereIn('program_id', $programIds)
                ->whereDoesntHave('peos')
                ->with('program:id,name')
                ->orderBy('po_code')
                ->limit(5)
                ->get();

            $mappingIssues[] = $this->buildIssue(
                'po_without_peo',
                'Program Outcomes not mapped to any PEO',
                $poWithoutPeoCount,
                $samplePos
                    ->map(fn (ProgramOutcome $po) => ($po->po_code ?: 'PO') . ' — ' . ($po->program?->name ?? 'Unknown program'))
                    ->all(),
            );
        }

        $coursesWithCurriculumWithoutCoCount = Course::query()
            ->whereIn('program_id', $programIds)
            ->where('status', 'active')
            ->whereHas('programOutcomes')
            ->whereHas('syllabi')
            ->whereDoesntHave('syllabi', fn ($query) => $query->whereHas('courseOutcomes'))
            ->count();

        if ($coursesWithCurriculumWithoutCoCount > 0) {
            $sampleCourses = Course::query()
                ->whereIn('program_id', $programIds)
                ->where('status', 'active')
                ->whereHas('programOutcomes')
                ->whereHas('syllabi')
                ->whereDoesntHave('syllabi', fn ($query) => $query->whereHas('courseOutcomes'))
                ->orderBy('course_code')
                ->limit(5)
                ->get(['course_code', 'course_title']);

            $mappingIssues[] = $this->buildIssue(
                'curriculum_without_co',
                'Courses with curriculum mapping but no COs in syllabi',
                $coursesWithCurriculumWithoutCoCount,
                $sampleCourses
                    ->map(fn (Course $course) => "{$course->course_code} — {$course->course_title}")
                    ->all(),
            );
        }

        return [
            'warnings'        => $warnings,
            'mapping_issues'  => $mappingIssues,
        ];
    }

    /**
     * @return array<int>
     */
    public function programIdsForDepartment(int $departmentId): array
    {
        return Program::query()
            ->whereHas('departments', fn ($query) => $query->where('departments.id', $departmentId))
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * @return array<int>
     */
    public function programIdsForCollege(int $collegeId): array
    {
        return Program::query()
            ->whereHas('departments', fn ($query) => $query->where('college_id', $collegeId))
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * @param  list<string>  $samples
     * @return array<string, mixed>
     */
    private function buildIssue(string $type, string $label, int $count, array $samples): array
    {
        return [
            'type'    => $type,
            'label'   => $label,
            'count'   => $count,
            'samples' => $samples,
        ];
    }
}
