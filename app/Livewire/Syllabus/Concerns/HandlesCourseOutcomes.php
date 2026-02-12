<?php

namespace App\Livewire\Syllabus\Concerns;

use App\Models\CourseOutcome;

trait HandlesCourseOutcomes
{
    public $courseOutcomes = [];
    // each item: ['id' => ?int, 'temp_key' => ?string, 'co_code' => string, 'description' => string]

    public ?string $coAddError = null;

    /**
     * Auto‑save handler for any change to course outcome fields.
     * Relies on the current wizard step so we don't run on other screens.
     */
    public function updatedCourseOutcomes($value, $name): void
    {
        if ($this->currentStep !== 'course_outcomes' || !$this->syllabus) {
            return;
        }

        $path = (string) $name;
        if (!str_starts_with($path, 'courseOutcomes.')) {
            $path = 'courseOutcomes.' . ltrim($path, '.');
        }

        $parts = explode('.', $path);
        if (count($parts) < 3) {
            return;
        }

        $index = (int) $parts[1];
        $field = $parts[2];
        if (!in_array($field, ['description', 'co_code'], true)) {
            return;
        }

        if ($this->saveCourseOutcomeAtIndex($index)) {
            $this->markDraftSaved();
        }
    }

    public function syncCourseOutcomeDescription(string $rowKey, string $description): void
    {
        if ($this->currentStep !== 'course_outcomes' || !$this->syllabus) {
            $this->skipRender();
            return;
        }

        $index = $this->findOutcomeIndexByRowKey($rowKey);
        if ($index === null) {
            $this->skipRender();
            return;
        }

        $this->courseOutcomes[$index]['description'] = $description;
        if ($this->saveCourseOutcomeAtIndex($index)) {
            $this->markDraftSaved();
        }

        // UI is already updated locally via Alpine, skip diff to keep typing smooth.
        $this->skipRender();
    }

    public function syncCourseOutcomeDescriptions(array $rows): void
    {
        if ($this->currentStep !== 'course_outcomes' || !$this->syllabus) {
            return;
        }

        $saved = false;

        foreach ($rows as $row) {
            $rowKey = (string) ($row['rowKey'] ?? '');
            if ($rowKey === '') {
                continue;
            }

            $index = $this->findOutcomeIndexByRowKey($rowKey);
            if ($index === null) {
                continue;
            }

            $description = (string) ($row['description'] ?? '');
            $this->courseOutcomes[$index]['description'] = $description;

            if ($this->saveCourseOutcomeAtIndex($index)) {
                $saved = true;
            }
        }

        if ($saved) {
            $this->markDraftSaved();
        }
    }

    public function addCourseOutcome()
    {

    // foreach course outcome, if description is empty, set coAddError to 'Fill the blank before adding.' and return
        // foreach ($this->courseOutcomes as $outcome) {
        //     if (empty(trim($outcome['description'] ?? ''))) {
        //         $this->coAddError = 'Fill the blank before adding.';
        //         return;
        //     }
        // }

        // if all descriptions are filled, clear the error and add a new course outcome with empty description and code CO{n+1}
        $this->coAddError = null;
        $nextNumber = count($this->courseOutcomes) + 1;
        $tempKey = $this->newOutcomeTempKey();
        $this->courseOutcomes[] = [
            'id' => null,
            'temp_key' => $tempKey,
            'co_code' => 'CO' . $nextNumber,
            'description' => '',
        ];

        // Initialize CO-PO mapping for new outcome
        // examples:
        // if we have 2 existing outcomes, and we add a new one,
        // the new outcome will have a temporary key 'new_2'
        // (since index starts at 0), and we will initialize an empty array for it in the coPoMappings
        $this->coPoMappings[$tempKey] = [];
    }

