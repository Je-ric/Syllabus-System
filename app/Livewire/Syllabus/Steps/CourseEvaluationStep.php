<?php

namespace App\Livewire\Syllabus\Steps;

use App\Models\Syllabus;
use App\Models\SyllabusEvaluationItem;
use App\Models\SyllabusWeek;
use App\Models\WeekContent;
use Livewire\Attributes\On;
use Livewire\Component;

class CourseEvaluationStep extends Component
{
    // ── Public state ──────────────────────────────────────────────────────────

    public int  $syllabusId;

    /** Whether this course has both LEC and LAB (determines if LAB columns show). */
    public bool $courseHasLab = false;

    /**
     * The rows to display in the evaluation table.
     *
     * Each entry represents ONE week that has either an exam or an assessment task.
     * The row contains both LEC and LAB data side by side.
     *
     * Example structure:
     * [
     *   [
     *     'week_no'        => 4,
     *     'is_exam'        => true,
     *     'term_label'     => '1st Term',        // '1st Term', '2nd Term', 'Final Term'
     *     'lec' => [
     *       'week_content_id' => 12,
     *       'co_code'         => 'CO1',           // null if no CO mapped
     *       'task_label'      => '1st Term Exam', // auto-generated label
     *     ],
     *     'lab' => [                              // null if no LAB component
     *       'week_content_id' => 13,
     *       'co_code'         => 'CO1',
     *       'task_label'      => '1st Term Practical Exam',
     *     ],
     *   ],
     *   ...
     * ]
     */
    public array $rows = [];

    /**
     * The weight inputs, keyed by week_content_id.
     *
     * wire:model binds directly to these in the blade.
     *
     * Example: [ 12 => '10', 13 => '10', 7 => '4', 8 => '4' ]
     *
     * We also store outcome_label here (for rows with no CO, like MVGO tasks).
     *
     * Example: [ 7 => ['weight' => '4', 'outcome_label' => 'MVGO', 'kind' => 'activity'] ]
     */
    public array $inputs = [];

    // ── Mount ─────────────────────────────────────────────────────────────────

    public function mount(int $syllabusId): void
    {
        $this->syllabusId = $syllabusId;
        $this->loadData();
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.syllabus.steps.course-evaluation');
    }

    // ── Event listeners ───────────────────────────────────────────────────────

    /** Fired by the wizard when the user navigates TO this step. */
    #[On('syllabus-step-changed')]
    public function onStepChanged(string $step): void
    {
        if ($step === 'course_evaluation') {
            $this->loadData();
        }
    }

    /** Fired by the wizard just before navigating AWAY from this step. */
    #[On('syllabus-save-step')]
    public function onSaveRequested(string $step): void
    {
        if ($step !== 'course_evaluation') {
            return;
        }
        $this->save();
        $this->dispatch('syllabus-step-saved', step: 'course_evaluation');
    }

    // ── Public actions ────────────────────────────────────────────────────────

    /** Manual save button. */
    public function save(): void
    {
        $this->persistEvaluation();
        $this->dispatch('lw-toast', type: 'success', message: 'Course Evaluation saved.');
        $this->dispatch('syllabus-step-saved', step: 'course_evaluation');
    }

    // ── Private: load data ────────────────────────────────────────────────────

