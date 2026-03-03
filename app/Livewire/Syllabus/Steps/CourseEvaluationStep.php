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

    // True when the course has both LEC and LAB components.
    // Controls whether the LAB column group is rendered in the table.
    public bool $courseHasLab = false;

    // Rows shown in the evaluation table.
    // Each entry is one week that has at least one side (LEC or LAB) with a real task.
    //
    // Shape:
    // [
    //   'week_no'    => 4,
    //   'is_exam'    => true,
    //   'term_label' => '1st Term',   // null for regular activity rows
    //   'lec' => [                    // NULL if LEC has no assessment task this week
    //     'week_content_id' => 12,
    //     'co_code'         => 'CO1', // null if no CO is mapped on this WeekContent
    //     'task_label'      => '1st Term Exam',
    //   ],
    //   'lab' => [                    // NULL if LAB has no task, or course has no LAB
    //     'week_content_id' => 13,
    //     'co_code'         => 'CO1',
    //     'task_label'      => '1st Term Practical Exam',
    //   ],
    // ]
    //
    // When 'lec' is null the blade renders the LEC columns as greyed-out / disabled.
    // When 'lab' is null the blade renders the LAB columns as greyed-out / disabled.
    // This handles the case where LEC and LAB have different numbers of tasks.
    public array $rows = [];

    // Weight (and optional outcome label) inputs keyed by week_content_id.
    // wire:model.lazy in the blade binds directly to this array.
    //
    // Shape: [ week_content_id => ['weight' => '10', 'outcome_label' => 'MVGO'] ]
    //
    // Only rows where 'lec' / 'lab' is non-null get an entry here.
    // If a side is null (no task) there is no input entry, so the blade cannot
    // accidentally bind a wire:model to a non-existent slot.
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

    // Wizard dispatches this when the user navigates TO this step.
    #[On('syllabus-step-changed')]
    public function onStepChanged(string $step): void
    {
        if ($step === 'course_evaluation') {
            $this->loadData();
        }
    }

    // Wizard dispatches this just before navigating AWAY from this step.
    #[On('syllabus-save-step')]
    public function onSaveRequested(string $step): void
    {
        if ($step !== 'course_evaluation') {
            return;
        }
        $this->persistEvaluation();
        $this->dispatch('syllabus-step-saved', step: 'course_evaluation');
    }

    // ── Public actions ────────────────────────────────────────────────────────

    // Manual "Save Evaluation" button handler.
    public function save(): void
    {
        $this->persistEvaluation();
        $this->dispatch('lw-toast', type: 'success', message: 'Course Evaluation saved.');
        $this->dispatch('syllabus-step-saved', step: 'course_evaluation');
    }

    // ── Private: load data ────────────────────────────────────────────────────

    // Build $rows and $inputs from the database.
    //
    // Row inclusion rules (all logic lives here, blade just renders):
    //   INCLUDE  — LEC side is included when LEC WeekContent.assessment_task is non-empty.
    //   INCLUDE  — LAB side is included when LAB WeekContent.assessment_task is non-empty
    //              AND the course has a LAB component.
    //   SKIP row — when BOTH sides have an empty assessment_task.
    //   SKIP row — when the task is exactly "Non-Teaching Week" (written by WeeklyCoverageStep
    //              for non_teaching calendar events; not a real assessable task).
    //   SKIP row — 'break' calendar event weeks that the faculty left blank naturally
    //              fall through the "both sides empty" rule above and are never shown.
    //
    // When one side has a task and the other does not:
    //   The side without a task is represented as null ('lec' => null or 'lab' => null).
    //   The blade reads this and renders greyed-out disabled placeholder columns for
    //   that side, ensuring the table stays aligned regardless of asymmetric tasks.
    private function loadData(): void
    {
        $syllabus = Syllabus::with('course')->find($this->syllabusId);
        if (! $syllabus) {
            return;
        }

        $this->courseHasLab = (bool) $syllabus->course?->has_lec_lab;

        $weeks = SyllabusWeek::where('syllabus_id', $this->syllabusId)
            ->orderBy('week_no')
            ->get();

        if ($weeks->isEmpty()) {
            $this->rows   = [];
            $this->inputs = [];
            return;
        }

        $weekIds = $weeks->pluck('id')->all();

        // Load all WeekContent rows in one query, eager-load the CO relation
        $allContents = WeekContent::whereIn('syllabus_week_id', $weekIds)
            ->with('courseOutcome')
            ->get();

        // Index as $contentMap[week_id][component_type] for O(1) lookup
        $contentMap = [];
        foreach ($allContents as $content) {
            $contentMap[$content->syllabus_week_id][$content->component_type] = $content;
        }

        // Load any previously saved evaluation items to pre-fill weight inputs
        $evalMap = SyllabusEvaluationItem::where('syllabus_id', $this->syllabusId)
            ->get()
            ->keyBy('week_content_id');

        $rows   = [];
        $inputs = [];

        // Count exam rows in order to assign sequential term labels: 1st, 2nd, Final
        $examTermLabels = ['1st Term', '2nd Term', 'Final Term'];
        $examCount      = 0;

        foreach ($weeks as $week) {
            $lecContent = $contentMap[$week->id]['LEC'] ?? null;
            $labContent = $contentMap[$week->id]['LAB'] ?? null;

            // Trim tasks once — used multiple times below
            $lecTask = trim($lecContent->assessment_task ?? '');
            $labTask = trim($labContent->assessment_task ?? '');

            // Skip weeks where both sides have nothing to evaluate
            if ($lecTask === '' && $labTask === '') {
                continue;
            }

            // Skip non-teaching weeks — they are locked placeholder rows written
            // by WeeklyCoverageStep, not real assessable tasks.
            // A week is non-teaching if either side carries that label
            // (both sides get the same label from computeLockedWeeks).
            if ($lecTask === 'Non-Teaching Week' || $labTask === 'Non-Teaching Week') {
                continue;
            }

            // Detect exam rows: WeeklyCoverageStep writes "Exam" into the task label
            // for both LEC ("1st Term Exam") and LAB ("1st Term Practical Exam").
            $isExam = str_contains(strtolower($lecTask), 'exam')
                   || str_contains(strtolower($labTask), 'exam');

            // Assign a sequential term label only for exam rows
            $termLabel = null;
            if ($isExam) {
                $termLabel = $examTermLabels[min($examCount, 2)];
                $examCount++;
            }

            // ── LEC side ──────────────────────────────────────────────────────
            // Only build a LEC slot when LEC actually has an assessment task.
            // If $lecTask is empty the LEC columns are disabled in the blade.
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
                    'outcome_label' => $lecEval?->outcome_label ?? '',
                ];
            }

            // ── LAB side ──────────────────────────────────────────────────────
            // Only build a LAB slot when the course has LAB AND LAB has a task.
            // If $labTask is empty the LAB columns are disabled in the blade.
            $labRow = null;
            if ($this->courseHasLab && $labTask !== '') {
                $labEval = $evalMap->get($labContent->id);
                $labRow  = [
                    'week_content_id' => $labContent->id,
                    'co_code'         => $labContent->courseOutcome?->co_code,
                    'task_label'      => $labTask,
                ];
                $inputs[$labContent->id] = [
                    'weight'        => $labEval?->weight !== null ? (string) $labEval->weight : '',
                    'outcome_label' => $labEval?->outcome_label ?? '',
                ];
            }

            $rows[] = [
                'week_no'    => $week->week_no,
                'is_exam'    => $isExam,
                'term_label' => $termLabel,
                'lec'        => $lecRow,  // null = no LEC task → blade shows disabled placeholder
                'lab'        => $labRow,  // null = no LAB task → blade shows disabled placeholder
            ];
        }

        $this->rows   = $rows;
        $this->inputs = $inputs;
    }

    // ── Private: save to DB ───────────────────────────────────────────────────

    // Write $inputs to the syllabus_evaluation_items table.
    // One SyllabusEvaluationItem per WeekContent row (LEC and LAB are separate DB rows).
    private function persistEvaluation(): void
    {
        $syllabus = Syllabus::with('course')->find($this->syllabusId);
        if (! $syllabus) {
            return;
        }
        $courseId = (int) $syllabus->course?->id;

        foreach ($this->rows as $row) {
            // Save LEC item — only when this row actually has a LEC task
            if (isset($row['lec']['week_content_id'])) {
                $this->saveOneItem(
                    weekContentId: (int) $row['lec']['week_content_id'],
                    courseId:      $courseId,
                    isExam:        $row['is_exam'],
                    termLabel:     $row['term_label'],
                );
            }

            // Save LAB item — only when this row actually has a LAB task
            if ($this->courseHasLab && isset($row['lab']['week_content_id'])) {
                $this->saveOneItem(
                    weekContentId: (int) $row['lab']['week_content_id'],
                    courseId:      $courseId,
                    isExam:        $row['is_exam'],
                    termLabel:     $row['term_label'],
                );
            }
        }
    }

    // Upsert a single SyllabusEvaluationItem for the given WeekContent row.
    private function saveOneItem(
        int $weekContentId,
        int $courseId,
        bool $isExam,
        ?string $termLabel,
    ): void {
        $data = $this->inputs[$weekContentId] ?? [];

        // Convert weight to int, or null if the field was left blank
        $weightRaw = trim((string) ($data['weight'] ?? ''));
        $weight    = $weightRaw !== '' ? (int) $weightRaw : null;

        // Outcome label for rows not mapped to a specific CO (e.g. "MVGO")
        $outcomeLabel = trim((string) ($data['outcome_label'] ?? ''));
        $outcomeLabel = $outcomeLabel !== '' ? $outcomeLabel : null;

        // kind and exam_type derived from whether this is an exam row
        if ($isExam) {
            $kind     = 'exam';
            $examType = match ($termLabel) {
                '1st Term'   => 'first_term',
                '2nd Term'   => 'second_term',
                'Final Term' => 'final_term',
                default      => null,
            };
        } else {
            $kind     = 'activity';
            $examType = null;
        }

        SyllabusEvaluationItem::updateOrCreate(
            ['week_content_id' => $weekContentId],
            [
                'syllabus_id'   => $this->syllabusId,
                'course_id'     => $courseId,
                'outcome_label' => $outcomeLabel,
                'kind'          => $kind,
                'exam_type'     => $examType,
                'weight'        => $weight,
            ]
        );
    }
}