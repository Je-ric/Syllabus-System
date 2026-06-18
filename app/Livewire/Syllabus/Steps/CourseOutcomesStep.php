<?php

namespace App\Livewire\Syllabus\Steps;

use App\Models\Syllabus;
use App\Services\Syllabus\CourseOutcomeService;
use Livewire\Attributes\On;
use Livewire\Component;

class CourseOutcomesStep extends Component
{
    public int $syllabusId;

    // Each item: ['id' => int, 'co_code' => string, 'description' => string]
    public array $outcomes = [];

    // Each item: ['po_code' => string, 'po_text' => string, 'ied' => string]
    public array $programOutcomes = [];

    // ── Lifecycle ─────────────────────────────────────────────────────────────

    public function mount(int $syllabusId): void
    {
        $this->syllabusId = $syllabusId;
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.syllabus.steps.course-outcomes');
    }

    // ── Event listeners ───────────────────────────────────────────────────────

    #[On('syllabus-step-changed')]
    public function onStepChanged(string $step): void
    {
        if ($step === 'course_outcomes') {
            $this->loadData();
        }
    }

    #[On('syllabus-save-step')]
    public function onSaveRequested(string $step): void
    {
        if ($step !== 'course_outcomes') {
            return;
        }

        $this->dispatch('syllabus-step-saved', step: 'course_outcomes');
        $this->dispatch('syllabus-course-outcomes-updated');
    }

    // ── Actions ───────────────────────────────────────────────────────────────

    /**
     * Delete a single persisted CO immediately (called from Alpine per-row).
     */
    public function deleteSingle(int $outcomeId): void
    {
        $service = app(CourseOutcomeService::class);

        try {
            $service->delete($this->syllabusId, $outcomeId);
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('lw-toast', type: 'error', message: 'Unable to delete Course Outcome.');
            $this->dispatch('co-save-failed');
            return;
        }

        $this->outcomes = $service->all($this->syllabusId);
        $this->dispatch('co-all-saved', outcomes: $this->outcomes);
        $this->dispatch('lw-toast', type: 'success', message: 'Course Outcome deleted.');
        $this->dispatch('syllabus-course-outcomes-updated');
    }

    /**
     * Persist all pending additions and edits in one shot.
     *
     * @param  array  $drafts  [{ id: int|null, description: string, isNew: bool }]
     */
    public function saveAll(array $drafts): void
    {
        $service = app(CourseOutcomeService::class);

        try {
            foreach ($drafts as $draft) {
                $description = trim($draft['description'] ?? '');

                if ($description === '') {
                    continue;
                }

                if (!empty($draft['isNew'])) {
                    $service->create($this->syllabusId, $description);
                } else {
                    $service->update($this->syllabusId, (int) $draft['id'], $description);
                }
            }
        } catch (\InvalidArgumentException $e) {
            $this->dispatch('lw-toast', type: 'error', message: $e->getMessage());
            $this->dispatch('co-save-failed');
            return;
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('lw-toast', type: 'error', message: 'Unable to save Course Outcomes. Please try again.');
            $this->dispatch('co-save-failed');
            return;
        }

        $this->outcomes = $service->all($this->syllabusId);
        $this->dispatch('co-all-saved', outcomes: $this->outcomes);
        $this->dispatch('lw-toast', type: 'success', message: 'Course Outcomes saved.');
        $this->dispatch('syllabus-step-saved', step: 'course_outcomes');
        $this->dispatch('syllabus-course-outcomes-updated');
    }

    // ── Private ───────────────────────────────────────────────────────────────

    private function loadData(): void
    {
        $this->outcomes = app(CourseOutcomeService::class)->all($this->syllabusId);

        $syllabus = Syllabus::with(['course.program.outcomes', 'course.programOutcomes'])
            ->findOrFail($this->syllabusId);

        $coursePoIedById = $syllabus->course?->programOutcomes
            ?->mapWithKeys(fn ($po) => [(int) $po->id => (string) ($po->pivot?->ied ?? '')]) ?? collect();

        $this->programOutcomes = $syllabus->course?->program?->outcomes
            ?->map(fn ($po) => [
                'po_code' => $po->po_code,
                'po_text' => $po->po_text,
                'ied'     => $coursePoIedById->get((int) $po->id, ''),
            ])->values()->all() ?? [];
    }
}
