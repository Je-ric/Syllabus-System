<?php

namespace App\Livewire\Syllabus\Steps;

use App\Services\Syllabus\CourseEvaluationService;
use Livewire\Attributes\On;
use Livewire\Component;

class CourseEvaluationStep extends Component
{
    // ── Public state ──────────────────────────────────────────────────────────

    public int  $syllabusId;

    /** True when the course has both LEC and LAB components. */
    public bool $courseHasLab = false;

    /**
     * Performance standard strings pulled from the saved CourseComponent rows.
     * Used in the notes partial to show what the selected standard is and
     * compare it against the running weight total.
     * e.g. '67%' for LEC, '33%' for LAB
     */
    public ?string $lecPerformanceStd = null;
    public ?string $labPerformanceStd = null;

    /** Pre-computed weight total targets (structural: 67/33/100). */
    public int $lecStdNum = 100;
    public int $labStdNum = 33;
    /** Running totals from saved weights. */
    public int $lecTotal  = 0;
    public int $labTotal  = 0;
    /** Passing mark from performance_standard (e.g. 60, 75). */
    public int $lecPassingMark = 60;
    public int $labPassingMark = 60;

    /**
     * Rows shown in the evaluation table.
     *
     * Shape (one entry per week that has at least one assessable task):
     * [
     *   'week_no'     => int,
     *   'is_exam'     => bool,
     *   'is_mvgo'     => bool,          // true only for week 1
     *   'term_label'  => string|null,   // '1st Term' / '2nd Term' / 'Final Term'
     *   'co_coverage' => string,        // auto-resolved CO code for exam rows (read-only)
     *   'lec' => [
     *     'week_content_id' => int,
     *     'co_code'         => string|null,
     *     'task_label'      => string,
     *   ] | null,
     *   'lab' => [...same...] | null,
     * ]
     *
     * For exam rows, 'co_coverage' is the CO code of the last non-exam week
     * before this exam — resolved automatically in CourseEvaluationService.
     * It is NOT user-editable in the blade; a read-only badge is shown instead.
     */
    public array $rows = [];

    /**
     * Weight (and outcome label) inputs keyed by week_content_id.
     * wire:model.lazy in the blade binds directly to these slots.
     *
     * Shape: [ week_content_id => ['weight' => '10', 'outcome_label' => 'CO1'] ]
     *
     * For MVGO rows, 'outcome_label' is pre-set to 'MVGO' and never editable.
     * For exam rows, 'outcome_label' is auto-filled from co_coverage on save.
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

    // ── Private helpers ───────────────────────────────────────────────────────

    private function loadData(): void
    {
        $payload = app(CourseEvaluationService::class)->loadRows($this->syllabusId);

        $this->courseHasLab      = $payload['courseHasLab'];
        $this->lecPerformanceStd = $payload['lecPerformanceStd'];
        $this->labPerformanceStd = $payload['labPerformanceStd'];
        $this->lecStdNum         = $payload['lecStdNum']     ?? ($payload['courseHasLab'] ? 67 : 100);
        $this->labStdNum         = $payload['labStdNum']     ?? 33;
        $this->lecTotal          = $payload['lecTotal']      ?? 0;
        $this->labTotal          = $payload['labTotal']      ?? 0;
        $this->lecPassingMark    = $payload['lecPassingMark'] ?? 60;
        $this->labPassingMark    = $payload['labPassingMark'] ?? 60;
        $this->rows              = $payload['rows'];
        $this->inputs            = $payload['inputs'];
    }

    private function persistEvaluation(): void
    {
        app(CourseEvaluationService::class)->persist(
            syllabusId:   $this->syllabusId,
            rows:         $this->rows,
            inputs:       $this->inputs,
            courseHasLab: $this->courseHasLab,
        );
    }
}