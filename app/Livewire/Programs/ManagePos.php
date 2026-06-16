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
        $this->pos = $this->program->outcomes()
            ->orderBy('po_code')
            ->get(['id', 'po_code', 'po_text'])
            ->toArray();
    }

    private function loadPeos(): void
    {
        $this->peos = $this->program->peos()
            ->orderBy('peo_code')
            ->get(['id', 'peo_code', 'peo_text'])
            ->toArray();
    }

    private function loadMapping(): void
    {
        $this->mapping = $this->program->outcomes()->with('peos')->get()
            ->mapWithKeys(fn($po) => [$po->id => $po->peos->pluck('id')->all()])
            ->all();
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


    #[On('peosUpdated')]
    public function refreshPeos(int $programId): void
    {
        if ($this->program->id != $programId) return;

        $this->loadPeos();

        $validIds = array_column($this->peos, 'id');
        foreach ($this->mapping as $poId => $peoIds) {
            $this->mapping[$poId] = array_values(array_intersect($peoIds, $validIds));
        }
    }

    public function render()
    {
        return view('livewire.programs.manage-pos');
    }
}
