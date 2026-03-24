<?php

namespace App\Livewire\Syllabus\Steps;

use App\Models\Syllabus;
use App\Services\Syllabus\CourseOutcomeService;
use Livewire\Attributes\On;
use Livewire\Component;

// Course Outcomes step — individual CRUD per outcome.
//
// UI contract (enforced by Alpine in the blade):
//   editingId  = int|null — which saved CO card is in edit mode
//   addingNew  = bool     — whether the "Add" form is open
//   savingId   = int|null — which card is mid-save (shows spinner on that card only)
//   deletingId = int|null — which card is mid-delete
//
// Livewire owns: $outcomes (the authoritative list), $programOutcomes (read-only).
// Alpine owns: all transient UI state (editingId, draft text, etc).
//
// Individual save/delete instead of saveAll:
//   → Instant per-row feedback. Users know exactly which CO saved.
//   → No "unsaved" ambiguity — every card is either persisted or clearly in draft.
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
    // Individual-save model means there is nothing pending — just signal done.
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
    // CRUD — called from Alpine via $wire.*
    // ══════════════════════════════════════════════════════════════════════════

    // Create a new CO. Called by Alpine: await $wire.createOutcome(draftText)
    public function createOutcome(string $description): void
    {
        try {
            app(CourseOutcomeService::class)->create($this->syllabusId, $description);
        } catch (\InvalidArgumentException $e) {
            $this->dispatch('lw-toast', type: 'error', message: $e->getMessage());
            return;
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('lw-toast', type: 'error', message: 'Unable to save Course Outcome.');
            return;
        }

        $this->outcomes = app(CourseOutcomeService::class)->all($this->syllabusId);
        $this->dispatch('co-saved');
        $this->dispatch('lw-toast', type: 'success', message: 'Course Outcome added.');
        $this->dispatch('syllabus-course-outcomes-updated');
    }

    // Update an existing CO's description. Called by Alpine: await $wire.updateOutcome(id, draftText)
    public function updateOutcome(int $outcomeId, string $description): void
    {
        try {
            app(CourseOutcomeService::class)->update($this->syllabusId, $outcomeId, $description);
        } catch (\InvalidArgumentException $e) {
            $this->dispatch('lw-toast', type: 'error', message: $e->getMessage());
            return;
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('lw-toast', type: 'error', message: 'Unable to update Course Outcome.');
            return;
        }

        $this->outcomes = app(CourseOutcomeService::class)->all($this->syllabusId);
        $this->dispatch('co-saved');
        $this->dispatch('lw-toast', type: 'success', message: 'Course Outcome updated.');
        $this->dispatch('syllabus-course-outcomes-updated');
    }

    // Delete a CO by id. Called by Alpine: await $wire.deleteOutcome(id)
    public function deleteOutcome(int $outcomeId): void
    {
        try {
            app(CourseOutcomeService::class)->delete($this->syllabusId, $outcomeId);
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('lw-toast', type: 'error', message: 'Unable to delete Course Outcome.');
            $this->dispatch('co-delete-failed');
            return;
        }

        $this->outcomes = app(CourseOutcomeService::class)->all($this->syllabusId);
        $this->dispatch('co-deleted');
        $this->dispatch('lw-toast', type: 'success', message: 'Course Outcome removed.');
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
