<?php

namespace App\Http\Controllers;

use App\Models\Program;

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
}
