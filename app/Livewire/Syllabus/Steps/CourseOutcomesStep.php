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

    // Cached from loadData() so render() doesn't re-query
    public array $courseInfo = [];

    // ── Lifecycle ─────────────────────────────────────────────────────────────

    public function mount(int $syllabusId): void
    {
        $this->syllabusId = $syllabusId;
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.syllabus.steps.course-outcomes', [
            'courseInfo' => $this->courseInfo,
        ]);
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

        // If Alpine has pending changes, flush them — saveAll() will dispatch
        // syllabus-step-saved itself after persisting. If nothing is pending,
        // mark saved immediately.
        $this->dispatch('request-co-flush-step');
    }

    /**
     * Called via Alpine when the parent dispatches 'request-co-save-and-navigate'.
     * Alpine's coManager saves pending changes, then calls this method to proceed.
     */
    public function onCoSaveAndNavigate(string $toStep): void
    {
        $this->dispatch('navigate-after-save', step: $toStep);
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

        // course.programOutcomes = pivot table (course_curriculum_maps) with ied
        $coursePoIedById = $syllabus->course?->programOutcomes
            ?->mapWithKeys(fn ($po) => [(int) $po->id => (string) ($po->pivot?->ied ?? '')]) ?? collect();

        // course.program.outcomes = all POs for the program
        $this->programOutcomes = $syllabus->course?->program?->outcomes
            ?->map(fn ($po) => [
                'po_code' => $po->po_code,
                'po_text' => $po->po_text,
                'ied'     => $coursePoIedById->get((int) $po->id, ''),
            ])->values()->all() ?? [];

        // Cache courseInfo so render() doesn't need a second query
        $course = $syllabus->course;
        if ($course) {
            $poRows = $course->program?->outcomes
                ?->map(fn ($po) => [
                    'po_code' => $po->po_code,
                    'po_text' => $po->po_text,
                    'ied'     => $coursePoIedById->get((int) $po->id, ''),
                ])->values()->all() ?? [];

            $this->courseInfo = [
                'course_code'   => $course->course_code,
                'course_title'  => $course->course_title,
                'description'   => $course->course_description,
                'lec_units'     => $course->lec_class_hours,
                'lab_units'     => $course->lab_class_hours,
                'program_title' => $course->program?->name,
                'po_rows'       => $poRows,
            ];
        } else {
            $this->courseInfo = [];
        }
    }
}
