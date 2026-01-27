<?php

namespace App\Livewire\Programs;

use App\Models\Program;
use App\Models\ProgramOutcome;
use Livewire\Component;

class ManagePos extends Component
{
    public Program $program;

    /** @var array<int,array{id:int|null,po_code:string,po_text:string}> */
    public array $pos = [];

    /** @var array<int,array{id:int,peo_code:string,peo_text:string}> */
    public array $peos = [];

    /** @var array<int,array<int,int>> Mapping: po_id => [peo_id,...] */
    public array $mapping = [];

    public function mount(Program $program): void
    {
        $this->program = $program;

        // Load existing POs
        $this->pos = $program->outcomes()
            ->orderBy('po_code')
            ->get(['id', 'po_code', 'po_text'])
            ->map(fn ($po) => $po->toArray())
            ->all();

        // Load PEOs for checkbox rendering
        $this->peos = $program->peos()
            ->orderBy('peo_code')
            ->get(['id', 'peo_code', 'peo_text'])
            ->map(fn ($peo) => $peo->toArray())
            ->all();

        // Load existing mappings (PO -> selected PEO ids)
        foreach ($program->outcomes()->with('peos:id')->get(['id']) as $po) {
            /** @var \App\Models\ProgramOutcome $po */
            $this->mapping[$po->id] = $po->peos->pluck('id')->all();
        }
    }

    public function savePos(array $posData, array $mappingData): void
    {
        // Track existing DB ids and codes
        $existingIds = $this->program->outcomes()->pluck('id')->toArray();
        $existingCodes = $this->program->outcomes()->pluck('po_code')->toArray();

        $submittedIds = [];

        foreach ($posData as $index => $poData) {
            // Skip empty POs
            if (!isset($poData['po_text']) || trim($poData['po_text']) === '') {
                continue;
            }

            // Assign code if missing (use next available letter)
            $poCode = ($poData['po_code'] ?? '') !== ''
                ? $poData['po_code']
                : $this->generateNextPoCode($existingCodes);

            $po = ProgramOutcome::updateOrCreate(
                ['id' => $poData['id'] ?? null],
                [
                    'program_id' => $this->program->id,
                    'po_code'    => $poCode,
                    'po_text'    => $poData['po_text'],
                ]
            );

            // Update local state
            $posData[$index]['id'] = $po->id;
            $posData[$index]['po_code'] = $poCode;

            $existingCodes[] = $poCode;
            $submittedIds[] = $po->id;

            // Sync mappings for this PO
            $selectedPeoIds = $mappingData[$po->id] ?? [];
            $po->peos()->sync($selectedPeoIds);
        }

        // Delete removed POs
        $idsToDelete = array_diff($existingIds, $submittedIds);
        if (!empty($idsToDelete)) {
            ProgramOutcome::whereIn('id', $idsToDelete)->delete();
        }

        // Clean state (remove blanks)
        $this->pos = array_values(array_filter($posData, function ($row) {
            return isset($row['po_text']) && trim($row['po_text']) !== '';
        }));

        // Keep mapping updated
        $this->mapping = $mappingData;

        session()->flash('message', 'POs and mappings saved successfully!');
    }

    private function generateNextPoCode(array $existingCodes): string
    {
        // Codes expected as letters a, b, c ... (lowercase)
        $i = 0;
        while (true) {
            $code = $this->intToLetters($i);
            if (!in_array($code, $existingCodes, true)) {
                return $code;
            }
            $i++;
        }
    }

    private function intToLetters(int $n): string
    {
        // 0 -> 'a', 1 -> 'b', ..., 25 -> 'z', 26 -> 'aa', etc.
        $s = '';
        $n++; // shift to 1-based
        while ($n > 0) {
            $n--; // 0-25
            $s = chr(ord('a') + ($n % 26)) . $s;
            $n = intdiv($n, 26);
        }
        return $s;
    }

    public function deletePo(int $id): void
    {
        $po = ProgramOutcome::find($id);
        if ($po) {
            $po->delete(); // cascades pivot
            // Update local state
            $this->pos = array_values(array_filter($this->pos, fn ($row) => ($row['id'] ?? null) !== $id));
            unset($this->mapping[$id]);
            session()->flash('message', 'PO deleted successfully!');
        }
    }

    public function render()
    {
        return view('livewire.programs.manage-pos');
    }
}
