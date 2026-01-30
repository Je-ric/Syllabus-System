<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ProgramEducationalObjective;
use App\Models\ProgramOutcome;
use App\Helpers\ProgramCodeHelper;
use Illuminate\Http\Request;

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
        $peo = ProgramEducationalObjective::findOrFail($peoId);
        $programId = $peo->program_id;

        $peo->delete();

        // Use helper instead of repeating resequence logic
        ProgramCodeHelper::resequencePeoCodes($programId);

        return redirect()->route('programs.show', ['program' => $programId])
            ->with('toast', [
                'message' => 'PEO deleted and codes re-sequenced!',
                'type' => 'success'
            ]);
    }

    public function deletePo(int $poId)
    {
        $po = ProgramOutcome::findOrFail($poId);
        $programId = $po->program_id;

        $po->delete();

        // Use helper instead of repeating resequence logic
        ProgramCodeHelper::resequencePoCodes($programId);

        return redirect()->route('programs.show', ['program' => $programId])
            ->with('toast', [
                'message' => 'PO deleted and codes re-sequenced!',
                'type' => 'success'
            ]);
    }
}
