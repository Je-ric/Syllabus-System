<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ProgramEducationalObjective;
use App\Models\ProgramOutcome;
use App\Models\AuditLog;
use App\Helpers\ProgramCodeHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProgramController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $program = null;

        if (request('program_id')) {
            $program = Program::find(request('program_id'));
        }

        $noAssignment = !$user->hasRole('admin')
            && $user->hasRole('chair')
            && !$user->getPrimaryDepartmentAssignment();

        return view('Programs.index', compact('program', 'noAssignment'));
    }

    public function show(Program $program)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $noAssignment = !$user->hasRole('admin')
            && $user->hasRole('chair')
            && !$user->getPrimaryDepartmentAssignment();

        return view('Programs.index', compact('program', 'noAssignment'));
    }


    public function deletePeo(int $peoId)
    {
        DB::beginTransaction();

        try {
            $peo = ProgramEducationalObjective::findOrFail($peoId);
            $programId = $peo->program_id;

            /** @var \App\Models\User $user */
            // $user = auth()->user();
            $user = Auth::user();
            if (!$user->hasRole('admin')) {
                if ($redirect = $this->abortIfNotAssignedToProgram($user, $programId)) {
                    return $redirect;
                }
            }

            $programName = $peo->program?->name ?? 'Unknown Program';
            $peoCode = $peo->peo_code;

            // Detach PO mappings before deleting to avoid orphaned program_outcome_peo rows
            $peo->outcomes()->detach();

            $peo->delete();

            // Use helper instead of repeating resequence logic
            ProgramCodeHelper::resequencePeoCodes($programId);

            // LOGS
            AuditLog::record(
                action: 'deleted',
                module: 'PEO',
                referenceId: $peoId,
                description: "Deleted {$peoCode} from {$programName} and re-sequenced PEO codes."
            );

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()->with('toast', [
                'message' => 'Failed to delete PEO. Please try again.',
                'type'    => 'error',
            ]);
        }

        return redirect()->route('programs.show', ['program' => $programId])
            ->with('toast', [
                'message' => 'PEO deleted and codes re-sequenced!',
                'type'    => 'success',
            ]);
    }

    public function deletePo(int $poId)
    {
        DB::beginTransaction();

        try {
            $po = ProgramOutcome::findOrFail($poId);
            $programId = $po->program_id;

            /** @var \App\Models\User $user */
            // $user = auth()->user();
            $user = Auth::user();
            if (!$user->hasRole('admin')) {
                if ($redirect = $this->abortIfNotAssignedToProgram($user, $programId)) {
                    return $redirect;
                }
            }

            $programName = $po->program?->name ?? 'Unknown Program';
            $poCode = $po->po_code;

            // Block deletion if any syllabus exists for a course that maps this PO
            $syllabusCount = \App\Models\Syllabus::whereIn(
                'course_id',
                \App\Models\Course::whereHas('programOutcomes', fn($q) => $q->where('program_outcomes.id', $po->id))
                    ->select('id')
            )->count();

            if ($syllabusCount > 0) {
                DB::rollBack();
                return redirect()->back()->with('toast', [
                    'message' => "Cannot delete {$poCode}: it is mapped to a course that has {$syllabusCount} existing syllabus/syllabi. Remove the PO mapping from the course first.",
                    'type'    => 'error',
                ]);
            }

            // Detach from PEOs and course curriculum maps before deleting
            $po->peos()->detach();
            $po->courses()->detach();

            $po->delete();

            // Use helper instead of repeating resequence logic
            ProgramCodeHelper::resequencePoCodes($programId);

            // LOGS
            AuditLog::record(
                action: 'deleted',
                module: 'PO',
                referenceId: $poId,
                description: "Deleted {$poCode} from {$programName} and re-sequenced PO codes."
            );

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()->with('toast', [
                'message' => 'Failed to delete PO. Please try again.',
                'type'    => 'error',
            ]);
        }

        return redirect()->route('programs.show', ['program' => $programId])
            ->with('toast', [
                'message' => 'PO deleted and codes re-sequenced!',
                'type'    => 'success',
            ]);
    }

    private function abortIfNotAssignedToProgram($user, int $programId): ?RedirectResponse
    {
        $assignment = $user->getPrimaryDepartmentAssignment();
        $allowed = $assignment && Program::whereHas('departments', fn($q) =>
            $q->where('department_id', $assignment->department_id)
        )->where('id', $programId)->exists();

        if (!$allowed) {
            return redirect()->route('programs.index')
                ->with('toast', ['message' => 'You can only manage PEOs/POs for programs in your assigned department.', 'type' => 'warning']);
        }

        return null;
    }
}
