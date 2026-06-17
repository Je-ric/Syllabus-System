<?php

namespace App\Livewire\Syllabus\Steps;

use App\Models\Syllabus;
use App\Services\Syllabus\CourseOutcomeService;
use Livewire\Attributes\On;
use Livewire\Component;

// Course Outcomes step — Batch / Draft-first model.
//
// UI contract:
//   Alpine owns all transient state (drafts[], deletedIds[], editingIdx, saving).
//   Livewire owns $outcomes (authoritative) and $programOutcomes (read-only).
//
//   Alpine calls ONE method: saveAll(drafts, deletedIds)
//     drafts[]     — [{ id: int|null, description: string, isNew: bool }]
//     deletedIds[] — int[] of persisted CO ids to remove
//
//   Livewire dispatches:
//     co-all-saved   → payload: { outcomes: [...] }  → Alpine re-syncs clean
//     co-save-failed → Alpine resets saving flag, preserves drafts
class CourseOutcomesStep extends Component
{
    // ── Identity ───────────────────────────────────────────────────────────────
    public int $syllabusId;

    // ── Data ───────────────────────────────────────────────────────────────────
    // Each item: ['id' => int, 'co_code' => string, 'description' => string]
    public array $outcomes = [];

    // Program Outcomes for the reference panel
    // Each item: ['po_code' => string, 'po_text' => string, 'ied' => string]
    public array $programOutcomes = [];

    // ══════════════════════════════════════════════════════════════════════════
    // LIFECYCLE
    // ══════════════════════════════════════════════════════════════════════════

    public function mount(int $syllabusId): void
    {
        $this->syllabusId = $syllabusId;
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.syllabus.steps.course-outcomes');
    }

    // ══════════════════════════════════════════════════════════════════════════
    // EVENT LISTENERS
    // ══════════════════════════════════════════════════════════════════════════

    #[On('syllabus-step-changed')]
    public function onStepChanged(string $step): void
    {
        if ($step === 'course_outcomes') {
            $this->loadData();
        }
    }

    // Auto-save on step navigation.
    // Alpine is responsible for warning the user about unsaved drafts before
    // navigation (via isDirty guard in the wizard). If they navigate away
    // anyway, we signal done without saving — drafts are intentionally lost.
    #[On('syllabus-save-step')]
    public function onSaveRequested(string $step): void
    {
        if ($step !== 'course_outcomes') {
            return;
        }

        $this->dispatch('syllabus-step-saved', step: 'course_outcomes');
        $this->dispatch('syllabus-course-outcomes-updated');
    }

    // ══════════════════════════════════════════════════════════════════════════
    // BATCH SAVE — single entry point called from Alpine
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Persist all pending changes in one shot.
     *
     * @param  array  $drafts      [{ id: int|null, description: string, isNew: bool }]
     * @param  array  $deletedIds  int[] — IDs of persisted COs to remove
     */
    public function saveAll(array $drafts, array $deletedIds): void
    {
        $service = app(CourseOutcomeService::class);

        try {
            // 1. Deletions first — avoids co_code collisions on re-numbering
            foreach ($deletedIds as $id) {
                $service->delete($this->syllabusId, (int) $id);
            }

            // 2. Updates for existing, dirty COs
            foreach ($drafts as $draft) {
                $description = trim($draft['description'] ?? '');

                if (empty($description)) {
                    continue; // skip empty drafts silently
                }

                if (!empty($draft['isNew'])) {
                    // 3. Creates for new drafts
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

        // Reload authoritative list and push back to Alpine
        $this->outcomes = $service->all($this->syllabusId);

        $this->dispatch('co-all-saved', outcomes: $this->outcomes);
        $this->dispatch('lw-toast', type: 'success', message: 'Course Outcomes saved.');
        $this->dispatch('syllabus-step-saved', step: 'course_outcomes');
        $this->dispatch('syllabus-course-outcomes-updated');
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PRIVATE HELPERS
    // ══════════════════════════════════════════════════════════════════════════

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
