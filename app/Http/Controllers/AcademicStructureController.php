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

    // =======================
    //  COLLEGE
    // =======================

    public function storeCollege(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:colleges,name',
        ]);

        College::create([
            'name' => $request->name,
        ]);

        return back()->with('toast', [
            'message' => 'College added successfully.',
            'type' => 'success'
        ]);
    }

    public function updateCollege(Request $request, College $college)
    {
        $college = College::findOrFail($college->id);
        $request->validate([
            'name' => 'required|string|unique:colleges,name,' . $college->id,
        ]);

        $college->update([
            'name' => $request->name,
        ]);

        return back()->with('toast', [
            'message' => 'College updated successfully.',
            'type' => 'success'
        ]);
    }

    // =======================
    //  DEPARTMENT
    // =======================

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

        return back()->with('toast', [
            'message' => 'Department added successfully.',
            'type' => 'success'
        ]);
    }

    public function updateDepartment(Request $request, Department $department)
    {
        $department = Department::findOrFail($department->id);
        $request->validate([
            'name' => 'required|string',
            'college_id' => 'required|exists:colleges,id',
        ]);

        $department->update([
            'name' => $request->name,
            'college_id' => $request->college_id,
        ]);

        return back()->with('toast', [
            'message' => 'Department updated successfully.',
            'type' => 'success'
        ]);
    }

    // =======================
    //  PROGRAM
    // =======================

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

        return back()->with('toast', [
            'message' => 'Program added successfully.',
            'type' => 'success'
        ]);
    }

    public function updateProgram(Request $request, Program $program)
    {
        $program = Program::findOrFail($program->id);
        $request->validate([
            'name' => 'required|string|unique:programs,name,' . $program->id,
            'department_id' => 'required|exists:departments,id',
            'bor_approval_no' => 'nullable|string',
            'bor_approval_date' => 'nullable|date',
        ]);

        $program->update([
            'name' => $request->name,
            'bor_approval_no' => $request->bor_approval_no,
            'bor_approval_date' => $request->bor_approval_date,
        ]);

        // Update program_departments junction table
        $program->departments()->sync([$request->department_id => ['role' => 'primary']]);

        return back()->with('toast', [
            'message' => 'Program updated successfully.',
            'type' => 'success'
        ]);
    }
}
