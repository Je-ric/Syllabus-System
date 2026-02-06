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

        $saved = false;
        foreach ($this->coPoMappings as $coKey => $poMappings) {
            if (!is_array($poMappings)) {
                continue;
            }

            // Handle both ID keys and temporary keys (new_X)
            $coId = null;
            if (is_numeric($coKey)) {
                $coId = $coKey;
            } else {
                // Extract index from 'new_X' format
                if (str_starts_with($coKey, 'new_')) {
                    $index = (int) str_replace('new_', '', $coKey);
                    if (isset($this->courseOutcomes[$index]['id'])) {
                        $coId = $this->courseOutcomes[$index]['id'];
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
                        $syncData[$poId] = [];  // No IED needed, just connect them
                    }
                }
                $co->programOutcomes()->sync($syncData);
                $saved = true;
            }
        }

        return $saved;
    }
}
