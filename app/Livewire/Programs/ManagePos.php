<?php

namespace App\Livewire\Programs;

use App\Models\Program;
use App\Models\ProgramOutcome;
use App\Models\AuditLog;
// use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use App\Helpers\ProgramCodeHelper;

class ManagePos extends Component
{
    public Program $program;
    public array $pos = [];
    public array $peos = [];
    public array $mapping = [];

    public function mount(Program $program): void
    {
        /** @var \App\Models\User $user */
        // $user = auth()->user();
        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            $assignment = $user->getPrimaryDepartmentAssignment();
            $allowed = $assignment && Program::whereHas('departments', fn($q) =>
                $q->where('department_id', $assignment->department_id)
            )->where('id', $program->id)->exists();
            if (!$allowed) {
                session()->flash('toast', ['message' => 'You can only manage POs for programs in your assigned department.', 'type' => 'warning']);
                $this->redirect(route('programs.index'));
                return;
            }
        }

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
        foreach ($posData as $index => $poData) {
            if (trim((string) ($poData['po_text'] ?? '')) === '') {
                $this->dispatch('lw-toast', type: 'warning', message: 'PO row ' . ($index + 1) . ' is blank.');
                return;
            }
        }

        $existingIds  = $this->program->outcomes()->pluck('id')->toArray();
        $submittedIds = [];

        // Save or update POs
        foreach ($posData as $poData) {
            if (empty(trim($poData['po_text'] ?? ''))) continue;

            $po = ProgramOutcome::updateOrCreate(
                ['id' => $poData['id'] ?? null],
                ['program_id' => $this->program->id, 'po_text' => trim($poData['po_text'])]
            );

            if (isset($mappingData[$po->id])) $po->peos()->sync($mappingData[$po->id]);

            $submittedIds[] = $po->id;
        }

        // Delete removed POs
        $idsToDelete = array_diff($existingIds, $submittedIds);
        if ($idsToDelete) ProgramOutcome::whereIn('id', $idsToDelete)->delete();

        // Use helper
        ProgramCodeHelper::resequencePoCodes($this->program->id);

        $primaryDepartment = $this->program->departments()->with('college')->first();
        $collegeName = $primaryDepartment?->college?->name ?? 'N/A';
        $departmentName = $primaryDepartment?->name ?? 'N/A';

        // LOGS
        AuditLog::record(
            action: 'saved',
            module: 'PO',
            referenceId: $this->program->id,
            description: "Saved POs for {$this->program->name}; college: {$collegeName}; department: {$departmentName}."
        );

        $this->loadPos();
        $this->loadMapping();

        session()->flash('message', 'POs saved and re-sequenced successfully!');
        $this->dispatch('lw-toast', type: 'success', message: 'POs saved.');
    }

    public function toggleMapping(int $poId, int $peoId, bool $checked): void
    {
        $po = ProgramOutcome::where('program_id', $this->program->id)->find($poId);
        if (!$po) {
            $this->dispatch('lw-toast', type: 'warning', message: 'Save PO row first before mapping.');
            return;
        }

        $validPeoIds = $this->program->peos()->pluck('id')->all();
        if (!in_array($peoId, $validPeoIds, true)) {
            return;
        }

        if ($checked) {
            $po->peos()->syncWithoutDetaching([$peoId]);
        } else {
            $po->peos()->detach($peoId);
        }

        AuditLog::record(
            action: $checked ? 'mapped' : 'unmapped',
            module: 'PO-PEO Mapping',
            referenceId: $po->id,
            description: ($checked ? 'Mapped' : 'Unmapped') . " PEO #{$peoId} " . ($checked ? 'to' : 'from') . " PO #{$po->id} in {$this->program->name}."
        );

        $this->loadMapping();
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
