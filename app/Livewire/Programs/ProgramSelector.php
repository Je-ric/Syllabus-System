<?php

namespace App\Livewire\Programs;

use Livewire\Component;
use App\Models\College;
use App\Models\Department;
use App\Models\Program;

class ProgramSelector extends Component
{
    public $colleges = [];
    public $departments = [];
    public $programs = [];

    public $collegeId;
    public $departmentId;
    public $programId;

    // Route to redirect to after selection (optional)
    // Set to null to disable redirect and use event dispatching instead
    public $redirectRoute = null;

    // Whether to auto-redirect when program is selected
    public $autoRedirect = true;

    public function mount($programId = null, $redirectRoute = null, $autoRedirect = true)
    {
        $this->colleges = College::orderBy('name')->get();
        $this->programId = $programId;
        $this->redirectRoute = $redirectRoute;
        $this->autoRedirect = $autoRedirect;

        if ($this->programId) {
            $this->preselectFromProgram($this->programId);
        }
    }

    public function updatedCollegeId()
    {
        $this->departments = Department::where('college_id', $this->collegeId)
            ->orderBy('name')
            ->get();

        $this->reset(['departmentId', 'programId', 'programs']);
        $this->dispatch('programSelected', programId: null);
    }

    public function updatedDepartmentId()
    {
        $this->programs = Program::whereHas('departments', function ($query) {
            $query->where('department_id', $this->departmentId);
        })->orderBy('name')->get();

        $this->reset('programId');
        $this->dispatch('programSelected', programId: null);
    }

    public function updatedProgramId()
    {
        if ($this->programId) {
            // Dispatch event for parent component to listen to
            $this->dispatch('programSelected', programId: $this->programId);

            // Only redirect if autoRedirect is enabled and redirectRoute is set, not auto HAHAHAHAA
            // I mean, we need to set the route first before redirecting
            if ($this->autoRedirect && $this->redirectRoute) {
                // Handle special cases
                if ($this->redirectRoute === 'courses.index') {
                    return redirect()->route('courses.index', ['program_id' => $this->programId]);
                }
                if ($this->redirectRoute === 'syllabus.create') {
                    return redirect()->route('syllabus.create', ['program_id' => $this->programId]);
                }
                return redirect()->route($this->redirectRoute, $this->programId);
            }
        }
    }

    public function render()
    {
        return view('livewire.programs.program-selector');
    }

    // Preselect college and department based on given program ID
    // without this, the selector would not know which college/department to select
    // even when we select a program directly, page will reload and the college and department would be empty

    // This function loads the program with its related departments and colleges after selecting a program,
    // while the other 3 functions react to user input changes 
    private function preselectFromProgram(int $programId): void
    {
        // load program with its departments and their colleges
        $program = Program::with(['departments.college'])->find($programId);
        $department = $program?->departments->first();

        if (!$department) {
            return;
        }
        // set college and load departments
        $this->collegeId = $department->college_id;
        $this->departments = Department::where('college_id', $this->collegeId)
            ->orderBy('name')
            ->get();

        // set department and load programs
        $this->departmentId = $department->id;
        $this->programs = Program::whereHas('departments', function ($query) {
            $query->where('department_id', $this->departmentId);
        })->orderBy('name')->get();
    }
}
