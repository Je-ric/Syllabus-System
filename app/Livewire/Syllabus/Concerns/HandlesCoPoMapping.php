<?php

namespace App\Livewire\Syllabus\Concerns;

use App\Models\CourseOutcome;

trait HandlesCoPoMapping
{
    public $coPoMappings = [];

    public function updatedCoPoMappings()
    {
        if ($this->currentStep === 'co_po_mapping' && $this->syllabus) {
            if ($this->saveCoPoMappings()) {
                $this->markDraftSaved();
            }
        }
    }

    private function saveCoPoMappings(): bool
    {
        if (empty($this->coPoMappings)) {
            return false;
        }
        // Loop through each CO and sync its associated POs based on the current mappings
        // foreach mapping example:
        // coPoMappings = [
        //     '1' => [2 => true, 3 => true], // CO with ID 1 is mapped to PO 2 and PO 3
        //     'new_0' => [1 => true], // A new CO (not yet saved to DB) is mapped to PO 1
        // ]
        $saved = false;
        foreach ($this->coPoMappings as $coKey => $poMappings) {
            if (!is_array($poMappings)) {
                continue;
            }

            // Handle both ID keys and temporary keys (new_X)
            $coId = null;
            if (is_numeric($coKey)) {
                $coId = (int) $coKey;
            } else {
                foreach ($this->courseOutcomes as $outcome) {
                    if (($outcome['temp_key'] ?? null) === (string) $coKey && !empty($outcome['id'])) {
                        $coId = (int) $outcome['id'];
                        break;
                    }
                }
            }

            if (!$coId) {
                continue;
            }

            $co = CourseOutcome::where('id', $coId)
                ->where('syllabus_id', $this->syllabus->id)
                ->first();

            if ($co) {
                $syncData = [];
                foreach ($poMappings as $poId => $isConnected) {
                    if ($isConnected) {
                        // Keep mapping compatible whether pivot ied is required or nullable.
                        $syncData[$poId] = ['ied' => 'I'];
                    }
                }
                $co->programOutcomes()->sync($syncData);
                $saved = true;
            }
        }

        return $saved;
    }
}
