<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ProgramEducationalObjective;
use App\Models\ProgramOutcome;

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

        // Re-sequence remaining PEO codes
        $peos = ProgramEducationalObjective::where('program_id', $programId)
            ->orderBy('id')
            ->get();

        $counter = 1;
        foreach ($peos as $p) {
            $p->update(['peo_code' => 'PEO' . $counter]);
            $counter++;
        }

        // Redirect back to the program page
        return redirect()->route('programs.show', ['program' => $programId])
            ->with('message', 'PEO deleted and codes re-sequenced!');
    }

    public function deletePo(int $poId)
    {
        $po = ProgramOutcome::findOrFail($poId);
        $programId = $po->program_id;

        $po->delete();

        // Re-sequence remaining PO codes (a, b, c…)
        $pos = ProgramOutcome::where('program_id', $programId)
            ->orderBy('id')
            ->get();

        $counter = 1;
        foreach ($pos as $p) {
            $p->update(['po_code' => $this->numberToLetter($counter)]);
            $counter++;
        }

        return redirect()->route('programs.show', ['program' => $programId])
            ->with('message', 'PO deleted and codes re-sequenced!');
    }

    // helper method
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
}
