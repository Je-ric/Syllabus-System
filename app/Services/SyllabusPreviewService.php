<?php

namespace App\Services;

use App\Models\CompleteSyllabus;
use App\Models\Syllabus;
use Carbon\Carbon;

/**
 * SyllabusPreviewService
 *
 * Centralises all data-building logic for every syllabus preview variant.
 * The SyllabusController delegates to this service instead of carrying the
 * logic in its own private method.
 *
 * Public API:
 *   buildCompleteData(Syllabus)  — full OBTL preview (all sections)
 *   buildAbridgedData(Syllabus)  — student-facing abridged version
 *
 * Both methods return an array that can be passed directly to view().
 */
class SyllabusPreviewService
{
    // ══════════════════════════════════════════════════════════════════════════
    // PUBLIC ENTRY POINTS
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Build data for the complete OBTL preview (complete.blade.php).
     * Extracted verbatim from SyllabusController::buildPreviewData() so the
     * controller can stay thin.
     */
    public function buildCompleteData(Syllabus $syllabus): array
    {
        $this->eagerLoad($syllabus);

        $shared  = $this->sharedData($syllabus);
        $weekly  = $this->buildWeeklyCoverageRows($syllabus, $shared['weeks']);
        $evalOut = $this->buildEvaluationRows($syllabus, $shared['weeks']);
        $refs    = $this->buildReferences($syllabus);

        return array_merge($shared['export'], [
            'weeklyCoverageRows' => $weekly,
            'allReferences'      => $refs['allReferences'],
            'onlineMaterialLinks'=> $refs['onlineMaterialLinks'],
            'evaluationRows'     => $evalOut['evaluationRows'],
            'evaluationTotals'   => $evalOut['evaluationTotals'],
        ]);
    }

