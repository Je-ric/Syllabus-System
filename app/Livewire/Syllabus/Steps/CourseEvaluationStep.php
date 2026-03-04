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
    public bool $courseHasLab = false;

    // Rows shown in the evaluation table.
    //
    // Shape:
    // [
    //   'week_no'    => 1,
    //   'is_exam'    => false,
    //   'is_mvgo'    => true,   // ← NEW: true only for week 1 (MVGO week)
    //   'term_label' => null,
    //   'lec' => [
    //     'week_content_id' => 3,
    //     'co_code'         => null,   // always null for MVGO (no CO assigned)
    //     'task_label'      => 'Quiz 1',
    //   ],
    //   'lab' => null,
    // ]
    public array $rows = [];

    // Weight inputs keyed by week_content_id.
    // For MVGO rows, outcome_label is always pre-filled as 'MVGO' and is
    // not editable in the blade — the badge is shown instead.
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

    #[On('syllabus-step-changed')]
    public function onStepChanged(string $step): void
    {
        if ($step === 'course_evaluation') {
            $this->loadData();
        }
    }

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

    public function save(): void
    {
        $this->persistEvaluation();
        $this->dispatch('lw-toast', type: 'success', message: 'Course Evaluation saved.');
        $this->dispatch('syllabus-step-saved', step: 'course_evaluation');
    }

    // ── Private: load data ────────────────────────────────────────────────────

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

        $allContents = WeekContent::whereIn('syllabus_week_id', $weekIds)
            ->with('courseOutcome')
            ->get();

        $contentMap = [];
        foreach ($allContents as $content) {
            $contentMap[$content->syllabus_week_id][$content->component_type] = $content;
        }

        $evalMap = SyllabusEvaluationItem::where('syllabus_id', $this->syllabusId)
            ->get()
            ->keyBy('week_content_id');

        $rows   = [];
        $inputs = [];

        $examTermLabels = ['1st Term', '2nd Term', 'Final Term'];
        $examCount      = 0;

        foreach ($weeks as $week) {
            $lecContent = $contentMap[$week->id]['LEC'] ?? null;
            $labContent = $contentMap[$week->id]['LAB'] ?? null;

            $lecTask = trim($lecContent->assessment_task ?? '');
            $labTask = trim($labContent->assessment_task ?? '');

            // Skip weeks with no assessable tasks on either side
            if ($lecTask === '' && $labTask === '') {
                continue;
            }

            // Skip non-teaching week placeholder rows
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

            // ── MVGO flag ─────────────────────────────────────────────────────
            // Week 1 is always the MVGO (Mission-Vision-Goals-Objectives) week.
            // It never gets a CO assigned — instead it shows a static MVGO badge
            // in both the Weekly Coverage CO field and the Evaluation CO column.
            // The outcome_label is always pre-filled as 'MVGO' for this week so
            // it is saved correctly even if the faculty never visits the evaluation.
            $isMvgo = ((int) $week->week_no === 1);

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
                    // For MVGO, outcome_label is always 'MVGO' — not user-editable
                    'weight'        => $lecEval?->weight !== null ? (string) $lecEval->weight : '',
                    'outcome_label' => $isMvgo ? 'MVGO' : ($lecEval?->outcome_label ?? ''),
                ];
            }

            // ── LAB side ──────────────────────────────────────────────────────
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
                    'outcome_label' => $isMvgo ? 'MVGO' : ($labEval?->outcome_label ?? ''),
                ];
            }

            // Skip row entirely if nothing visible on either side
            if ($lecRow === null && $labRow === null) {
                continue;
            }

            $rows[] = [
                'week_no'    => (int) $week->week_no,
                'is_exam'    => $isExam,
                'is_mvgo'    => $isMvgo,
                'term_label' => $termLabel,
                'lec'        => $lecRow,
                'lab'        => $labRow,
            ];
        }

        $this->rows   = $rows;
        $this->inputs = $inputs;
    }

    // ── Private: save to DB ───────────────────────────────────────────────────

    private function persistEvaluation(): void
    {
        $syllabus = Syllabus::with('course')->find($this->syllabusId);
        if (! $syllabus) {
            return;
        }
        $courseId = (int) $syllabus->course?->id;

        foreach ($this->rows as $row) {
            if (isset($row['lec']['week_content_id'])) {
                $this->saveOneItem(
                    weekContentId: (int) $row['lec']['week_content_id'],
                    courseId:      $courseId,
                    isExam:        $row['is_exam'],
                    isMvgo:        $row['is_mvgo'],
                    termLabel:     $row['term_label'],
                );
            }

            if ($this->courseHasLab && isset($row['lab']['week_content_id'])) {
                $this->saveOneItem(
                    weekContentId: (int) $row['lab']['week_content_id'],
                    courseId:      $courseId,
                    isExam:        $row['is_exam'],
                    isMvgo:        $row['is_mvgo'],
                    termLabel:     $row['term_label'],
                );
            }
        }
    }

    private function saveOneItem(
        int $weekContentId,
        int $courseId,
        bool $isExam,
        bool $isMvgo,
        ?string $termLabel,
    ): void {
        $data = $this->inputs[$weekContentId] ?? [];

        $weightRaw = trim((string) ($data['weight'] ?? ''));
        $weight    = $weightRaw !== '' ? (int) $weightRaw : null;

        // MVGO rows always save 'MVGO' as the outcome label — never blank, never editable
        $outcomeLabel = $isMvgo
            ? 'MVGO'
            : trim((string) ($data['outcome_label'] ?? ''));
        $outcomeLabel = $outcomeLabel !== '' ? $outcomeLabel : null;

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