    /**
     * Build the $rows and $inputs arrays from the database.
     *
     * Strategy:
     * 1. Load all SyllabusWeeks for this syllabus, ordered by week_no.
     * 2. For each week, load the LEC WeekContent row (and LAB if applicable).
     * 3. Keep only weeks that have something worth evaluating:
     *    - Exam weeks (assessment_task contains "Exam", auto-written by WeeklyCoverageStep)
     *    - OR weeks where the faculty entered an assessment task in Weekly Coverage
     * 4. For exam weeks, assign the term label (1st / 2nd / Final) by counting them.
     * 5. Build the $inputs array pre-filled from any previously saved evaluation data.
     */
    private function loadData(): void
    {
        // Load the syllabus to check if this course has LAB
        $syllabus = Syllabus::with('course')->find($this->syllabusId);
        if (! $syllabus) {
            return;
        }

        $this->courseHasLab = (bool) $syllabus->course?->has_lec_lab;

        // Load all weeks for this syllabus
        $weeks = SyllabusWeek::where('syllabus_id', $this->syllabusId)
            ->orderBy('week_no')
            ->get();

        if ($weeks->isEmpty()) {
            $this->rows   = [];
            $this->inputs = [];
            return;
        }

        // Load ALL WeekContent rows for these weeks in one query
        $weekIds = $weeks->pluck('id')->all();

        $allContents = WeekContent::whereIn('syllabus_week_id', $weekIds)
            ->with('courseOutcome') // load the CO so we can show co_code
            ->get();

        // Group contents by [week_id][component_type] for easy lookup
        // e.g. $contentMap[5]['LEC'] = WeekContent model
        $contentMap = [];
        foreach ($allContents as $content) {
            $contentMap[$content->syllabus_week_id][$content->component_type] = $content;
        }

        // Load any previously saved evaluation items for pre-filling weights
        $allEvalItems = SyllabusEvaluationItem::where('syllabus_id', $this->syllabusId)->get();

        // Index evaluation items by week_content_id for quick lookup
        $evalMap = $allEvalItems->keyBy('week_content_id');

        // Build the rows
        $rows   = [];
        $inputs = [];

        // Count exam rows as we encounter them so we can label them 1st, 2nd, Final
        $examTermLabels = ['1st Term', '2nd Term', 'Final Term'];
        $examCount      = 0;

        foreach ($weeks as $week) {
            $lecContent = $contentMap[$week->id]['LEC'] ?? null;
            $labContent = $contentMap[$week->id]['LAB'] ?? null;

            // Determine if this week is an exam week by checking the LEC task label
            // (WeeklyCoverageStep auto-writes "1st Term Exam" etc. into assessment_task)
            $isExam = $lecContent && str_contains(strtolower($lecContent->assessment_task ?? ''), 'exam');

            // Also check LAB in case only LAB has an assessment task
            if (! $isExam && $labContent) {
                $isExam = str_contains(strtolower($labContent->assessment_task ?? ''), 'exam');
            }

            // Check if this week has any assessment task at all
            $lecHasTask = $lecContent && trim($lecContent->assessment_task ?? '') !== '';
            $labHasTask = $labContent && trim($labContent->assessment_task ?? '') !== '';

            // Skip weeks with nothing to evaluate
            if (! $lecHasTask && ! $labHasTask) {
                continue;
            }

            // Assign term label for exam weeks
            $termLabel = null;
            if ($isExam) {
                $termLabel = $examTermLabels[min($examCount, 2)];
                $examCount++;
            }

            // Build the LEC side of the row
            $lecRow = null;
            if ($lecContent) {
                $lecEval = $evalMap->get($lecContent->id);

                $lecRow = [
                    'week_content_id' => $lecContent->id,
                    'co_code'         => $lecContent->courseOutcome?->co_code,
                    'task_label'      => $lecContent->assessment_task ?? '',
                ];

                // Pre-fill the inputs from saved data, or leave blank
                $inputs[$lecContent->id] = [
                    'weight'        => $lecEval?->weight !== null ? (string) $lecEval->weight : '',
                    'outcome_label' => $lecEval?->outcome_label ?? '',
                    'kind'          => $lecEval?->kind ?? 'activity',
                ];
            }

            // Build the LAB side of the row (only if course has LAB)
            $labRow = null;
            if ($this->courseHasLab && $labContent) {
                $labEval = $evalMap->get($labContent->id);

                $labRow = [
                    'week_content_id' => $labContent->id,
                    'co_code'         => $labContent->courseOutcome?->co_code,
                    'task_label'      => $labContent->assessment_task ?? '',
                ];

                $inputs[$labContent->id] = [
                    'weight'        => $labEval?->weight !== null ? (string) $labEval->weight : '',
                    'outcome_label' => $labEval?->outcome_label ?? '',
                    'kind'          => $labEval?->kind ?? 'activity',
                ];
            }

            $rows[] = [
                'week_no'    => $week->week_no,
                'is_exam'    => $isExam,
                'term_label' => $termLabel,
                'lec'        => $lecRow,
                'lab'        => $labRow,
            ];
        }

        $this->rows   = $rows;
        $this->inputs = $inputs;
    }

