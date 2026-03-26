<?php

namespace App\Livewire\Programs;

use App\Models\Program;
use App\Models\ProgramEducationalObjective;
use App\Models\AuditLog;
// use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Helpers\ProgramCodeHelper;

class ManagePeos extends Component
{
    public Program $program;
    public array $peos = [];

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
                session()->flash('toast', ['message' => 'You can only manage PEOs for programs in your assigned department.', 'type' => 'warning']);
                $this->redirect(route('programs.index'));
                return;
            }
        }

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
        foreach ($peosData as $index => $peoData) {
            if (trim((string) ($peoData['peo_text'] ?? '')) === '') {
                $this->dispatch('lw-toast', type: 'warning', message: 'PEO row ' . ($index + 1) . ' is blank.');
                return;
            }
        }

        $existingIds  = $this->program->peos()->pluck('id')->toArray();
        $submittedIds = [];

        // Save or update PEOs
        foreach ($peosData as $peoData) {
            if (empty(trim($peoData['peo_text'] ?? ''))) continue;

            $peo = ProgramEducationalObjective::updateOrCreate(
                ['id' => $peoData['id'] ?? null],
                ['program_id' => $this->program->id, 'peo_text' => trim($peoData['peo_text'])]
            );

            $submittedIds[] = $peo->id;
        }

        // Delete removed PEOs
        $idsToDelete = array_diff($existingIds, $submittedIds);
        if ($idsToDelete) ProgramEducationalObjective::whereIn('id', $idsToDelete)->delete();

        // Use helper
        ProgramCodeHelper::resequencePeoCodes($this->program->id);

        $primaryDepartment = $this->program->departments()->with('college')->first();
        $collegeName = $primaryDepartment?->college?->name ?? 'N/A';
        $departmentName = $primaryDepartment?->name ?? 'N/A';

        // LOGS
        AuditLog::record(
            action: 'saved',
            module: 'PEO',
            referenceId: $this->program->id,
            description: "Saved PEOs for {$this->program->name}; college: {$collegeName}; department: {$departmentName}."
        );

        $this->loadPeos();
        session()->flash('message', 'PEOs saved and re-sequenced successfully!');
        $this->dispatch('lw-toast', type: 'success', message: 'PEOs saved.');
        $this->dispatch('peosUpdated', programId: $this->program->id);
    }

    public function render()
    {
        return view('livewire.programs.manage-peos');
    }
}
