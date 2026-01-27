<?php

namespace App\Livewire\Programs;

use App\Models\Program;
use App\Models\ProgramEducationalObjective;
use Livewire\Component;

class ManagePeos extends Component
{
    public Program $program;

    /** @var array<int,array{id:int|null,peo_code:string,peo_text:string}> */
    public array $peos = [];

    public function mount(Program $program): void
    {
        $this->program = $program;

        // Load existing PEOs
        $this->peos = $program->peos()
            ->orderBy('peo_code')
            ->get(['id', 'peo_code', 'peo_text'])
            ->map(fn($peo) => $peo->toArray()) // Convert to array
            ->all();
    }


    public function savePeos(array $peosData): void
    {
        // Track existing DB ids and codes for this program
        $existingIds = $this->program->peos()->pluck('id')->toArray();
        $existingCodes = $this->program->peos()->pluck('peo_code')->toArray();

        $submittedIds = [];

        foreach ($peosData as $index => $peoData) {
            // Skip empty PEOs in save (do not persist blanks)
            if (!isset($peoData['peo_text']) || trim($peoData['peo_text']) === '') {
                continue;
            }

            // Only assign new PEO code if this is a new PEO (no code provided)
            $peoCode = ($peoData['peo_code'] ?? '') !== ''
                ? $peoData['peo_code']
                : $this->generateNextPeoCode($existingCodes);

            $peo = ProgramEducationalObjective::updateOrCreate(
                ['id' => $peoData['id'] ?? null],
                [
                    'program_id' => $this->program->id,
                    'peo_code'   => $peoCode,
                    'peo_text'   => $peoData['peo_text'],
                ]
            );

            // Update local state
            $peosData[$index]['id'] = $peo->id;
            $peosData[$index]['peo_code'] = $peoCode;

            // Track assigned codes and ids to prevent duplicates and compute deletions
            $existingCodes[] = $peoCode;
            $submittedIds[] = $peo->id;
        }

        // Delete any PEOs that exist in DB but were removed from the submitted list
        $idsToDelete = array_diff($existingIds, $submittedIds);
        if (!empty($idsToDelete)) {
            ProgramEducationalObjective::whereIn('id', $idsToDelete)->delete();
        }

        // Keep component state clean (remove blanks, reindex)
        $this->peos = array_values(array_filter($peosData, function ($row) {
            return isset($row['peo_text']) && trim($row['peo_text']) !== '';
        }));

        session()->flash('message', 'PEOs saved successfully!');
    }

    private function generateNextPeoCode(array $existingCodes): string
    {
        $i = 1;
        do {
            $code = 'PEO' . $i;
            $i++;
        } while (in_array($code, $existingCodes));

        return $code;
    }

    public function deletePeo(int $id): void
    {
        $peo = ProgramEducationalObjective::find($id);

        if ($peo) {
            $peo->delete();

            // Remove from local state to keep UI in sync
            $this->peos = array_values(array_filter($this->peos, function ($row) use ($id) {
                return ($row['id'] ?? null) !== $id;
            }));

            session()->flash('message', 'PEO deleted successfully!');
        }
    }

    public function render()
    {
        return view('livewire.programs.manage-peos');
    }
}
