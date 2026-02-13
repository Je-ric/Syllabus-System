<?php

namespace App\Livewire\Syllabus\Concerns;

use App\Models\CourseOutcome;

trait HandlesCourseOutcomes
{
    public $courseOutcomes = [];
    // each item: ['id' => ?int, 'temp_key' => string, 'co_code' => string, 'description' => string]

    public ?string $coAddError = null;

    public function updatedCourseOutcomes($value, $key): void
    {
        if ($this->currentStep !== 'course_outcomes') {
            return;
        }

        if ($this->hasBlankCourseOutcome()) {
            $this->coAddError = 'Please fill the blank CO before adding a new one.';
        } else {
            $this->coAddError = null;
        }

        // Keep row numbering stable while typing and mark the step as dirty.
        $this->resequenceCourseOutcomes();
        $this->markStepDirty('course_outcomes');
    }

    public function addCourseOutcome(): void
    {
        if ($this->hasBlankCourseOutcome()) {
            $this->coAddError = 'Please fill the blank CO before adding a new one.';
            return;
        }

        $this->coAddError = null;
        $nextIndex = count($this->courseOutcomes) + 1;

        $this->courseOutcomes[] = [
            'id' => null,
            'temp_key' => $this->newOutcomeTempKey(),
            'co_code' => 'CO' . $nextIndex,
            'description' => '',
        ];

        $this->resequenceCourseOutcomes();
        $this->markStepDirty('course_outcomes');
    }

    public function removeCourseOutcome(int $index): void
    {
        if (!isset($this->courseOutcomes[$index])) {
            return;
        }

        array_splice($this->courseOutcomes, $index, 1);
        $this->resequenceCourseOutcomes();
        $this->markStepDirty('course_outcomes');
    }

    private function saveCourseOutcomes(): bool
    {
        if (!$this->syllabus) {
            return false;
        }

        $this->coAddError = null;

        // Keep a compact sequence and assign CO codes consistently before saving.
        $this->courseOutcomes = array_values($this->courseOutcomes ?? []);
        foreach ($this->courseOutcomes as $i => $outcome) {
            $this->courseOutcomes[$i]['co_code'] = 'CO' . ($i + 1);

            if (empty($this->courseOutcomes[$i]['temp_key'])) {
                $this->courseOutcomes[$i]['temp_key'] = !empty($outcome['id'])
                    ? 'co_' . $outcome['id']
                    : $this->newOutcomeTempKey();
            }
        }

        $existingCos = CourseOutcome::where('syllabus_id', $this->syllabus->id)
            ->get()
            ->keyBy('id');

        $validDescriptions = collect($this->courseOutcomes)
            ->filter(fn($co) => trim((string) ($co['description'] ?? '')) !== '')
            ->values();

        // Safety guard: prevent accidental full delete when payload is unexpectedly empty.
        if ($existingCos->isNotEmpty() && $validDescriptions->isEmpty()) {
            $this->coAddError = 'At least one Course Outcome description is required before saving.';
            return false;
        }

        $submittedIds = collect($this->courseOutcomes)
            ->pluck('id')
            ->filter()
            ->map(fn($id) => (int) $id)
            ->values()
            ->all();

        // Delete rows removed from UI.
        $idsToDelete = $existingCos->keys()->diff($submittedIds);
        if ($idsToDelete->isNotEmpty()) {
            CourseOutcome::where('syllabus_id', $this->syllabus->id)
                ->whereIn('id', $idsToDelete->all())
                ->delete();

            foreach ($idsToDelete as $deletedId) {
                unset($this->coPoMappings[$deletedId]);
            }
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
                $co = $existingCos->get((int) $outcome['id']);
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

            $newCo = CourseOutcome::create([
                'syllabus_id' => $this->syllabus->id,
                'co_code' => $coCode,
                'description' => $description,
            ]);

            $tempKey = (string) ($outcome['temp_key'] ?? '');
            $this->courseOutcomes[$index]['id'] = $newCo->id;
            $this->courseOutcomes[$index]['temp_key'] = 'co_' . $newCo->id;

            if ($tempKey !== '' && isset($this->coPoMappings[$tempKey])) {
                $this->coPoMappings[$newCo->id] = $this->coPoMappings[$tempKey];
                unset($this->coPoMappings[$tempKey]);
            } elseif (!isset($this->coPoMappings[$newCo->id])) {
                $this->coPoMappings[$newCo->id] = [];
            }

            $saved = true;
        }

        // Treat no-op valid payload as successful save so UI doesn't show false errors.
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
