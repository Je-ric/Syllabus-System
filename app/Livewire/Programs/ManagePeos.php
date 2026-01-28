<?php

namespace App\Livewire\Programs;

use App\Models\Program;
use App\Models\ProgramEducationalObjective;
use Livewire\Component;

class ManagePeos extends Component
{
    public Program $program;
    public array $peos = [];

    public function mount(Program $program): void
    {
        $this->program = $program;
        $this->loadPeos();
    }

    private function loadPeos(): void
    {
        $dbPeos = $this->program->peos()
            ->orderBy('id')
            ->get();

        $this->peos = [];

        foreach ($dbPeos as $peo) {
            $this->peos[] = [
                'id' => $peo->id,
                'peo_code' => $peo->peo_code,
                'peo_text' => $peo->peo_text,
            ];
        }
    }

    public function savePeos(array $peosData): void
    {
        $existingIds  = $this->program->peos()->pluck('id')->toArray();
        $submittedIds = [];

        foreach ($peosData as $peoData) {
            if (empty(trim($peoData['peo_text'] ?? ''))) {
                continue;
            }

            $peo = ProgramEducationalObjective::updateOrCreate(
                ['id' => $peoData['id'] ?? null],
                [
                    'program_id' => $this->program->id,
                    'peo_text'   => trim($peoData['peo_text']),
                ]
            );

            $submittedIds[] = $peo->id;
        }

        // Delete removed PEOs
        $idsToDelete = array_diff($existingIds, $submittedIds);
        if ($idsToDelete) {
            ProgramEducationalObjective::whereIn('id', $idsToDelete)->delete();
        }

        // Re-sequence PEO codes
        $peos = $this->program->peos()
            ->orderBy('id')
            ->get();

        foreach ($peos as $index => $peo) {
            $peo->update([
                'peo_code' => 'PEO' . ($index + 1),
            ]);
        }

        $this->loadPeos();

        session()->flash('message', 'PEOs saved and re-sequenced successfully!');
        $this->dispatch('peosUpdated', programId: $this->program->id);
    }


    public function render()
    {
        return view('livewire.programs.manage-peos');
    }
}
