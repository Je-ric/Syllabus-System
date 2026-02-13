<?php

namespace App\Livewire\Syllabus\Concerns;

use App\Models\CourseOutcome;

trait HandlesCoPoMapping
{
    public $coPoMappings = [];

    public function updatedCoPoMappings()
    {
        if ($this->currentStep === 'co_po_mapping' && $this->syllabus) {
            $this->markStepDirty('co_po_mapping');
            if ($this->saveCoPoMappings()) {
                $this->markStepSaved('co_po_mapping');
            }
        }
    }

    private function saveCoPoMappings(): bool
    {
        if (empty($this->coPoMappings) || !$this->syllabus) {
            return false;
        }

        // Resolve temporary CO keys to database IDs once.
        $coIdByTempKey = [];
        foreach ($this->courseOutcomes as $outcome) {
            $tempKey = (string) ($outcome['temp_key'] ?? '');
            $outcomeId = (int) ($outcome['id'] ?? 0);
            if ($tempKey !== '' && $outcomeId > 0) {
                $coIdByTempKey[$tempKey] = $outcomeId;
            }
        }

        // Collect all candidate CO IDs from current mapping payload.
        $candidateCoIds = [];
        foreach ($this->coPoMappings as $coKey => $poMappings) {
            if (!is_array($poMappings)) {
                continue;
            }

            if (is_numeric($coKey)) {
                $candidateCoIds[] = (int) $coKey;
                continue;
            }

            if (isset($coIdByTempKey[(string) $coKey])) {
                $candidateCoIds[] = (int) $coIdByTempKey[(string) $coKey];
            }
        }

        $candidateCoIds = array_values(array_unique(array_filter($candidateCoIds)));
        if (empty($candidateCoIds)) {
            return false;
        }

        $existingCos = CourseOutcome::query()
            ->where('syllabus_id', $this->syllabus->id)
            ->whereIn('id', $candidateCoIds)
            ->get()
            ->keyBy('id');

        $saved = false;

        foreach ($this->coPoMappings as $coKey => $poMappings) {
            if (!is_array($poMappings)) {
                continue;
            }

            $coId = is_numeric($coKey)
                ? (int) $coKey
                : (int) ($coIdByTempKey[(string) $coKey] ?? 0);

            if ($coId <= 0 || !$existingCos->has($coId)) {
                continue;
            }

            $syncData = [];
            foreach ($poMappings as $poId => $isConnected) {
                if (!$isConnected || !is_numeric($poId)) {
                    continue;
                }
                $syncData[(int) $poId] = ['ied' => 'I'];
            }

            $existingCos[$coId]->programOutcomes()->sync($syncData);
            $saved = true;
        }

        return $saved;
    }
}
