<?php

namespace App\Livewire\Syllabus\Concerns;

use App\Models\CourseOutcome;

trait HandlesCourseOutcomes
{
    public $courseOutcomes = [];
    // each item: ['id' => ?int, 'temp_key' => string, 'co_code' => string, 'description' => string]

    public ?string $coAddError = null;

    private function saveCourseOutcomes(): bool
    {
        if (!$this->syllabus) {
            return false;
        }

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

        foreach ($this->courseOutcomes as $index => $outcome) {
            $description = trim((string) ($outcome['description'] ?? ''));
            if ($description === '') {
                continue;
            }

            $coCode = $this->courseOutcomes[$index]['co_code'];

            if (!empty($outcome['id']) && $existingCos->has((int) $outcome['id'])) {
                $co = $existingCos->get((int) $outcome['id']);
                $co->update([
                    'co_code' => $coCode,
                    'description' => $description,
                ]);
                $saved = true;
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

        return $saved;
    }

    private function newOutcomeTempKey(): string
    {
        return uniqid('new_', false);
    }
}
