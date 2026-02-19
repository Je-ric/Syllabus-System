<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveCollegeRequest;
use App\Http\Requests\SaveDepartmentRequest;
use App\Http\Requests\SaveProgramRequest;
use App\Models\College;
use App\Models\Department;
use App\Models\Program;
use Illuminate\Support\Facades\DB;

class AcademicStructureController extends Controller
{
    public function index()
    {
        return view('AcademicStructure.index', [
            'colleges' => College::orderBy('name')->get(),
            'departments' => Department::withRelations()->orderBy('name')->get(), 
            'programs' => Program::all()->sortBy('name'),
        ]);
    }

    // =======================
    //  COLLEGE
    // =======================

    public function storeCollege(SaveCollegeRequest $request)
    {
        $validated = $request->validated();

        College::create([
            'name' => $validated['name'],
        ]);

        return back()->with('toast', [
            'message' => 'College added successfully.',
            'type' => 'success'
        ]);
    }

    public function updateCollege(SaveCollegeRequest $request, College $college)
    {
        $validated = $request->validated();

        $college->update([
            'name' => $validated['name'],
        ]);

        return back()->with('toast', [
            'message' => 'College updated successfully.',
            'type' => 'success'
        ]);
    }

    // =======================
    //  DEPARTMENT
    // =======================

    public function storeDepartment(SaveDepartmentRequest $request)
    {
        $validated = $request->validated();

        Department::create([
            'name' => $validated['name'],
            'college_id' => $validated['college_id'],
        ]);

        return back()->with('toast', [
            'message' => 'Department added successfully.',
            'type' => 'success'
        ]);
    }

    public function updateDepartment(SaveDepartmentRequest $request, Department $department)
    {
        $validated = $request->validated();

        $department->update([
            'name' => $validated['name'],
            'college_id' => $validated['college_id'],
        ]);

        return back()->with('toast', [
            'message' => 'Department updated successfully.',
            'type' => 'success'
        ]);
    }

    // =======================
    //  PROGRAM
    // =======================

    public function storeProgram(SaveProgramRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            $program = Program::create([
                'name' => $validated['name'],
                'bor_approval_no' => $validated['bor_approval_no'] ?? null,
                'bor_approval_date' => $validated['bor_approval_date'] ?? null,
            ]);

            // Insert into program_departments junction table
            $program->departments()->attach($validated['department_id'], ['role' => 'primary']);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => 'Failed to add program. Please try again.',
            ])->withInput();
        }

        return back()->with('toast', [
            'message' => 'Program added successfully.',
            'type' => 'success'
        ]);
    }

    public function updateProgram(SaveProgramRequest $request, Program $program)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            $program->update([
                'name' => $validated['name'],
                'bor_approval_no' => $validated['bor_approval_no'] ?? null,
                'bor_approval_date' => $validated['bor_approval_date'] ?? null,
            ]);

            // Update program_departments junction table
            $program->departments()->sync([$validated['department_id'] => ['role' => 'primary']]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => 'Failed to update program. Please try again.',
            ])->withInput();
        }

        return back()->with('toast', [
            'message' => 'Program updated successfully.',
            'type' => 'success'
        ]);
    }
}
