<?php

namespace App\Services\Syllabus;

use App\Models\CourseComponent;
use App\Models\Syllabus;
use App\Models\SyllabusEvaluationItem;
use App\Models\SyllabusWeek;
use App\Models\WeekContent;

/**
 * CourseEvaluationService
 *
 * Owns all read/write logic for the Course Evaluation wizard step so that
 * CourseEvaluationStep (Livewire) stays thin.
 *
 * Public API:
 *   loadRows(int $syllabusId): array          — rows + inputs + meta for the blade
 *   persist(int $syllabusId, array $rows, array $inputs): void
 *
 * The returned array from loadRows() has shape:
 * [
 *   'courseHasLab'         => bool,
 *   'lecPerformanceStd'    => string|null,   // e.g. '67%'
 *   'labPerformanceStd'    => string|null,   // e.g. '33%'
 *   'rows'                 => [...],
 *   'inputs'               => [...],
 * ]
 *
 * ── Row shape ────────────────────────────────────────────────────────────────
 * [
 *   'week_no'    => int,
 *   'is_exam'    => bool,
 *   'is_mvgo'    => bool,           // true only for week 1
 *   'term_label' => string|null,    // '1st Term' / '2nd Term' / 'Final Term'
 *   'co_coverage'=> string,         // auto-resolved CO code for exam rows
 *   'lec' => [
 *     'week_content_id' => int,
 *     'co_code'         => string|null,
 *     'task_label'      => string,
 *   ] | null,
 *   'lab' => [...same...] | null,
 * ]
 *
 * ── Exam CO resolution ───────────────────────────────────────────────────────
 * For exam rows, the CO is NOT user input — it is auto-resolved from the week
 * immediately before the exam.  If that week also has no CO (e.g. it is itself
 * MVGO or an earlier exam) we walk backwards until we find a real CO or give up.
 * The resolved code is stored in 'co_coverage' on the row and is read-only in
 * the blade.
 */
