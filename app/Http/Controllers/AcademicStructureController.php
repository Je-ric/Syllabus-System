<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\College;
use App\Models\Department;
use App\Models\Program;

class AcademicStructureController extends Controller
{
    public function index()
    {
        return view('AcademicStructure.index', [
            'colleges' => College::orderBy('name')->get(),
            'departments' => Department::with('college', 'programs')->orderBy('name')->get(),
            'programs' => Program::all()->sortBy('name'),
        ]);
    }

    public function storeCollege(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:colleges,name',
        ]);

        College::create([
            'name' => $request->name,
        ]);

        return back()->with('success', 'College added successfully.');
    }

    public function storeDepartment(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'college_id' => 'required|exists:colleges,id',
        ]);

        Department::create([
            'name' => $request->name,
            'college_id' => $request->college_id,
        ]);

        return back()->with('success', 'Department added successfully.');
    }

    public function storeProgram(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:programs,name',
            'department_id' => 'required|exists:departments,id',
            'bor_approval_no' => 'nullable|string',
            'bor_approval_date' => 'nullable|date',
        ]);

        $program = Program::create([
            'name' => $request->name,
            'bor_approval_no' => $request->bor_approval_no,
            'bor_approval_date' => $request->bor_approval_date,
        ]);

        // Insert into program_departments junction table
        $program->departments()->attach($request->department_id, ['role' => 'primary']);

        return back()->with('success', 'Program added successfully.');
    }
}
