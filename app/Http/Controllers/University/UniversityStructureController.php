<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Department;
use App\Models\Program;
use App\Services\UniversityStructureService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UniversityStructureController extends Controller
{
    public function __construct(private UniversityStructureService $service)
    {
    }

    public function index()
    {
        return view('UniversityStructure.index', [
            'colleges'       => College::orderBy('name')->get(),
            'departments'    => Department::withRelations()->orderBy('name')->get(),
            'programs'       => Program::with('departments')->get()->sortBy('name'),
            'allDepartments' => Department::with('college')->orderBy('name')->get(),
        ]);
    }

    // =======================
    //  COLLEGE
    // =======================

    public function storeCollege(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', Rule::unique('colleges', 'name')],
        ]);
        $this->service->storeCollege($data);

        return back()->with('toast', ['message' => 'College added successfully.', 'type' => 'success']);
    }

    public function updateCollege(Request $request, College $college)
    {
        $data = $request->validate([
            'name' => ['required', 'string', Rule::unique('colleges', 'name')->ignore($college->id)],
        ]);
        $this->service->updateCollege($college, $data);

        return back()->with('toast', ['message' => 'College updated successfully.', 'type' => 'success']);
    }

    public function destroyCollege(College $college)
    {
        try {
            $error = $this->service->destroyCollege($college);
        } catch (\Throwable) {
            return back()->with('toast', ['message' => 'Failed to delete college. Please try again.', 'type' => 'error']);
        }

        if ($error)
            return back()->with('toast', ['message' => $error, 'type' => 'error']);

        return back()->with('toast', ['message' => 'College deleted successfully.', 'type' => 'success']);
    }

    // =======================
    //  DEPARTMENT
    // =======================

    public function storeDepartment(Request $request)
    {
        $data = $request->validate([
            'name'       => ['required', 'string', Rule::unique('departments', 'name')],
            'college_id' => ['required', 'exists:colleges,id'],
        ]);
        $this->service->storeDepartment($data);

        return back()->with('toast', ['message' => 'Department added successfully.', 'type' => 'success']);
    }

    public function updateDepartment(Request $request, Department $department)
    {
        $data = $request->validate([
            'name'       => ['required', 'string', Rule::unique('departments', 'name')->ignore($department->id)],
            'college_id' => ['required', 'exists:colleges,id'],
        ]);
        $this->service->updateDepartment($department, $data);

        return back()->with('toast', ['message' => 'Department updated successfully.', 'type' => 'success']);
    }

    public function destroyDepartment(Department $department)
    {
        try {
            $error = $this->service->destroyDepartment($department);
        } catch (\Throwable) {
            return back()->with('toast', ['message' => 'Failed to delete department. Please try again.', 'type' => 'error']);
        }

        if ($error)
            return back()->with('toast', ['message' => $error, 'type' => 'error']);

        return back()->with('toast', ['message' => 'Department deleted successfully.', 'type' => 'success']);
    }

    // =======================
    //  PROGRAM
    // =======================

    public function storeProgram(Request $request)
    {
        $data = $request->validate([
            'name'                        => ['required', 'string', Rule::unique('programs', 'name')],
            'primary_department_id'       => ['required', 'exists:departments,id'],
            'supporting_department_ids'   => ['nullable', 'array'],
            'supporting_department_ids.*' => ['exists:departments,id'],
            'bor_approval_no'             => ['nullable', 'string'],
            'bor_approval_date'           => ['nullable', 'date'],
        ]);
        try {
            $this->service->storeProgram($data);
        } catch (\Throwable) {
            return back()->withErrors(['error' => 'Failed to add program. Please try again.'])->withInput();
        }

        return back()->with('toast', ['message' => 'Program added successfully.', 'type' => 'success']);
    }

    public function updateProgram(Request $request, Program $program)
    {
        $data = $request->validate([
            'name'                        => ['required', 'string', Rule::unique('programs', 'name')->ignore($program->id)],
            'primary_department_id'       => ['required', 'exists:departments,id'],
            'supporting_department_ids'   => ['nullable', 'array'],
            'supporting_department_ids.*' => ['exists:departments,id'],
            'bor_approval_no'             => ['nullable', 'string'],
            'bor_approval_date'           => ['nullable', 'date'],
        ]);
        try {
            $error = $this->service->updateProgram($program, $data);
        } catch (\Throwable) {
            return back()->withErrors(['error' => 'Failed to update program. Please try again.'])->withInput();
        }

        if ($error)
            return back()->with('toast', ['message' => $error, 'type' => 'error']);

        return back()->with('toast', ['message' => 'Program updated successfully.', 'type' => 'success']);
    }

    public function destroyProgram(Program $program)
    {
        try {
            $error = $this->service->destroyProgram($program);
        } catch (\Throwable) {
            return back()->with('toast', ['message' => 'Failed to delete program. Please try again.', 'type' => 'error']);
        }

        if ($error)
            return back()->with('toast', ['message' => $error, 'type' => 'error']);

        return back()->with('toast', ['message' => 'Program deleted successfully.', 'type' => 'success']);
    }
}
