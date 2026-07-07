<?php

namespace App\Livewire\Syllabus\Steps;

use App\Services\Syllabus\CourseEvaluationService;
use Livewire\Attributes\On;
use Livewire\Component;

class CourseEvaluationStep extends Component
{
    // ── Public state ──────────────────────────────────────────────────────────

    public int  $syllabusId;

    // True when the course has both LEC and LAB components.
    public bool $courseHasLab = false;

    // Performance standard strings pulled from the saved CourseComponent rows.
    // Used in the notes partial to show what the selected standard is.
    // e.g. '67%' for LEC, '33%' for LAB
    public ?string $lecPerformanceStd = null;
    public ?string $labPerformanceStd = null;

    // Pre-computed weight total targets (structural: 67/33/100).
    public int $lecStdNum = 100;
    public int $labStdNum = 33;
    // Running totals from saved weights.
    public int $lecTotal  = 0;
    public int $labTotal  = 0;
    // Passing mark from performance_standard (e.g. 60, 75).
    public int $lecPassingMark = 60;
    public int $labPassingMark = 60;

    // Rows shown in the evaluation table.
    // Shape (one entry per week that has at least one assessable task):
    // [
    //   'week_no'     => int,
    //   'is_exam'     => bool,
    //   'is_mvgo'     => bool,
    //   'term_label'  => string|null,
    //   'co_coverage' => string,  // auto-resolved CO code for exam rows (read-only)
    //   'lec'         => ['week_content_id' => int, 'co_code' => string|null, 'task_label' => string] | null,
    //   'lab'         => [...same...] | null,
    // ]
    public array $rows = [];

    // Weight and outcome label inputs keyed by week_content_id.
    // wire:model.lazy in the blade binds directly to these slots.
    // Shape: [ week_content_id => ['weight' => '10', 'outcome_label' => 'CO1'] ]
    // For MVGO rows, 'outcome_label' is pre-set to 'MVGO' and never editable.
    // For exam rows, 'outcome_label' is auto-filled from co_coverage on save.
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

    public function updated(string $propertyName): void
    {
        if (str_starts_with($propertyName, 'inputs.') && str_ends_with($propertyName, '.weight')) {
            $this->recomputeTotalsFromInputs();
        }
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

    // Accepts the full Alpine weights map in one round-trip, replacing the
    // previous Promise.all($wire.set()) loop that fired N simultaneous requests.
    // Shape: [ week_content_id => weight_int, ... ]
    public function setAllWeights(array $weights): void
    {
        foreach ($weights as $id => $value) {
            $id = (int) $id;
            if (isset($this->inputs[$id])) {
                $this->inputs[$id]['weight'] = is_numeric($value) ? (string) (int) $value : '';
            }
        }
        $this->recomputeTotalsFromInputs();
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
        $this->lecPassingMark    = $payload['lecPassingMark'] ?? 60;
        $this->labPassingMark    = $payload['labPassingMark'] ?? 60;
        $this->rows              = $payload['rows'];
        $this->inputs            = $payload['inputs'];
        $this->recomputeTotalsFromInputs();
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

    private function recomputeTotalsFromInputs(): void
    {
        $lecTotal = 0;
        $labTotal = 0;

        $parseWeight = static function (mixed $raw): int {
            if ($raw === null) {
                return 0;
            }

            $raw = trim((string) $raw);
            if ($raw === '') {
                return 0;
            }

            if (! is_numeric($raw)) {
                return 0;
            }

            return (int) round((float) $raw);
        };

        foreach ($this->rows as $row) {
            $lecId = $row['lec']['week_content_id'] ?? null;
            if ($lecId && isset($this->inputs[$lecId])) {
                $lecTotal += $parseWeight($this->inputs[$lecId]['weight'] ?? null);
            }

            if ($this->courseHasLab) {
                $labId = $row['lab']['week_content_id'] ?? null;
                if ($labId && isset($this->inputs[$labId])) {
                    $labTotal += $parseWeight($this->inputs[$labId]['weight'] ?? null);
                }
            }
        }

        $this->lecTotal = $lecTotal;
        $this->labTotal = $labTotal;
    }
}