    /**
     * Build data for the abridged preview (abridged.blade.php).
     *
     * Extends the complete data with:
     *   abridgedWeeklyRows  — portrait-only weekly coverage with co_code + co_no
     *   coPoLetterMap       — [ co_id => 'A, B, D, …' ]  (letter codes per CO)
     */
    public function buildAbridgedData(Syllabus $syllabus): array
    {
        $base = $this->buildCompleteData($syllabus);

        // ── CO → PO letter codes ──────────────────────────────────────────────
        // Each CourseOutcome maps to program outcomes through the weekly content
        // CO relationship. The PO letter codes (po_code values) come from the
        // program outcomes that the *course* addresses (coursePoIedMap keys).
        // We derive per-CO PO codes from the CO->outcome relationship if it
        // exists, otherwise fall back to showing the course-level PO codes.
        $coPoLetterMap = $this->buildCoPoLetterMap($syllabus);

        // ── Portrait-optimised weekly rows ────────────────────────────────────
        // Uses the LEC component (primary) — same as the sample abridged PDF.
        // Adds co_code and co_no to every row for the CO No. column.
        $weeks = $base['weeks'] ?? null;

        $abridgedWeeklyRows = [
            'LEC' => $this->buildAbridgedWeeklyRows($syllabus, $weeks, 'LEC'),
        ];

        if ($syllabus->course?->has_lec_lab) {
            $abridgedWeeklyRows['LAB'] = $this->buildAbridgedWeeklyRows($syllabus, $weeks, 'LAB');
        }

        return array_merge($base, [
            'coPoLetterMap'      => $coPoLetterMap,
            'abridgedWeeklyRows' => $abridgedWeeklyRows,
        ]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PRIVATE HELPERS — shared
    // ══════════════════════════════════════════════════════════════════════════

    private function eagerLoad(Syllabus $syllabus): void
    {
        $syllabus->load([
            'course.program.peos',
            'course.program.outcomes.peos',
            'course.program.departments.objectives',
            'course.program.departments.college.goals',
            'course.programOutcomes',
            'components',
            'courseOutcomes',
            'references',
            'onlineMaterials',
            'weeks.contents.courseOutcome',
            'weeks.contents.evaluation',
            'revisions',
            'reviewers.user',
            'dean',
            'deanConcurred',
        ]);
    }

    /**
     * Returns shared computed values + the weeks collection (not exported to view).
     * 'export' key contains everything safe to spread into view data.
     */
    private function sharedData(Syllabus $syllabus): array
    {
        $program              = $syllabus->course->program;
        $department           = $program->departments->first();
        $college              = $department?->college;

        $collegeName          = $college?->name ?? 'College';
        $departmentName       = $department?->name ?? 'Department';
        $collegeGoals         = $college?->goals?->sortBy('college_goals_code') ?? collect();
        $departmentObjectives = $department?->objectives?->sortBy('dept_obj_code') ?? collect();
        $peos                 = $program->peos?->sortBy('peo_code') ?? collect();
        $pos                  = $program->outcomes?->sortBy('po_code') ?? collect();
        $courseOutcomes       = $syllabus->courseOutcomes?->sortBy('co_code') ?? collect();
        $lecComponent         = $syllabus->components->firstWhere('type', 'LEC');
        $labComponent         = $syllabus->components->firstWhere('type', 'LAB');

        $coursePoIedMap = $syllabus->course?->programOutcomes
            ?->pluck('pivot.ied', 'id')
            ?->filter()
            ?->toArray()
            ?? [];

        $courseYearLevel = $syllabus->course?->year_level;
        $courseSemester  = $syllabus->course?->semester;

        $ordinal = static function (?int $n): ?string {
            if (! $n) {
                return null;
            }
            $suffix = 'th';
            if (! in_array($n % 100, [11, 12, 13], true)) {
                $suffix = match ($n % 10) {
                    1 => 'st', 2 => 'nd', 3 => 'rd', default => 'th',
                };
            }
            return $n . $suffix;
        };

        $courseLevel = match (true) {
            ! blank($courseYearLevel) && ! blank($courseSemester) =>
                $ordinal((int) $courseYearLevel) . ' Year, ' . $ordinal((int) $courseSemester) . ' Semester',
            ! blank($courseYearLevel) => $ordinal((int) $courseYearLevel) . ' Year',
            ! blank($courseSemester)  => $ordinal((int) $courseSemester) . ' Semester',
            default => 'N/A',
        };

        // Revision history — uses the service field names:
        // revision_no, revision_date, implementation_semester, highlights, contributors
        $syllabusRevisions = $syllabus->revisions
            ?->sortBy('revision_no')
            ?? collect();

        // Reviewers — each SyllabusReviewer has a user() relationship
        $syllabusReviewers = $syllabus->reviewers ?? collect();

        // approved_by → dean() relationship; concurred_by → chair() relationship.
        $approvedByUser  = $syllabus->dean  ?? null;
        $concurredByUser = $syllabus->deanConcurred ?? null;

        $weeks = $syllabus->weeks?->sortBy('week_no') ?? collect();

        $savedVersions = CompleteSyllabus::query()
            ->where('syllabus_id', $syllabus->id)
            ->orderByDesc('version')
            ->orderByDesc('created_at')
            ->get();

        return [
            'weeks'  => $weeks,
            'export' => compact(
                'syllabus',
                'program',
                'collegeName',
                'collegeGoals',
                'departmentName',
                'departmentObjectives',
                'peos',
                'pos',
                'courseOutcomes',
                'lecComponent',
                'labComponent',
                'coursePoIedMap',
                'courseLevel',
                'syllabusRevisions',
                'syllabusReviewers',
                'approvedByUser',
                'concurredByUser',
                'savedVersions',
            ),
        ];
    }

    // ── Weekly coverage rows (complete view) ──────────────────────────────────

    private function buildWeeklyCoverageRows(Syllabus $syllabus, $weeks): array
    {
        $examLabel = $this->examLabelFn();

        $weeklyCoverageRows = ['LEC' => [], 'LAB' => []];
        $assessmentCounters = ['LEC' => ['activity' => 0, 'quiz' => 0],
                               'LAB' => ['activity' => 0, 'quiz' => 0]];

        foreach ($weeks as $week) {
            $weekContents = $week->contents ?? collect();
            $weekExamTask = $weekContents->first(function ($c) {
                $t = trim((string) ($c?->assessment_task ?? ''));
                return $t !== '' && str_contains(strtolower($t), 'exam');
            });
            $weekNoClassTask = $weekContents->first(function ($c) {
                $t = strtolower(trim((string) ($c?->assessment_task ?? '')));
                return $t !== '' && (
                    str_contains($t, 'non-teaching week')
                    || str_contains($t, 'non teaching week')
                    || str_contains($t, 'no class')
                );
            });

            $isExam = $weekExamTask !== null
                || trim((string) ($week->exam_type ?? '')) !== ''
                || (bool) $week->is_exam_week;

            $isNoClass = $weekNoClassTask !== null;

            $resolvedExamLabel = $weekExamTask
                ? trim((string) $weekExamTask->assessment_task)
                : $examLabel($week->exam_type);
            $resolvedNoClassLabel = $weekNoClassTask
                ? trim((string) $weekNoClassTask->assessment_task)
                : 'Non-Teaching Week / No Class';
            $dateRange = $this->formatDateRange($week);

            foreach (['LEC', 'LAB'] as $type) {
                $content = $week->contents?->where('component_type', $type)?->first();

                if ($isNoClass || $isExam) {
                    $weeklyCoverageRows[$type][] = [
                        'week_label'        => 'Week ' . (int) $week->week_no,
                        'exam_label'        => $isNoClass ? $resolvedNoClassLabel : $resolvedExamLabel,
                        'week_no'           => (int) $week->week_no,
                        'is_exam'           => true,
                        'date_range'        => $dateRange,
                        'co_code'           => '',
                        'co_no'             => '',
                        'co_description'    => '',
                        'learning_outcomes' => '',
                        'topics'            => '',
                        'tla'               => '',
                        'assessment_task'   => '',
                    ];
                    continue;
                }

                $kind = $content?->evaluation?->kind;
                $task = trim((string) ($content?->assessment_task ?? ''));

                $assessmentDisplay = $task;
                if ($task !== '' && in_array($kind, ['activity', 'quiz'], true)) {
                    $assessmentCounters[$type][$kind]++;
                    $assessmentDisplay = ucfirst($kind) . ' ' . $assessmentCounters[$type][$kind] . ': ' . $task;
                }

                $coCode = (int) $week->week_no === 1
                    ? 'MVGO'
                    : ($content?->courseOutcome?->co_code ?? '');

                $weeklyCoverageRows[$type][] = [
                    'week_label'        => 'Week ' . (int) $week->week_no,
                    'exam_label'        => null,
                    'week_no'           => (int) $week->week_no,
                    'is_exam'           => false,
                    'date_range'        => $dateRange,
                    'co_code'           => $coCode,
                    'co_no'             => $content?->courseOutcome?->co_code
                                              ? ltrim($content->courseOutcome->co_code, 'COco')
                                              : ((int) $week->week_no === 1 ? '—' : ''),
                    'co_description'    => (int) $week->week_no === 1
                        ? 'MVGO — Mission, Vision, Goals & Objectives'
                        : ($content?->courseOutcome?->description ?? ''),
                    'learning_outcomes' => trim((string) ($content?->learning_outcomes ?? '')),
                    'topics'            => trim((string) ($content?->topics ?? '')),
                    'tla'               => trim((string) ($content?->tla ?? '')),
                    'assessment_task'   => $assessmentDisplay,
                ];
            }
        }

        return $weeklyCoverageRows;
    }

    // ── Abridged weekly rows ──────────────────────────────────────────────────

    /**
     * Portrait-only weekly rows for the abridged view.
     * Accepts a component type (`LEC` or `LAB`).
     * Columns: CO No., Wk No., Topics, Learning Activities, Assessment.
     * Exam rows span all columns.
     * Consecutive weeks with the same CO are merged into one grouped row.
     */
    private function buildAbridgedWeeklyRows(Syllabus $syllabus, $weeks, string $componentType = 'LEC'): array
    {
        $weeks     = $weeks ?? $syllabus->weeks?->sortBy('week_no') ?? collect();
        $examLabel = $this->examLabelFn();
        $rows      = [];

        // For displaying "Activity 1", "Quiz 1", etc. in the Assessment column
        $counters  = ['activity' => 0, 'quiz' => 0];

        // Pass 1: build one raw row per week (per component)
        $rawRows = [];
        $lastKnownCoNo = '';
        foreach ($weeks as $week) {
            $weekContents = $week->contents ?? collect();
            $weekExamTask = $weekContents->first(function ($c) {
                $t = trim((string) ($c?->assessment_task ?? ''));
                return $t !== '' && str_contains(strtolower($t), 'exam');
            });
            $weekNoClassTask = $weekContents->first(function ($c) {
                $t = strtolower(trim((string) ($c?->assessment_task ?? '')));
                return $t !== '' && (
                    str_contains($t, 'non-teaching week')
                    || str_contains($t, 'non teaching week')
                    || str_contains($t, 'no class')
                );
            });

            $isExam = $weekExamTask !== null
                || trim((string) ($week->exam_type ?? '')) !== ''
                || (bool) $week->is_exam_week;

            $isNoClass = $weekNoClassTask !== null;

            $resolvedExamLabel = $weekExamTask
                ? trim((string) $weekExamTask->assessment_task)
                : $examLabel($week->exam_type);
            $resolvedNoClassLabel = $weekNoClassTask
                ? trim((string) $weekNoClassTask->assessment_task)
                : 'Non-Teaching Week / No Class';
            $content = $week->contents?->where('component_type', $componentType)?->first();

            // CO is shared per week; if LAB has no CO selected, fall back to LEC's CO
            $coContent = $content;
            if ((int) $week->week_no !== 1 && ! $coContent?->course_outcome_id) {
                $lecContent = $week->contents?->where('component_type', 'LEC')?->first();
                if ($lecContent?->course_outcome_id) {
                    $coContent = $lecContent;
                }
            }

            if ($isNoClass || $isExam) {
                $coCode = $coContent?->courseOutcome?->co_code ?? '';
                $coNo   = '';
                if ((int) $week->week_no === 1) {
                    $coNo = 'MVGO';
                } elseif ($coCode !== '') {
                    $coNo = preg_replace('/^[A-Za-z]+/', '', $coCode); // strip 'CO' prefix
                }

                if ($coNo === '') {
                    $coNo = $lastKnownCoNo;
                }

                $rawRows[] = [
                    'is_exam'    => true,
                    'week_no'    => (int) $week->week_no,
                    'wk_label'   => (string) ((int) $week->week_no),
                    'exam_label' => $isNoClass ? $resolvedNoClassLabel : $resolvedExamLabel,
                    'co_no'      => $coNo,
                    'co_id'      => null,
                    'topics'     => '',
                    'tla'        => '',
                    'assessment' => '',
                ];
                continue;
            }

            // Assessment label with Activity/Quiz numbering when kind is set
            $taskRaw    = trim((string) ($content?->assessment_task ?? ''));
            $taskLabel  = $taskRaw;
            $isTaskExam = str_contains(strtolower($taskRaw), 'exam');
            $eval       = $content?->evaluation;

            if (! $isTaskExam && $taskRaw !== '' && in_array($eval?->kind, ['activity', 'quiz'], true)) {
                $k = $eval->kind;
                $counters[$k]++;
                $taskLabel = ucfirst($k) . ' ' . $counters[$k];
            }

            // Extract numeric part of CO code for the CO No. column
            $coCode = $coContent?->courseOutcome?->co_code ?? '';
            $coNo   = '';
            if ((int) $week->week_no === 1) {
                $coNo = 'MVGO';
            } elseif ($coCode !== '') {
                $coNo = preg_replace('/^[A-Za-z]+/', '', $coCode); // strip 'CO' prefix
            }

            if ($coNo !== '') {
                $lastKnownCoNo = $coNo;
            }

            $rawRows[] = [
                'is_exam'    => false,
                'week_no'    => (int) $week->week_no,
                'exam_label' => null,
                'co_no'      => $coNo,
                'co_id'      => $coContent?->course_outcome_id,
                'topics'     => trim((string) ($content?->topics ?? '')),
                'tla'        => trim((string) ($content?->tla ?? '')),
                'assessment' => $taskLabel,
            ];
        }

        // Pass 2: merge consecutive non-exam rows with same CO into one
        $i = 0;
        while ($i < count($rawRows)) {
            $row = $rawRows[$i];

            if ($row['is_exam']) {
                $rows[] = $row;
                $i++;
                continue;
            }

            // Look ahead: collect consecutive weeks with same co_id
            $group = [$row];
            $j     = $i + 1;
            while (
                $j < count($rawRows) &&
                ! $rawRows[$j]['is_exam'] &&
                $rawRows[$j]['co_id'] !== null &&
                $rawRows[$j]['co_id'] === $row['co_id']
            ) {
                $group[] = $rawRows[$j];
                $j++;
            }

            $startWk = $group[0]['week_no'];
            $endWk   = end($group)['week_no'];
            $wkLabel = $startWk === $endWk ? (string) $startWk : $startWk . ' – ' . $endWk;

            $topics     = collect($group)->pluck('topics')->filter()->implode("\n");
            $tla        = collect($group)->pluck('tla')->filter()->unique()->implode(', ');
            $assessment = collect($group)->pluck('assessment')->filter()->unique()->implode('; ');

            $rows[] = [
                'is_exam'    => false,
                'wk_label'   => $wkLabel,
                'co_no'      => $row['co_no'],
                'topics'     => $topics,
                'tla'        => $tla,
                'assessment' => $assessment,
            ];

            $i = $j;
        }

        return $rows;
    }


    // ── Evaluation rows ───────────────────────────────────────────────────────

    private function buildEvaluationRows(Syllabus $syllabus, $weeks): array
    {
        $evaluationRows   = [];
        $evaluationTotals = ['lec' => 0, 'lab' => 0];
        $evalCounters     = ['LEC' => ['activity' => 0, 'quiz' => 0],
                             'LAB' => ['activity' => 0, 'quiz' => 0]];

        foreach ($weeks as $week) {
            $lecContent = $week->contents?->where('component_type', 'LEC')?->first();
            $labContent = $week->contents?->where('component_type', 'LAB')?->first();

            $lecTaskRaw = trim((string) ($lecContent?->assessment_task ?? ''));
            $labTaskRaw = trim((string) ($labContent?->assessment_task ?? ''));

            if ($lecTaskRaw === '' && $labTaskRaw === '') {
                continue;
            }

            if ($lecTaskRaw === 'Non-Teaching Week' || $labTaskRaw === 'Non-Teaching Week') {
                continue;
            }

            $lecEval = $lecContent?->evaluation;
            $labEval = $labContent?->evaluation;

            $isExam = str_contains(strtolower($lecTaskRaw), 'exam')
                || str_contains(strtolower($labTaskRaw), 'exam');

            $lecTaskLabel = $lecTaskRaw;
            if (! $isExam && $lecTaskRaw !== '' && in_array($lecEval?->kind, ['activity', 'quiz'], true)) {
                $k = $lecEval->kind;
                $evalCounters['LEC'][$k]++;
                $lecTaskLabel = ucfirst($k) . ' ' . $evalCounters['LEC'][$k];
            }

            $labTaskLabel = $labTaskRaw;
            if (! $isExam && $labTaskRaw !== '' && in_array($labEval?->kind, ['activity', 'quiz'], true)) {
                $k = $labEval->kind;
                $evalCounters['LAB'][$k]++;
                $labTaskLabel = ucfirst($k) . ' ' . $evalCounters['LAB'][$k];
            }

            $lecWeight = $lecEval?->weight;
            $labWeight = $labEval?->weight;

            if ($lecWeight !== null) {
                $evaluationTotals['lec'] += (int) $lecWeight;
            }
            if ($syllabus->course?->has_lec_lab && $labWeight !== null) {
                $evaluationTotals['lab'] += (int) $labWeight;
            }

            $coLabel = $lecEval?->outcome_label
                ?? $labEval?->outcome_label
                ?? $lecContent?->courseOutcome?->co_code
                ?? $labContent?->courseOutcome?->co_code
                ?? '';

            $evaluationRows[] = [
                'co_label'   => $coLabel,
                'is_exam'    => $isExam,
                'lec_task'   => $lecTaskLabel,
                'lec_weight' => $lecWeight,
                'lab_task'   => $labTaskLabel,
                'lab_weight' => $labWeight,
            ];
        }

        return compact('evaluationRows', 'evaluationTotals');
    }

    // ── References ────────────────────────────────────────────────────────────

    private function buildReferences(Syllabus $syllabus): array
    {
        $lower = static fn (string $t): string => mb_strtolower($t);

        $allReferences = $syllabus->references
            ->pluck('reference_text')
            ->map(fn ($t) => trim((string) $t))
            ->filter()
            ->unique(fn ($t) => $lower($t))
            ->sortBy(fn ($t) => $lower($t))
            ->values();

        $onlineMaterialLinks = $syllabus->onlineMaterials
            ->pluck('url')
            ->map(fn ($t) => trim((string) $t))
            ->filter()
            ->unique(fn ($t) => $lower($t))
            ->sortBy(fn ($t) => $lower($t))
            ->values();

        return compact('allReferences', 'onlineMaterialLinks');
    }

    // ── CO → PO letter codes ──────────────────────────────────────────────────

    /**
     * Build a map of [ co_id => 'A, B, D, …' ] for the abridged CO table.
     *
     * Strategy: if CourseOutcome has a direct relationship to ProgramOutcomes,
     * use that. Otherwise fall back to the course-level PO map (same POs for
     * all COs) — which matches the sample syllabus where all COs share the same
     * letter codes.
     */
    private function buildCoPoLetterMap(Syllabus $syllabus): array
    {
        $courseOutcomes = $syllabus->courseOutcomes ?? collect();

        if ($courseOutcomes->isEmpty()) {
            return [];
        }

        $map = [];

        // Try per-CO PO relationship (requires course_outcome_po pivot table).
        // Catch QueryException so a missing table falls through to the safe fallback.
        $hasCoPo = method_exists($courseOutcomes->first(), 'programOutcomes');

        if ($hasCoPo) {
            try {
                foreach ($courseOutcomes as $co) {
                    $map[$co->id] = $co->programOutcomes
                        ->sortBy('po_code')
                        ->pluck('po_code')
                        ->implode(', ');
                }
                return $map;
            } catch (\Illuminate\Database\QueryException) {
                // Pivot table does not exist yet — fall through to course-level fallback.
                $map = [];
            }
        }

        // Fallback: course-level PO codes shared across all COs.
        // Matches the sample abridged syllabus where every CO lists the same POs.
        $coursePoCodes = collect($syllabus->course?->programOutcomes ?? collect())
            ->sortBy('po_code')
            ->pluck('po_code')
            ->implode(', ');

        foreach ($courseOutcomes as $co) {
            $map[$co->id] = $coursePoCodes;
        }

        return $map;
    }

    // ── Utilities ─────────────────────────────────────────────────────────────

    private function examLabelFn(): \Closure
    {
        return static function (?string $examType): string {
            $examType = trim((string) $examType);
            return match ($examType) {
                'first_term'  => '1st Term Exam',
                'second_term' => '2nd Term Exam',
                'final_term'  => 'Final Term Exam',
                'midterm'     => 'Midterm Exam',
                'final'       => 'Final Exam',
                default       => $examType !== ''
                    ? ucwords(str_replace('_', ' ', $examType)) . ' Exam'
                    : 'Exam',
            };
        };
    }

    private function formatDateRange($week): ?string
    {
        if (! $week->start_date || ! $week->end_date) {
            return null;
        }
        return Carbon::parse($week->start_date)->format('M d, Y')
            . ' - '
            . Carbon::parse($week->end_date)->format('M d, Y');
    }
}
