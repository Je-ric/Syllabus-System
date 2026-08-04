<?php

namespace App\Services\Syllabus\Weeks;

// Pure in-memory array mutations for the references and materials
// sub-arrays inside $weekInputs.
//
// Zero database interaction — DB writes happen in WeekContentService
// when the week is explicitly saved.
//
// Every method accepts the full $weekInputs array, mutates it, and
// returns the updated array so the Livewire component can assign:
//   $this->weekInputs = $this->weekResourceService->addReference(…);
class WeekResourceService
{
    // ── References ────────────────────────────────────────────────────────────

    // Append a blank reference row.
    public function addReference(array $weekInputs, int $weekNo, array $lockedWeeks): array
    {
        if (isset($lockedWeeks[$weekNo])) {
            return $weekInputs;
        }

        $weekInputs['w' . $weekNo]['references'][] = ['text' => ''];

        return $weekInputs;
    }

    // Remove a reference row by index.
    // Always keeps at least one blank row so the input field renders.
    public function removeReference(array $weekInputs, int $weekNo, int $index, array $lockedWeeks): array
    {
        if (isset($lockedWeeks[$weekNo])) {
            return $weekInputs;
        }

        $key = 'w' . $weekNo;

        if (! isset($weekInputs[$key]['references'][$index])) {
            return $weekInputs;
        }

        array_splice($weekInputs[$key]['references'], $index, 1);

        if (empty($weekInputs[$key]['references'])) {
            $weekInputs[$key]['references'] = [['text' => '']];
        }

        return $weekInputs;
    }

    // ── Materials ─────────────────────────────────────────────────────────────

    // Append a blank material row.
    public function addMaterial(array $weekInputs, int $weekNo, array $lockedWeeks): array
    {
        if (isset($lockedWeeks[$weekNo])) {
            return $weekInputs;
        }

        $weekInputs['w' . $weekNo]['materials'][] = ['name' => '', 'url' => ''];

        return $weekInputs;
    }

    // Remove a material row by index.
    // Always keeps at least one blank row so the input pair renders.
    public function removeMaterial(array $weekInputs, int $weekNo, int $index, array $lockedWeeks): array
    {
        if (isset($lockedWeeks[$weekNo])) {
            return $weekInputs;
        }

        $key = 'w' . $weekNo;

        if (! isset($weekInputs[$key]['materials'][$index])) {
            return $weekInputs;
        }

        array_splice($weekInputs[$key]['materials'], $index, 1);

        if (empty($weekInputs[$key]['materials'])) {
            $weekInputs[$key]['materials'] = [['name' => '', 'url' => '']];
        }

        return $weekInputs;
    }
}
