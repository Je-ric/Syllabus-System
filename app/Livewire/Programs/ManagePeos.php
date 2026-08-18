<?php

namespace App\Livewire\Programs;

use App\Models\Program;
use App\Models\ProgramEducationalObjective;
use App\Models\AuditLog;
use App\Helpers\SecurityValidator;
// use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $this->peos = $this->program->peos()
            ->orderBy('peo_code')
            ->get(['id', 'peo_code', 'peo_text'])
            ->toArray();
    }

    public function savePeos(array $peosData): void
    {
        try {
            DB::transaction(function () use ($peosData): void {
                foreach ($peosData as $index => $row) {
                    $text = trim((string) ($row['peo_text'] ?? ''));
                    if ($text === '') {
                        throw new \RuntimeException('PEO row ' . ($index + 1) . ' is blank.');
                    }
                    if (SecurityValidator::containsAnyInjection($text)) {
                        $type = SecurityValidator::getInjectionType($text);
                        throw new \RuntimeException('PEO row ' . ($index + 1) . " contains {$type} injection and is not allowed.");
                    }
                }

                // Delete PEOs that were removed in Alpine (had an id but absent from submission)
                $submittedIds = array_values(array_filter(array_column($peosData, 'id')));
                $existingIds  = $this->program->peos()->pluck('id')->toArray();
                $toDelete     = array_diff($existingIds, $submittedIds);
                if ($toDelete) ProgramEducationalObjective::whereIn('id', $toDelete)->delete();

                // Update text on existing rows (the submitted text is already in the visual order)
                foreach ($peosData as $row) {
                    if (empty($row['id'])) continue;
                    ProgramEducationalObjective::where('id', $row['id'])
                        ->where('program_id', $this->program->id)
                        ->update(['peo_text' => trim($row['peo_text'])]);
                }

                // Insert new rows (no id)
                $newIds = [];
                foreach ($peosData as $row) {
                    if (!empty($row['id'])) continue;
                    $peo = ProgramEducationalObjective::create([
                        'program_id' => $this->program->id,
                        'peo_text'   => trim($row['peo_text']),
                        'peo_code'   => null,
                    ]);
                    $newIds[] = $peo->id;
                }

                // Build final ordered ID list: existing in submitted order, then newly inserted
                $orderedIds = array_merge($submittedIds, $newIds);

                // Resequence codes from the saved order.
                ProgramCodeHelper::resequencePeoCodesOrdered($this->program->id, $orderedIds);
            });
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('lw-toast', type: 'error', message: $e->getMessage());
            return;
        }

        $dept = $this->program->departments()->with('college')->first();
        $collegeName = $dept?->college?->name ?? 'N/A';
        $departmentName = $dept?->name ?? 'N/A';
        AuditLog::record(
            action: 'saved', module: 'PEO', referenceId: $this->program->id,
            description: "Saved PEOs for {$this->program->name}; college: {$collegeName}; department: {$departmentName}."
        );

        $this->loadPeos();
        $this->dispatch('lw-toast', type: 'success', message: 'PEOs saved.');
        $this->dispatch('peosUpdated', programId: $this->program->id);
        $this->dispatch('peos-saved', peos: $this->peos);
    }

    public function render()
    {
        return view('livewire.programs.manage-peos');
    }
}