    public function removeCourseOutcome($index)
    {
        if (!isset($this->courseOutcomes[$index])) {
            return;
        }

        $oldMappings = $this->coPoMappings;
        $outcome = $this->courseOutcomes[$index];
        $mappingKey = $this->getOutcomeMappingKey($outcome, $index);

        unset($oldMappings[$mappingKey]);

        // Delete from database if it exists
        if (!empty($outcome['id'])) {
            CourseOutcome::where('id', $outcome['id'])
                ->where('syllabus_id', $this->syllabus->id)
                ->delete();
        }

        // Remove from the courseOutcomes array
        unset($this->courseOutcomes[$index]);
        $this->courseOutcomes = array_values($this->courseOutcomes);

        // Resequence codes and update mapping keys
        $newMappings = [];
        foreach ($this->courseOutcomes as $i => $item) {
            $newCode = 'CO' . ($i + 1);
            $this->courseOutcomes[$i]['co_code'] = $newCode;

            if (empty($this->courseOutcomes[$i]['temp_key']) && empty($item['id'])) {
                $this->courseOutcomes[$i]['temp_key'] = $this->newOutcomeTempKey();
            }

            if (!empty($item['id'])) {
                CourseOutcome::where('id', $item['id'])
                    ->where('syllabus_id', $this->syllabus->id)
                    ->update(['co_code' => $newCode]);
            }

            $newKey = $this->getOutcomeMappingKey($this->courseOutcomes[$i], $i);
            $newMappings[$newKey] = $oldMappings[$newKey] ?? [];
        }

        $this->coPoMappings = $newMappings;
        $this->markDraftSaved();
    }

    public function removeCourseOutcomeByRowKey(string $rowKey): void
    {
        $index = $this->findOutcomeIndexByRowKey($rowKey);
        if ($index === null) {
            return;
        }

        $this->removeCourseOutcome($index);
    }

    private function saveCourseOutcomes(): bool
    {
        $saved = false;

        foreach (array_keys($this->courseOutcomes) as $index) {
            if ($this->saveCourseOutcomeAtIndex($index)) {
                $saved = true;
            }
        }

        return $saved;
    }

    private function saveCourseOutcomeAtIndex(int $index): bool
    {
        if (!isset($this->courseOutcomes[$index])) {
            return false;
        }

        $outcome = $this->courseOutcomes[$index];
        $description = trim((string) ($outcome['description'] ?? ''));
        if ($description === '') {
            return false;
        }

        $coCode = $outcome['co_code'] ?? ('CO' . ($index + 1));

        if (!empty($outcome['id'])) {
            $co = CourseOutcome::where('id', $outcome['id'])
                ->where('syllabus_id', $this->syllabus->id)
                ->first();

            if (!$co) {
                return false;
            }

            $co->update([
                'co_code' => $coCode,
                'description' => $description,
            ]);

            return true;
        }

        $newCo = CourseOutcome::create([
            'syllabus_id' => $this->syllabus->id,
            'co_code' => $coCode,
            'description' => $description,
        ]);

        $tempKey = $this->courseOutcomes[$index]['temp_key'] ?? null;
        $this->courseOutcomes[$index]['id'] = $newCo->id;

        if ($tempKey && isset($this->coPoMappings[$tempKey])) {
            $this->coPoMappings[$newCo->id] = $this->coPoMappings[$tempKey];
            unset($this->coPoMappings[$tempKey]);
        } elseif (!isset($this->coPoMappings[$newCo->id])) {
            $this->coPoMappings[$newCo->id] = [];
        }

        return true;
    }

    private function getOutcomeMappingKey(array $outcome, int $fallbackIndex): string
    {
        if (!empty($outcome['id'])) {
            return (string) $outcome['id'];
        }

        if (!empty($outcome['temp_key'])) {
            return (string) $outcome['temp_key'];
        }

        return 'new_' . $fallbackIndex;
    }

    private function getOutcomeRowKey(array $outcome, int $fallbackIndex): string
    {
        if (!empty($outcome['temp_key'])) {
            return (string) $outcome['temp_key'];
        }

        if (!empty($outcome['id'])) {
            return 'co_' . $outcome['id'];
        }

        return 'new_' . $fallbackIndex;
    }

    private function findOutcomeIndexByRowKey(string $rowKey): ?int
    {
        foreach ($this->courseOutcomes as $index => $outcome) {
            if ($this->getOutcomeRowKey($outcome, $index) === $rowKey) {
                return $index;
            }
        }

        return null;
    }

    private function newOutcomeTempKey(): string
    {
        return uniqid('new_', false);
    }
}
