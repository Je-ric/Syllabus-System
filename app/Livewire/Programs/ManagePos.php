<?php

namespace App\Livewire\Programs;

use App\Models\Program;
use App\Models\ProgramOutcome;
use App\Models\AuditLog;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            ->orderByRaw("po_code IS NULL ASC")
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
        try {
            DB::transaction(function () use ($posData, $mappingData): void {
                foreach ($posData as $index => $row) {
                    if (trim((string) ($row['po_text'] ?? '')) === '') {
                        throw new \RuntimeException('PO row ' . ($index + 1) . ' is blank.');
                    }
                }

                // Delete POs removed in Alpine.
                $submittedIds = array_values(array_filter(array_column($posData, 'id')));
                $existingIds  = $this->program->outcomes()->pluck('id')->toArray();
                $toDelete     = array_diff($existingIds, $submittedIds);
                if ($toDelete) ProgramOutcome::whereIn('id', $toDelete)->delete();

                // Update text on existing rows (the submitted text follows the dragged row order).
                foreach ($posData as $row) {
                    if (empty($row['id'])) continue;
                    ProgramOutcome::where('id', $row['id'])
                        ->where('program_id', $this->program->id)
                        ->update(['po_text' => trim($row['po_text'])]);
                }

                // Insert new rows.
                $newIds = [];
                foreach ($posData as $row) {
                    if (!empty($row['id'])) continue;
                    $po = ProgramOutcome::create([
                        'program_id' => $this->program->id,
                        'po_text'    => trim($row['po_text']),
                        'po_code'    => null,
                    ]);
                    $newIds[] = $po->id;
                }

                // Resequence codes from the saved order.
                $orderedIds = array_merge($submittedIds, $newIds);
                ProgramCodeHelper::resequencePoCodesOrdered($this->program->id, $orderedIds);

                // Sync PEO mappings for existing POs after the save succeeds.
                foreach ($posData as $row) {
                    if (empty($row['id'])) continue;
                    $po = ProgramOutcome::find($row['id']);
                    if ($po && array_key_exists($po->id, $mappingData)) {
                        $po->peos()->sync($mappingData[$po->id]);
                    }
                }
            });
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('lw-toast', type: 'error', message: 'Failed to save POs. Please try again.');
            return;
        }

        $dept = $this->program->departments()->with('college')->first();
        $collegeName = $dept?->college?->name ?? 'N/A';
        $departmentName = $dept?->name ?? 'N/A';
        AuditLog::record(
            action: 'saved', module: 'PO', referenceId: $this->program->id,
            description: "Saved POs for {$this->program->name}; college: {$collegeName}; department: {$departmentName}."
        );

        $this->loadPos();
        $this->loadMapping();
        $this->dispatch('lw-toast', type: 'success', message: 'POs saved.');
        $this->dispatch('pos-saved', pos: $this->pos, mapping: $this->mapping);
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