    // ── Private: save to DB ───────────────────────────────────────────────────

    /**
     * Write the $inputs data to the syllabus_evaluation_items table.
     *
     * For each row, we upsert one SyllabusEvaluationItem per WeekContent
     * (LEC and LAB are separate rows).
     */
    private function persistEvaluation(): void
    {
        // Load the course_id once (needed for the evaluation item)
        $syllabus = Syllabus::with('course')->find($this->syllabusId);
        if (! $syllabus) {
            return;
        }
        $courseId = (int) $syllabus->course?->id;

        foreach ($this->rows as $row) {
            // Save LEC evaluation item
            if (isset($row['lec']['week_content_id'])) {
                $this->saveOneItem(
                    weekContentId: (int) $row['lec']['week_content_id'],
                    syllabusId:    $this->syllabusId,
                    courseId:      $courseId,
                    isExam:        $row['is_exam'],
                    termLabel:     $row['term_label'],
                    component:     'LEC',
                );
            }

            // Save LAB evaluation item
            if ($this->courseHasLab && isset($row['lab']['week_content_id'])) {
                $this->saveOneItem(
                    weekContentId: (int) $row['lab']['week_content_id'],
                    syllabusId:    $this->syllabusId,
                    courseId:      $courseId,
                    isExam:        $row['is_exam'],
                    termLabel:     $row['term_label'],
                    component:     'LAB',
                );
            }
        }
    }

    /**
     * Save or update a single SyllabusEvaluationItem.
     *
     * @param int    $weekContentId  The WeekContent row this item belongs to
     * @param int    $syllabusId
     * @param int    $courseId
     * @param bool   $isExam         True if this is an exam week
     * @param string|null $termLabel '1st Term', '2nd Term', 'Final Term', or null
     * @param string $component      'LEC' or 'LAB'
     */
    private function saveOneItem(
        int $weekContentId,
        int $syllabusId,
        int $courseId,
        bool $isExam,
        ?string $termLabel,
        string $component,
    ): void {
        $inputData = $this->inputs[$weekContentId] ?? [];

        // Convert weight to integer, or null if blank
        $weightRaw = trim((string) ($inputData['weight'] ?? ''));
        $weight    = $weightRaw !== '' ? (int) $weightRaw : null;

        // Outcome label (for tasks not mapped to a specific CO, e.g. "MVGO")
        $outcomeLabel = trim((string) ($inputData['outcome_label'] ?? ''));
        $outcomeLabel = $outcomeLabel !== '' ? $outcomeLabel : null;

        // Determine kind and exam_type
        if ($isExam) {
            $kind     = 'exam';
            $examType = match ($termLabel) {
                '1st Term'   => 'first_term',
                '2nd Term'   => 'second_term',
                'Final Term' => 'final_term',
                default      => null,
            };
        } else {
            $kindRaw  = trim((string) ($inputData['kind'] ?? 'activity'));
            $kind     = in_array($kindRaw, ['activity', 'quiz'], true) ? $kindRaw : 'activity';
            $examType = null;
        }

        SyllabusEvaluationItem::updateOrCreate(
            // Find by this unique key
            ['week_content_id' => $weekContentId],
            // Update these fields
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
}
