<?php

namespace App\Livewire\Syllabus\Concerns;

use App\Models\CourseOutcome;

trait HandlesCourseOutcomes
{
    public $courseOutcomes = [];
    // an array of arrays, each inner array has keys: id, co_code, description

    public ?string $coAddError = null;
    public function addCourseOutcome()
    {

    // foreach course outcome, if description is empty, set coAddError to 'Fill the blank before adding.' and return
        foreach ($this->courseOutcomes as $outcome) {
            if (empty(trim($outcome['description'] ?? ''))) {
                $this->coAddError = 'Fill the blank before adding.';
                return;
            }
        }

        // if all descriptions are filled, clear the error and add a new course outcome with empty description and code CO{n+1}
        $this->coAddError = null;
        $nextNumber = count($this->courseOutcomes) + 1;
        $this->courseOutcomes[] = [
            'id' => null,
            'co_code' => 'CO' . $nextNumber,
            'description' => '',
        ];

        // Initialize CO-PO mapping for new outcome
        // examples:
        // if we have 2 existing outcomes, and we add a new one,
        // the new outcome will have a temporary key 'new_2'
        // (since index starts at 0), and we will initialize an empty array for it in the coPoMappings
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

        // Remove from the courseOutcomes array
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
