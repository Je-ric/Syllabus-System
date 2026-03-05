<?php

namespace App\Livewire\Syllabus\Steps;

use App\Models\CourseOutcome;
use App\Models\Syllabus;
use Livewire\Attributes\On;
use Livewire\Component;

class CourseOutcomesStep extends Component
{
    public int $syllabusId;

    // Each row: ['id' => int|null, 'description' => string]
    // Owned entirely by Livewire — no @entangle, no Alpine proxy issues.
    // wire:model.live binds each textarea directly to this array.
    public array $rows = [];

    public array $programOutcomes = [];

    // ── Mount ─────────────────────────────────────────────────────────────────

    public function mount(int $syllabusId): void
    {
        $this->syllabusId = $syllabusId;
        $this->loadData();
    }

    // ── Render ────────────────────────────────────────────────────────────────

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
        $this->save(silent: true);
        $this->dispatch('syllabus-step-saved', step: 'course_outcomes');
        $this->dispatch('syllabus-course-outcomes-updated');
    }

    // ── Public actions ────────────────────────────────────────────────────────

    public function addRow(): void
    {
        $this->rows[] = ['id' => null, 'description' => ''];
    }

    public function removeRow(int $index): void
    {
        $row = $this->rows[$index] ?? null;
        if (! $row) {
            return;
        }

        // If it has an ID, delete from DB
        if (! empty($row['id'])) {
            CourseOutcome::where('syllabus_id', $this->syllabusId)
                ->where('id', (int) $row['id'])
                ->delete();
            $this->dispatch('lw-toast', type: 'success', message: 'Course Outcome removed.');
            $this->dispatch('syllabus-course-outcomes-updated');
        }

        array_splice($this->rows, $index, 1);
    }

    // Called by the Save All button directly via wire:click.
    // No Alpine, no $wire.call(), no proxy — just a plain Livewire action.
    public function saveAll(): void
    {
        $this->save(silent: false);
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function save(bool $silent = false): void
    {
        // Filter out completely blank rows silently — don't save them, don't error
        $toSave = array_filter(
            $this->rows,
            fn ($row) => trim((string) ($row['description'] ?? '')) !== ''
        );

        if (empty($toSave)) {
            if (! $silent) {
                $this->dispatch('lw-toast', type: 'warning', message: 'Add at least one Course Outcome.');
            }
            return;
        }

        $existing = CourseOutcome::where('syllabus_id', $this->syllabusId)
            ->get()
            ->keyBy('id');

        $savedIds = [];
        $coNumber = 1;

        foreach ($toSave as $row) {
            $description = trim((string) ($row['description'] ?? ''));
            $coCode      = 'CO' . $coNumber++;
            $id          = ! empty($row['id']) ? (int) $row['id'] : null;

            if ($id && $existing->has($id)) {
                $existing[$id]->update([
                    'co_code'     => $coCode,
                    'description' => $description,
                ]);
                $savedIds[] = $id;
            } else {
                $new        = CourseOutcome::create([
                    'syllabus_id' => $this->syllabusId,
                    'co_code'     => $coCode,
                    'description' => $description,
                ]);
                $savedIds[] = $new->id;
            }
        }

        // Delete any DB rows the user removed
        $toDelete = $existing->keys()->diff($savedIds);
        if ($toDelete->isNotEmpty()) {
            CourseOutcome::where('syllabus_id', $this->syllabusId)
                ->whereIn('id', $toDelete->all())
                ->delete();
        }

        // Reload rows from DB so IDs and codes are fresh
        $this->reloadRows();

        if (! $silent) {
            $this->dispatch('lw-toast', type: 'success', message: 'Course Outcomes saved.');
        }

        $this->dispatch('syllabus-step-dirty', step: 'course_outcomes', dirty: false);
        $this->dispatch('syllabus-course-outcomes-updated');
    }

    private function loadData(): void
    {
        $this->reloadRows();

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

    private function reloadRows(): void
    {
        $this->rows = CourseOutcome::where('syllabus_id', $this->syllabusId)
            ->orderBy('co_code')
            ->get()
            ->map(fn ($co) => [
                'id'          => $co->id,
                'description' => $co->description,
            ])
            ->values()
            ->all();

        // Always keep at least one blank row so the form isn't empty on first visit
        if (empty($this->rows)) {
            $this->rows = [['id' => null, 'description' => '']];
        }
    }
}