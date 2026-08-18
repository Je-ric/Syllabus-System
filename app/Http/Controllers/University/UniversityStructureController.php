<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Department;
use App\Models\Program;
use App\Services\University\UniversityStructureService;
use App\Rules\NoInjectionRule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UniversityStructureController extends Controller
{
    public function __construct(private UniversityStructureService $service)
    {
    }

    public function index()
    {
        return view('University.UniversityStructure.index', [
            'colleges'       => College::orderBy('name')->get(),
            'departments'    => Department::withRelations()->orderBy('name')->get(),
            'programs'       => Program::with('departments')->orderBy('name')->get(),
            'allDepartments' => Department::with('college')->orderBy('name')->get(),
        ]);
    }

    // =======================
    //  COLLEGE
    // =======================

    public function storeCollege(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[\p{L}\s\-\.\,]+$/u', new NoInjectionRule(), Rule::unique('colleges', 'name')],
            ], [
                'name.regex' => 'College name must contain only letters, spaces, and basic punctuation.',
                'name.min' => 'College name must be at least 2 characters.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput()
                ->with('toast', ['message' => 'Please fix the highlighted fields before submitting.', 'type' => 'error']);
        }
        $this->service->storeCollege($data);

        return back()->with('toast', ['message' => 'College added successfully.', 'type' => 'success']);
    }

    public function updateCollege(Request $request, College $college)
    {
        try {
            $data = $request->validate([
                'name' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[\p{L}\s\-\.\,]+$/u', new NoInjectionRule(), Rule::unique('colleges', 'name')->ignore($college->id)],
            ], [
                'name.regex' => 'College name must contain only letters, spaces, and basic punctuation.',
                'name.min' => 'College name must be at least 2 characters.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput()
                ->with('toast', ['message' => 'Please fix the highlighted fields before submitting.', 'type' => 'error']);
        }
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
        try {
            $data = $request->validate([
                'name'       => ['required', 'string', 'min:2', 'max:255', 'regex:/^[\p{L}\s\-\.\,]+$/u', new NoInjectionRule(), Rule::unique('departments', 'name')],
                'college_id' => ['required', 'exists:colleges,id'],
            ], [
                'name.regex' => 'Department name must contain only letters, spaces, and basic punctuation.',
                'name.min' => 'Department name must be at least 2 characters.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput()
                ->with('toast', ['message' => 'Please fix the highlighted fields before submitting.', 'type' => 'error']);
        }
        $this->service->storeDepartment($data);

        return back()->with('toast', ['message' => 'Department added successfully.', 'type' => 'success']);
    }

    public function updateDepartment(Request $request, Department $department)
    {
        try {
            $data = $request->validate([
                'name'       => ['required', 'string', 'min:2', 'max:255', 'regex:/^[\p{L}\s\-\.\,]+$/u', new NoInjectionRule(), Rule::unique('departments', 'name')->ignore($department->id)],
                'college_id' => ['required', 'exists:colleges,id'],
            ], [
                'name.regex' => 'Department name must contain only letters, spaces, and basic punctuation.',
                'name.min' => 'Department name must be at least 2 characters.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput()
                ->with('toast', ['message' => 'Please fix the highlighted fields before submitting.', 'type' => 'error']);
        }
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
        try {
            $data = $request->validate([
                'name'                        => ['required', 'string', 'min:2', 'max:255', 'regex:/^[\p{L}\s\-\.\,0-9]+$/u', new NoInjectionRule(), Rule::unique('programs', 'name')],
                'primary_department_id'       => ['required', 'exists:departments,id'],
                'supporting_department_ids'   => ['nullable', 'array'],
                'supporting_department_ids.*' => ['exists:departments,id'],
                'bor_approval_no'             => ['nullable', 'string', 'max:255', 'regex:/^[A-Za-z0-9\-\/\.\s]+$/', new NoInjectionRule()],
                'bor_approval_date'           => ['nullable', 'date'],
            ], [
                'name.regex' => 'Program name must contain only letters, numbers, spaces, and basic punctuation.',
                'name.min' => 'Program name must be at least 2 characters.',
                'bor_approval_no.regex' => 'BOR approval number can only contain letters, numbers, hyphens, slashes, and periods.',
            ]);
            
            // Additional validation: if approval number is provided, date should also be provided
        if (!empty($data['bor_approval_no']) && empty($data['bor_approval_date'])) {
            return back()->withErrors(['bor_approval_date' => 'BOR approval date is required when BOR approval number is provided.'])->withInput()
                ->with('toast', ['message' => 'Please fix the highlighted fields before submitting.', 'type' => 'error']);
        }
        
        // Validate that BOR approval date is not in the future
        if (!empty($data['bor_approval_date'])) {
            $approvalDate = \Carbon\Carbon::parse($data['bor_approval_date']);
            if ($approvalDate->isFuture()) {
                return back()->withErrors(['bor_approval_date' => 'BOR approval date cannot be in the future.'])->withInput()
                    ->with('toast', ['message' => 'Please fix the highlighted fields before submitting.', 'type' => 'error']);
            }
        }
        
            $this->service->storeProgram($data);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput()
                ->with('toast', ['message' => 'Please fix the highlighted fields before submitting.', 'type' => 'error']);
        } catch (\Throwable $e) {
            \Log::error('Failed to add program', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Failed to add program: ' . $e->getMessage()])->withInput();
        }

        return back()->with('toast', ['message' => 'Program added successfully.', 'type' => 'success']);
    }

    public function updateProgram(Request $request, Program $program)
    {
        try {
            $data = $request->validate([
                'name'                        => ['required', 'string', 'min:2', 'max:255', 'regex:/^[\p{L}\s\-\.\,0-9]+$/u', new NoInjectionRule(), Rule::unique('programs', 'name')->ignore($program->id)],
                'primary_department_id'       => ['required', 'exists:departments,id'],
                'supporting_department_ids'   => ['nullable', 'array'],
                'supporting_department_ids.*' => ['exists:departments,id'],
                'bor_approval_no'             => ['nullable', 'string', 'max:255', 'regex:/^[A-Za-z0-9\-\/\.\s]+$/', new NoInjectionRule()],
                'bor_approval_date'           => ['nullable', 'date'],
            ], [
                'name.regex' => 'Program name must contain only letters, numbers, spaces, and basic punctuation.',
                'name.min' => 'Program name must be at least 2 characters.',
                'bor_approval_no.regex' => 'BOR approval number can only contain letters, numbers, hyphens, slashes, and periods.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput()
                ->with('toast', ['message' => 'Please fix the highlighted fields before submitting.', 'type' => 'error']);
        }
        
        // Additional validation: if approval number is provided, date should also be provided
        if (!empty($data['bor_approval_no']) && empty($data['bor_approval_date'])) {
            return back()->withErrors(['bor_approval_date' => 'BOR approval date is required when BOR approval number is provided.'])->withInput()
                ->with('toast', ['message' => 'Please fix the highlighted fields before submitting.', 'type' => 'error']);
        }
        
        // Validate that BOR approval date is not in the future (only if date is being changed)
        if (!empty($data['bor_approval_date']) && $data['bor_approval_date'] != $program->bor_approval_date) {
            $approvalDate = \Carbon\Carbon::parse($data['bor_approval_date']);
            if ($approvalDate->isFuture()) {
                return back()->withErrors(['bor_approval_date' => 'BOR approval date cannot be in the future.'])->withInput()
                    ->with('toast', ['message' => 'Please fix the highlighted fields before submitting.', 'type' => 'error']);
            }
        }
        
        try {
            $error = $this->service->updateProgram($program, $data);
        } catch (\Throwable) {
            return back()->withErrors(['error' => 'Failed to update program. Please try again.'])->withInput()
                ->with('toast', ['message' => 'Failed to update program. Please try again.', 'type' => 'error']);
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
