<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ProgramEducationalObjective;
use App\Models\ProgramOutcome;
use App\Models\AuditLog;
use App\Helpers\ProgramCodeHelper;
use Illuminate\Support\Facades\DB;
// use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index()
    {
        $program = null;

        // If a program ID is passed via query parameter, load it
        if (request('program_id')) {
            $program = Program::find(request('program_id'));
        }

        return view('Programs.index', compact('program'));
    }

    public function show(Program $program)
    {

        return view('Programs.index', compact('program'));
    }


    public function deletePeo(int $peoId)
    {
        DB::beginTransaction();

        try {
            $peo = ProgramEducationalObjective::findOrFail($peoId);
            $programId = $peo->program_id;
            $programName = $peo->program?->name ?? 'Unknown Program';
            $peoCode = $peo->peo_code;

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

            return redirect()->back()->withErrors([
                'error' => 'Failed to delete PEO. Please try again.',
            ]);
        }

        return redirect()->route('programs.show', ['program' => $programId])
            ->with('toast', [
                'message' => 'PEO deleted and codes re-sequenced!',
                'type' => 'success'
            ]);
    }

    public function deletePo(int $poId)
    {
        DB::beginTransaction();

        try {
            $po = ProgramOutcome::findOrFail($poId);
            $programId = $po->program_id;
            $programName = $po->program?->name ?? 'Unknown Program';
            $poCode = $po->po_code;

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

            return redirect()->back()->withErrors([
                'error' => 'Failed to delete PO. Please try again.',
            ]);
        }

        return redirect()->route('programs.show', ['program' => $programId])
            ->with('toast', [
                'message' => 'PO deleted and codes re-sequenced!',
                'type' => 'success'
            ]);
    }
}
