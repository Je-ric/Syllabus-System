<?php

namespace App\Livewire\Syllabus\Steps;

use App\Models\CourseOutcome;
use App\Models\Syllabus;
use Livewire\Attributes\On;
use Livewire\Component;

class CourseOutcomesStep extends Component
{
    // ── Public state ──────────────────────────────────────────────────────────

    public int    $syllabusId;

    // This array is @entangle'd in the blade, so Alpine owns it during editing.
    // Shape per entry:
    // [
    //   'id'          => int|null,  // null = unsaved row
    //   'temp_key'    => string,    // stable key for x-for :key
    //   'co_code'     => 'CO1',     // display badge; resequenced on save
    //   'description' => string,
    // ]
    public array  $courseOutcomes  = [];
    public array  $programOutcomes = [];

    // Shown below the "Add CO" button when the user tries to add while a row is blank
    public ?string $coAddError = null;

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

    // Wizard dispatches this when the user navigates TO this step.
    // Reload so the list reflects any changes made since last visit.
    #[On('syllabus-step-changed')]
    public function onStepChanged(string $step): void
    {
        if ($step !== 'course_outcomes') {
            return;
        }
        $this->loadData();
    }

    // Wizard dispatches this just before navigating AWAY from this step.
    // Alpine pushes its current state back to Livewire via @entangle before
    // any $wire call fires, so $this->courseOutcomes already has the latest data.
    #[On('syllabus-save-step')]
    public function onSaveRequested(string $step): void
    {
        if ($step !== 'course_outcomes') {
            return;
        }

        // Pass $this->courseOutcomes directly — @entangle keeps it in sync
        if ($this->saveCourseOutcomes($this->courseOutcomes)) {
            $this->dispatch('syllabus-step-saved', step: 'course_outcomes');
            $this->dispatch('syllabus-course-outcomes-updated');
        }
    }

    // ── Public actions ────────────────────────────────────────────────────────

    // Called from Alpine's saveCos() via $wire.call('saveCourseOutcomes', this.cos).
    // Receives the full array from Alpine (including any unsaved rows the user added).
    public function saveCourseOutcomes(array $cosData): bool
    {
        // Reject if any description is blank
        foreach ($cosData as $index => $co) {
            if (trim((string) ($co['description'] ?? '')) === '') {
                $this->dispatch('lw-toast', type: 'warning',
                    message: 'CO row ' . ($index + 1) . ' is blank — fill it in before saving.');
                return false;
            }
        }

        $existingCos = CourseOutcome::where('syllabus_id', $this->syllabusId)
            ->get()
            ->keyBy('id');

        // IDs present in the submitted data; everything else gets deleted
        $submittedIds = collect($cosData)
            ->pluck('id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        // Delete outcomes the user removed
        $idsToDelete = $existingCos->keys()->diff($submittedIds);
        if ($idsToDelete->isNotEmpty()) {
            CourseOutcome::where('syllabus_id', $this->syllabusId)
                ->whereIn('id', $idsToDelete->all())
                ->delete();
        }

        // Upsert remaining outcomes with resequenced codes
        foreach ($cosData as $index => $co) {
            $description = trim((string) ($co['description'] ?? ''));
            if ($description === '') {
                continue;
            }

            $coCode = 'CO' . ($index + 1);

            if (! empty($co['id']) && $existingCos->has((int) $co['id'])) {
                // Update existing
                $existingCos[(int) $co['id']]->update([
                    'co_code'     => $coCode,
                    'description' => $description,
                ]);
            } else {
                // Create new
                CourseOutcome::create([
                    'syllabus_id' => $this->syllabusId,
                    'co_code'     => $coCode,
                    'description' => $description,
                ]);
            }
        }

        // Reload so the blade reflects real IDs and correct co_codes
        $this->reloadCourseOutcomes();
        $this->coAddError = null;

        $this->dispatch('lw-toast', type: 'success', message: 'Course Outcomes saved.');
        $this->dispatch('syllabus-step-dirty', step: 'course_outcomes', dirty: false);
        $this->dispatch('syllabus-course-outcomes-updated');

        return true;
    }

    // Called from Alpine when the user confirms deletion of a saved CO row.
    // Unsaved rows are removed client-side by splicing the Alpine array — no round-trip.
    public function removeSavedOutcome(int $id): void
    {
        CourseOutcome::where('syllabus_id', $this->syllabusId)
            ->where('id', $id)
            ->delete();

        $this->reloadCourseOutcomes();
        $this->dispatch('lw-toast', type: 'success', message: 'Course Outcome removed.');
        $this->dispatch('syllabus-step-dirty', step: 'course_outcomes', dirty: false);
        $this->dispatch('syllabus-course-outcomes-updated');
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    // Full data load: course outcomes + program outcomes reference panel.
    // Called from mount() and onStepChanged().
    private function loadData(): void
    {
        $this->reloadCourseOutcomes();

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

    // Reload only the course outcomes array from DB.
    // Called after save/delete to refresh IDs and codes without re-querying POs.
    private function reloadCourseOutcomes(): void
    {
        $this->courseOutcomes = CourseOutcome::where('syllabus_id', $this->syllabusId)
            ->orderBy('co_code')
            ->get()
            ->map(fn ($co) => [
                'id'          => $co->id,
                'temp_key'    => 'co_' . $co->id,
                'co_code'     => $co->co_code,
                'description' => $co->description,
            ])
            ->values()
            ->all();
    }
}