<?php

namespace App\Livewire\Syllabus\Steps;

use App\Services\Syllabus\CourseEvaluationService;
use Livewire\Attributes\On;
use Livewire\Component;

class CourseEvaluationStep extends Component
{
    // ── Public state ──────────────────────────────────────────────────────────

    public int  $syllabusId;
    public int  $stepNumber = 5;
    public bool $isLoaded = false;
    
    // Store initial state for dirty checking
    private array $initialInputs = [];

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
        return view('livewire.syllabus.wizard.steps.course-evaluation');
    }

    public function updated(string $propertyName): void
    {
        if (str_starts_with($propertyName, 'inputs.') && str_ends_with($propertyName, '.weight')) {
            $this->recomputeTotalsFromInputs();
            // Mark step as dirty when weights change
            $this->dispatch('syllabus-step-dirty', step: 'course_evaluation', dirty: true);
        }
    }

    // ── Event listeners ───────────────────────────────────────────────────────

    #[On('syllabus-step-changed')]
    public function onStepChanged(string $step): void
    {
        if ($step === 'course_evaluation') {
            // Only reload data if not already loaded (prevent unnecessary DB queries)
            if (! $this->isLoaded) {
                $this->loadData();
            }
        }
    }

    #[On('syllabus-save-step')]
    public function onSaveRequested(string $step): void
    {
        if ($step !== 'course_evaluation') {
            return;
        }
        try {
            $this->persistEvaluation();
            $this->dispatch('syllabus-step-saved', step: 'course_evaluation');
            // Update initial state after successful save
            $this->initialInputs = $this->inputs;
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('syllabus-step-save-failed', step: 'course_evaluation', error: $e->getMessage());
        }
    }

    #[On('request-eval-flush-and-navigate')]
    public function onEvalFlushAndNavigate(string $toStep, ?string $previousStep = null): void
    {
        // Check if there are unsaved changes before proceeding
        $isDirty = $this->checkIfDirty();
        
        try {
            if ($isDirty) {
                $this->persistEvaluation();
                $this->dispatch('lw-toast', type: 'success', message: 'Course Evaluation saved.');
            }
            $this->dispatch('navigate-after-save', step: $toStep);
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('syllabus-step-save-failed', step: 'course_evaluation', error: $e->getMessage(), previousStep: $previousStep);
        }
    }

    // ── Public actions ────────────────────────────────────────────────────────

    public function save(): void
    {
        $this->persistEvaluation();
        $this->dispatch('lw-toast', type: 'success', message: 'Course Evaluation saved.');
        $this->dispatch('syllabus-step-saved', step: 'course_evaluation');
        
        // Update initial state after successful save
        $this->initialInputs = $this->inputs;
    }

    private function checkIfDirty(): bool
    {
        // Check if any inputs have changed from initial state
        if (empty($this->initialInputs)) {
            return false; // No initial state to compare against
        }

        foreach ($this->inputs as $id => $input) {
            if (!isset($this->initialInputs[$id])) {
                return true; // New input added
            }
            if ($this->inputs[$id] !== $this->initialInputs[$id]) {
                return true; // Input value changed
            }
        }

        // Check if any inputs were removed
        foreach ($this->initialInputs as $id => $input) {
            if (!isset($this->inputs[$id])) {
                return true; // Input was removed
            }
        }

        return false;
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
        
        // Store initial state for dirty checking
        $this->initialInputs = $this->inputs;
        
        $this->isLoaded = true;
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
