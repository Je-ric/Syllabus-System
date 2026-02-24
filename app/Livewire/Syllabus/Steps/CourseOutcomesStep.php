<?php

namespace App\Livewire\Syllabus\Steps;

use App\Models\CourseOutcome;
use App\Models\Syllabus;
use Livewire\Attributes\On;
use Livewire\Component;

class CourseOutcomesStep extends Component
{
    public int $syllabusId;
    public bool $isLoaded = false;
    public array $courseOutcomes = [];
    public array $programOutcomes = [];
    public ?string $coAddError = null;

    public function mount(int $syllabusId): void
    {
        $this->syllabusId = $syllabusId;
        $this->loadData();
    }

    #[On('syllabus-step-changed')]
    public function onStepChanged(string $step): void
    {
        if ($step !== 'course_outcomes') {
            return;
        }

        $this->loadData();
    }

    #[On('syllabus-save-step')]
    public function onSaveRequested(string $step): void
    {
        if ($step !== 'course_outcomes') {
            return;
        }

        if ($this->saveCourseOutcomes()) {
            $this->dispatch('syllabus-step-saved', step: 'course_outcomes', message: 'Course Outcomes saved.');
            $this->dispatch('syllabus-course-outcomes-updated');
        }
    }

    public function updatedCourseOutcomes(): void
    {
        if (!$this->isLoaded) {
            return;
        }

        $this->resequenceCourseOutcomes();
        $this->coAddError = $this->hasBlankCourseOutcome()
            ? 'Please fill the blank CO before adding a new one.'
            : null;

        $this->dispatch('syllabus-step-dirty', step: 'course_outcomes', dirty: true);
    }

    public function addCourseOutcome(): void
    {
        $this->loadData();

        if ($this->hasBlankCourseOutcome()) {
            $this->coAddError = 'Please fill the blank CO before adding a new one.';
            $this->dispatch('lw-toast', type: 'warning', message: $this->coAddError);
            return;
        }

        $this->courseOutcomes[] = [
            'id' => null,
            'temp_key' => $this->newOutcomeTempKey(),
            'co_code' => 'CO' . (count($this->courseOutcomes) + 1),
            'description' => '',
        ];

        $this->resequenceCourseOutcomes();
        $this->dispatch('syllabus-step-dirty', step: 'course_outcomes', dirty: true);
    }

    public function removeCourseOutcome(int $index): void
    {
        $this->loadData();
        if (!isset($this->courseOutcomes[$index])) {
            return;
        }

        array_splice($this->courseOutcomes, $index, 1);
        $this->resequenceCourseOutcomes();
        $this->dispatch('syllabus-step-dirty', step: 'course_outcomes', dirty: true);
    }

    public function render()
    {
        return view('livewire.syllabus.steps.course-outcomes');
    }

    private function loadData(): void
    {
        if ($this->isLoaded) {
            return;
        }

        $this->courseOutcomes = CourseOutcome::query()
            ->where('syllabus_id', $this->syllabusId)
            ->orderBy('co_code')
            ->get()
            ->map(fn($co) => [
                'id' => $co->id,
                'temp_key' => 'co_' . $co->id,
                'co_code' => $co->co_code,
                'description' => $co->description,
            ])->values()->all();

        $syllabus = Syllabus::query()
            ->with('course.program.outcomes')
            ->findOrFail($this->syllabusId);

        $this->programOutcomes = $syllabus->course?->program?->outcomes
            ?->map(fn($po) => [
                'po_code' => $po->po_code,
                'po_text' => $po->po_text,
            ])->values()->all() ?? [];

        $this->resequenceCourseOutcomes();
        $this->isLoaded = true;
    }

    private function saveCourseOutcomes(): bool
    {
        $this->loadData();

        $this->courseOutcomes = array_values($this->courseOutcomes ?? []);
        foreach ($this->courseOutcomes as $i => $outcome) {
            $this->courseOutcomes[$i]['co_code'] = 'CO' . ($i + 1);
            if (empty($this->courseOutcomes[$i]['temp_key'])) {
                $this->courseOutcomes[$i]['temp_key'] = !empty($outcome['id'])
                    ? 'co_' . $outcome['id']
                    : $this->newOutcomeTempKey();
            }
        }

        $existingCos = CourseOutcome::query()
            ->where('syllabus_id', $this->syllabusId)
            ->get()
            ->keyBy('id');

        $validDescriptions = collect($this->courseOutcomes)
            ->filter(fn($co) => trim((string) ($co['description'] ?? '')) !== '')
            ->values();

        if ($existingCos->isNotEmpty() && $validDescriptions->isEmpty()) {
            $this->coAddError = 'At least one Course Outcome description is required before saving.';
            $this->dispatch('lw-toast', type: 'error', message: $this->coAddError);
            return false;
        }

        $submittedIds = collect($this->courseOutcomes)
            ->pluck('id')
            ->filter()
            ->map(fn($id) => (int) $id)
            ->values()
            ->all();

        $idsToDelete = $existingCos->keys()->diff($submittedIds);
        if ($idsToDelete->isNotEmpty()) {
            CourseOutcome::query()
                ->where('syllabus_id', $this->syllabusId)
                ->whereIn('id', $idsToDelete->all())
                ->delete();
        }

        $saved = false;
        $hasValidOutcomes = false;
        foreach ($this->courseOutcomes as $index => $outcome) {
            $description = trim((string) ($outcome['description'] ?? ''));
            if ($description === '') {
                continue;
            }

            $hasValidOutcomes = true;
            $coCode = $this->courseOutcomes[$index]['co_code'];

            if (!empty($outcome['id']) && $existingCos->has((int) $outcome['id'])) {
                $co = $existingCos[(int) $outcome['id']];
                $hasChanged = $co->co_code !== $coCode || trim((string) $co->description) !== $description;
                if ($hasChanged) {
                    $co->update([
                        'co_code' => $coCode,
                        'description' => $description,
                    ]);
                    $saved = true;
                }
                continue;
            }

            $created = CourseOutcome::create([
                'syllabus_id' => $this->syllabusId,
                'co_code' => $coCode,
                'description' => $description,
            ]);

            $this->courseOutcomes[$index]['id'] = $created->id;
            $this->courseOutcomes[$index]['temp_key'] = 'co_' . $created->id;
            $saved = true;
        }

        if ($saved || $hasValidOutcomes) {
            $this->dispatch('syllabus-step-dirty', step: 'course_outcomes', dirty: false);
        }

        return $saved || $hasValidOutcomes;
    }

    private function newOutcomeTempKey(): string
    {
        return uniqid('new_', false);
    }

    private function resequenceCourseOutcomes(): void
    {
        $this->courseOutcomes = array_values($this->courseOutcomes ?? []);
        foreach ($this->courseOutcomes as $i => $outcome) {
            $this->courseOutcomes[$i]['co_code'] = 'CO' . ($i + 1);
            if (empty($this->courseOutcomes[$i]['temp_key'])) {
                $this->courseOutcomes[$i]['temp_key'] = !empty($outcome['id'])
                    ? 'co_' . $outcome['id']
                    : $this->newOutcomeTempKey();
            }
            if (!array_key_exists('description', $this->courseOutcomes[$i])) {
                $this->courseOutcomes[$i]['description'] = '';
            }
        }
    }

    private function hasBlankCourseOutcome(): bool
    {
        foreach ($this->courseOutcomes as $outcome) {
            if (trim((string) ($outcome['description'] ?? '')) === '') {
                return true;
            }
        }

        return false;
    }
}