class CourseEvaluationService
{
    // ══════════════════════════════════════════════════════════════════════════
    // PUBLIC
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Load everything the blade needs.
     */
    public function loadRows(int $syllabusId): array
    {
        $syllabus = Syllabus::with('course')->find($syllabusId);
        if (! $syllabus) {
            return $this->emptyPayload();
        }

        $courseHasLab = (bool) $syllabus->course?->has_lec_lab;

        // ── Performance standards from the saved component records ────────────
        $lecComp = CourseComponent::where('syllabus_id', $syllabusId)
            ->where('type', 'LEC')
            ->first();
        $labComp = $courseHasLab
            ? CourseComponent::where('syllabus_id', $syllabusId)->where('type', 'LAB')->first()
            : null;

        $lecPerformanceStd = $lecComp?->performance_standard;
        $labPerformanceStd = $labComp?->performance_standard;

        // ── Weeks ─────────────────────────────────────────────────────────────
        $weeks = SyllabusWeek::where('syllabus_id', $syllabusId)
            ->orderBy('week_no')
            ->get();

        if ($weeks->isEmpty()) {
            return $this->emptyPayload($courseHasLab, $lecPerformanceStd, $labPerformanceStd);
        }

        $weekIds = $weeks->pluck('id')->all();

        $allContents = WeekContent::whereIn('syllabus_week_id', $weekIds)
            ->with('courseOutcome')
            ->get();

        // Index: $contentMap[week_id][component_type] = WeekContent
        $contentMap = [];
        foreach ($allContents as $content) {
            $contentMap[$content->syllabus_week_id][$content->component_type] = $content;
        }

        $evalMap = SyllabusEvaluationItem::where('syllabus_id', $syllabusId)
            ->get()
            ->keyBy('week_content_id');

        // ── Pass 1: build a quick lookup of week_no → LEC CO code for exam
        //            CO auto-resolution.  Only non-exam, non-MVGO weeks fill this.
        $weekNoCo = [];   // week_no => co_code (string|null)
        foreach ($weeks as $w) {
            $lc = $contentMap[$w->id]['LEC'] ?? null;
            if ($lc && ! $w->is_exam_week && (int) $w->week_no !== 1) {
                $weekNoCo[(int) $w->week_no] = $lc->courseOutcome?->co_code;
            }
        }

        // ── Pass 2: build rows ────────────────────────────────────────────────
        $rows            = [];
        $inputs          = [];
        $examTermLabels  = ['1st Term', '2nd Term', 'Final Term'];
        $examCount       = 0;

        foreach ($weeks as $week) {
            $lecContent = $contentMap[$week->id]['LEC'] ?? null;
            $labContent = $contentMap[$week->id]['LAB'] ?? null;

            $lecTask = trim((string) ($lecContent?->assessment_task ?? ''));
            $labTask = trim((string) ($labContent?->assessment_task ?? ''));

            if ($lecTask === '' && $labTask === '') {
                continue;
            }
            if ($lecTask === 'Non-Teaching Week' || $labTask === 'Non-Teaching Week') {
                continue;
            }

            $isExam = str_contains(strtolower($lecTask), 'exam')
                   || str_contains(strtolower($labTask), 'exam');

            $termLabel = null;
            if ($isExam) {
                $termLabel = $examTermLabels[min($examCount, 2)];
                $examCount++;
            }

            $isMvgo = ((int) $week->week_no === 1);

            // ── Auto-resolve CO coverage for exam rows ────────────────────────
            // Walk backwards from (this exam week − 1) to find the last assigned CO.
            $coCoverage = '';
            if ($isExam) {
                $coCoverage = $this->resolveExamCo((int) $week->week_no, $weekNoCo);
            }

            // ── LEC side ──────────────────────────────────────────────────────
            $lecRow = null;
            if ($lecTask !== '') {
                $lecEval = $evalMap->get($lecContent->id);
                $lecRow  = [
                    'week_content_id' => $lecContent->id,
                    'co_code'         => $lecContent->courseOutcome?->co_code,
                    'task_label'      => $lecTask,
                ];
                $inputs[$lecContent->id] = [
                    'weight'        => $lecEval?->weight !== null ? (string) $lecEval->weight : '',
                    'outcome_label' => $isMvgo ? 'MVGO' : ($lecEval?->outcome_label ?? ''),
                    // kind: 'activity', 'quiz', or 'exam' — user-selectable for regular rows
                    'kind'          => $isMvgo ? 'activity' : ($lecEval?->kind ?? 'activity'),
                ];
            }

            // ── LAB side ──────────────────────────────────────────────────────
            $labRow = null;
            if ($courseHasLab && $labTask !== '') {
                $labEval = $evalMap->get($labContent->id);
                $labRow  = [
                    'week_content_id' => $labContent->id,
                    'co_code'         => $labContent->courseOutcome?->co_code,
                    'task_label'      => $labTask,
                ];
                $inputs[$labContent->id] = [
                    'weight'        => $labEval?->weight !== null ? (string) $labEval->weight : '',
                    'outcome_label' => $isMvgo ? 'MVGO' : ($labEval?->outcome_label ?? ''),
                    'kind'          => $isMvgo ? 'activity' : ($labEval?->kind ?? 'activity'),
                ];
            }

            if ($lecRow === null && $labRow === null) {
                continue;
            }

            $rows[] = [
                'week_no'     => (int) $week->week_no,
                'is_exam'     => $isExam,
                'is_mvgo'     => $isMvgo,
                'term_label'  => $termLabel,
                'co_coverage' => $coCoverage,   // read-only auto-resolved CO for exam rows
                'lec'         => $lecRow,
                'lab'         => $labRow,
            ];
        }

        // ── Compute totals in PHP so the blade just displays, never calculates ──
        $lecTotal = 0;
        $labTotal = 0;
        foreach ($rows as $row) {
            if (isset($row['lec']['week_content_id'])) {
                $w = (int) ($inputs[$row['lec']['week_content_id']]['weight'] ?? 0);
                $lecTotal += $w;
            }
            if ($courseHasLab && isset($row['lab']['week_content_id'])) {
                $w = (int) ($inputs[$row['lab']['week_content_id']]['weight'] ?? 0);
                $labTotal += $w;
            }
        }

        // ── Weight total targets (structural, not configurable) ─────────────────
        // LEC+LAB courses: LEC must sum to 67, LAB must sum to 33.
        // LEC-only courses: LEC must sum to 100.
        // These are fixed academic standards — NOT the performance/passing standard.
        $lecStdNum = $courseHasLab ? 67 : 100;
        $labStdNum = 33;

        // ── Passing mark (from performance_standard on the component) ─────────
        // This is the minimum score a student must achieve (e.g. 60, 75).
        // Raw score is always out of 100; this is a threshold, not a weight target.
        $parseDecimal = static fn (mixed $v, int $fb): int =>
            is_numeric(str_replace('%', '', (string) ($v ?? '')))
                ? (int) round((float) str_replace('%', '', (string) $v))
                : $fb;

        $lecPassingMark = $parseDecimal($lecPerformanceStd, 60);
        $labPassingMark = $parseDecimal($labPerformanceStd, 60);

        return [
            'courseHasLab'      => $courseHasLab,
            'lecPerformanceStd' => $lecPerformanceStd,
            'labPerformanceStd' => $labPerformanceStd,
            'lecStdNum'         => $lecStdNum,     // weight total target (67 or 100)
            'labStdNum'         => $labStdNum,     // weight total target (33)
            'lecTotal'          => $lecTotal,      // running sum of saved weights
            'labTotal'          => $labTotal,
            'lecPassingMark'    => $lecPassingMark, // passing threshold (e.g. 60, 75)
            'labPassingMark'    => $labPassingMark,
            'rows'              => $rows,
            'inputs'            => $inputs,
        ];
    }

