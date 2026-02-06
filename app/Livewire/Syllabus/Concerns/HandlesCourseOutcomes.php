<?php

namespace App\Livewire\Syllabus\Concerns;

use App\Models\CourseOutcome;

trait HandlesCourseOutcomes
{
    public $courseOutcomes = [];
    public ?string $coAddError = null;

    public function addCourseOutcome()
    {
        foreach ($this->courseOutcomes as $outcome) {
            if (empty(trim($outcome['description'] ?? ''))) {
                $this->coAddError = 'Fill the blank before adding.';
                return;
            }
        }

        $this->coAddError = null;
        $nextNumber = count($this->courseOutcomes) + 1;
        $this->courseOutcomes[] = [
            'id' => null,
            'co_code' => 'CO' . $nextNumber,
            'description' => '',
        ];

        // Initialize CO-PO mapping for new outcome
        $index = count($this->courseOutcomes) - 1;
        $this->coPoMappings['new_' . $index] = [];
    }

    public function removeCourseOutcome($index)
    {
        $oldMappings = $this->coPoMappings;

        // Remove CO-PO mappings for this outcome
        if (isset($this->courseOutcomes[$index]['id'])) {
            $coId = $this->courseOutcomes[$index]['id'];
            unset($oldMappings[$coId]);
        }
        unset($oldMappings['new_' . $index]);

        // Delete from database if it exists
        if (isset($this->courseOutcomes[$index]['id']) && $this->courseOutcomes[$index]['id']) {
            CourseOutcome::where('id', $this->courseOutcomes[$index]['id'])->delete();
        }

        unset($this->courseOutcomes[$index]);
        $this->courseOutcomes = array_values($this->courseOutcomes);

        // Resequence codes and update mapping keys
        $newMappings = [];
        foreach ($this->courseOutcomes as $i => $outcome) {
            $this->courseOutcomes[$i]['co_code'] = 'CO' . ($i + 1);

            if (isset($outcome['id']) && $outcome['id']) {
                CourseOutcome::where('id', $outcome['id'])
                    ->where('syllabus_id', $this->syllabus->id)
                    ->update(['co_code' => $this->courseOutcomes[$i]['co_code']]);
            }

            // Update mapping keys
            if (isset($outcome['id']) && $outcome['id']) {
                if (isset($oldMappings[$outcome['id']])) {
                    $newMappings[$outcome['id']] = $oldMappings[$outcome['id']];
                }
            } else {
                if (isset($oldMappings['new_' . $i])) {
                    $newMappings['new_' . $i] = $oldMappings['new_' . $i];
                }
            }
        }
        $this->coPoMappings = $newMappings;
    }

    private function saveCourseOutcomes(): bool
    {
        $oldMappings = $this->coPoMappings;
        $tempKeyToId = [];
        $createdNew = false;
        $saved = false;

        // Save/update each outcome
        foreach ($this->courseOutcomes as $index => $outcome) {
            if (empty(trim($outcome['description'] ?? ''))) {
                continue;
            }

            if (isset($outcome['id']) && $outcome['id']) {
                // Update existing
                $co = CourseOutcome::where('id', $outcome['id'])
                    ->where('syllabus_id', $this->syllabus->id)
                    ->first();
                if ($co) {
                    $co->update([
                        'co_code' => $outcome['co_code'],
                        'description' => $outcome['description'],
                    ]);
                    $saved = true;
                }
            } else {
                // Create new
                $newCo = CourseOutcome::create([
                    'syllabus_id' => $this->syllabus->id,
                    'co_code' => $outcome['co_code'],
                    'description' => $outcome['description'],
                ]);
                $this->courseOutcomes[$index]['id'] = $newCo->id;
                $tempKeyToId['new_' . $index] = $newCo->id;
                $createdNew = true;
                $saved = true;
            }
        }

        if ($createdNew) {
            $newMappings = $oldMappings;
            foreach ($tempKeyToId as $tempKey => $newId) {
                if (isset($newMappings[$tempKey])) {
                    $newMappings[$newId] = $newMappings[$tempKey];
                    unset($newMappings[$tempKey]);
                } else {
                    $newMappings[$newId] = [];
                }
            }
            $this->coPoMappings = $newMappings;
        }

        return $saved;
    }
}
