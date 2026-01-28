<?php

namespace App\Livewire\Programs;

use App\Models\Program;
use App\Models\ProgramOutcome;
use Livewire\Component;
use Livewire\Attributes\On;

class ManagePos extends Component
{
    public Program $program;
    public array $pos = [];
    public array $peos = [];
    public array $mapping = [];

    public function mount(Program $program): void
    {
        $this->program = $program;
        $this->loadPos();
        $this->loadPeos();
        $this->loadMapping();
    }

    private function loadPos(): void
    {
        $dbPos = $this->program->outcomes()
            ->orderBy('id')
            ->get();

        $this->pos = [];
        foreach ($dbPos as $po) {
            $this->pos[] = [
                'id' => $po->id,
                'po_code' => $po->po_code,
                'po_text' => $po->po_text,
            ];
        }
    }

    private function loadPeos(): void
    {
        $dbPeos = $this->program->peos()
            ->orderBy('peo_code')
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

    // Load mapping (which PEOs are linked to which POs)
    private function loadMapping(): void
    {
        $dbPos = $this->program->outcomes()
            ->with('peos')
            ->get();

        $this->mapping = [];
        foreach ($dbPos as $po) {
            $peoIds = [];
            foreach ($po->peos as $peo) {
                $peoIds[] = $peo->id;
            }
            $this->mapping[$po->id] = $peoIds;
        }
    }

    public function savePos(array $posData, array $mappingData): void
    {
        $existingIds  = $this->program->outcomes()->pluck('id')->toArray();
        $submittedIds = [];

        // 1. Update / Create
        foreach ($posData as $poData) {
            if (empty(trim($poData['po_text'] ?? ''))) {
                continue;
            }

            $po = ProgramOutcome::updateOrCreate(
                ['id' => $poData['id'] ?? null],
                [
                    'program_id' => $this->program->id,
                    'po_text'    => trim($poData['po_text']),
                ]
            );

            // Sync PEO mappings
            if (isset($mappingData[$po->id])) {
                $po->peos()->sync($mappingData[$po->id]);
            }

            $submittedIds[] = $po->id;
        }

        // 2. Delete removed POs
        $idsToDelete = array_diff($existingIds, $submittedIds);
        if ($idsToDelete) {
            ProgramOutcome::whereIn('id', $idsToDelete)->delete();
        }

        // 3. Resequence PO codes (a, b, c...)
        $pos = $this->program->outcomes()
            ->orderBy('id') // IMPORTANT
            ->get();

        foreach ($pos as $index => $po) {
            $po->update([
                'po_code' => $this->numberToLetter($index + 1),
            ]);
        }

        // 4. Reload state
        $this->loadPos();
        $this->loadMapping();

        session()->flash('message', 'POs saved and resequenced successfully!');
    }

    // Convert number to letter (1=a, 2=b, etc.)
    private function numberToLetter(int $number): string
    {
        $letter = '';
        while ($number > 0) {
            $remainder = ($number - 1) % 26;
            $letter = chr(97 + $remainder) . $letter;
            $number = floor(($number - 1) / 26);
        }
        return $letter;
    }

    // Listen for PEO updates
    #[On('peosUpdated')]
    public function refreshPeos(int $programId): void
    {
        // Only refresh if it's for this program
        if ($this->program->id != $programId) {
            return;
        }

        $this->loadPeos();

        // Clean up mappings - remove any PEO IDs that no longer exist
        $validPeoIds = [];
        foreach ($this->peos as $peo) {
            $validPeoIds[] = $peo['id'];
        }

        // Check each PO's mappings
        foreach ($this->mapping as $poId => $peoIds) {
            $cleanedPeoIds = [];
            foreach ($peoIds as $peoId) {
                if (in_array($peoId, $validPeoIds)) {
                    $cleanedPeoIds[] = $peoId;
                }
            }
            $this->mapping[$poId] = $cleanedPeoIds;
        }
    }

    public function render()
    {
        return view('livewire.programs.manage-pos');
    }
}