    /**
     * Persist evaluation items to the DB.
     *
     * @param  array  $rows    The same rows array returned by loadRows()
     * @param  array  $inputs  The current wire:model inputs (keyed by week_content_id)
     */
    public function persist(int $syllabusId, array $rows, array $inputs, bool $courseHasLab): void
    {
        $syllabus = Syllabus::with('course')->find($syllabusId);
        if (! $syllabus) {
            return;
        }
        $courseId = (int) $syllabus->course?->id;

        foreach ($rows as $row) {
            if (isset($row['lec']['week_content_id'])) {
                $this->saveOneItem(
                    syllabusId:   $syllabusId,
                    courseId:     $courseId,
                    weekContentId:(int) $row['lec']['week_content_id'],
                    isExam:       $row['is_exam'],
                    isMvgo:       $row['is_mvgo'],
                    termLabel:    $row['term_label'],
                    coCoverage:   $row['co_coverage'] ?? '',
                    inputs:       $inputs,
                );
            }

            if ($courseHasLab && isset($row['lab']['week_content_id'])) {
                $this->saveOneItem(
                    syllabusId:   $syllabusId,
                    courseId:     $courseId,
                    weekContentId:(int) $row['lab']['week_content_id'],
                    isExam:       $row['is_exam'],
                    isMvgo:       $row['is_mvgo'],
                    termLabel:    $row['term_label'],
                    coCoverage:   $row['co_coverage'] ?? '',
                    inputs:       $inputs,
                );
            }
        }
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PRIVATE
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Walk backwards from ($examWeekNo - 1) to find the last non-null CO code.
     * Returns the CO code string or '' when nothing is found.
     *
     * Example:
     *   Weeks: 1(MVGO) 2(CO1) 3(CO1) 4(CO2) 5(EXAM)
     *   → resolveExamCo(5, map) returns 'CO2'  (week 4 was the last assigned CO)
     */
    private function resolveExamCo(int $examWeekNo, array $weekNoCo): string
    {
        for ($w = $examWeekNo - 1; $w >= 1; $w--) {
            if (isset($weekNoCo[$w]) && $weekNoCo[$w] !== null && $weekNoCo[$w] !== '') {
                return $weekNoCo[$w];
            }
        }
        return '';
    }

    private function saveOneItem(
        int $syllabusId,
        int $courseId,
        int $weekContentId,
        bool $isExam,
        bool $isMvgo,
        ?string $termLabel,
        string $coCoverage,
        array $inputs,
    ): void {
        $data = $inputs[$weekContentId] ?? [];

        $weightRaw = trim((string) ($data['weight'] ?? ''));
        $weight    = $weightRaw !== '' ? (int) $weightRaw : null;

        // MVGO → always 'MVGO'; exam → auto-resolved CO code (read-only in blade)
        if ($isMvgo) {
            $outcomeLabel = 'MVGO';
        } elseif ($isExam && $coCoverage !== '') {
            $outcomeLabel = $coCoverage;
        } else {
            $outcomeLabel = trim((string) ($data['outcome_label'] ?? ''));
            $outcomeLabel = $outcomeLabel !== '' ? $outcomeLabel : null;
        }

        // kind: exam rows are always 'exam'; regular rows respect user selection
        $kind = $isExam
            ? 'exam'
            : (in_array($data['kind'] ?? '', ['activity', 'quiz']) ? $data['kind'] : 'activity');
        $examType = $isExam
            ? match ($termLabel) {
                '1st Term'   => 'first_term',
                '2nd Term'   => 'second_term',
                'Final Term' => 'final_term',
                default      => null,
            }
            : null;

        SyllabusEvaluationItem::updateOrCreate(
            ['week_content_id' => $weekContentId],
            [
                'syllabus_id'   => $syllabusId,
                'course_id'     => $courseId,
                'outcome_label' => $outcomeLabel,
                'kind'          => $kind,
                'exam_type'     => $examType,
                'weight'        => $weight,
            ]
        );
    }

    private function emptyPayload(
        bool $courseHasLab = false,
        ?string $lecPerformanceStd = null,
        ?string $labPerformanceStd = null,
    ): array {
        return [
            'courseHasLab'      => $courseHasLab,
            'lecPerformanceStd' => $lecPerformanceStd,
            'labPerformanceStd' => $labPerformanceStd,
            'rows'              => [],
            'inputs'            => [],
        ];
    }
}