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

            // Only redirect if autoRedirect is enabled and redirectRoute is set
            if ($this->autoRedirect && $this->redirectRoute) {
                // Handle special cases
                if ($this->redirectRoute === 'courses.index') {
                    return redirect()->route('courses.index', ['program_id' => $this->programId]);
                }
                return redirect()->route($this->redirectRoute, $this->programId);
            }
        }
    }

    public function render()
    {
        return view('livewire.programs.program-selector');
    }

    private function preselectFromProgram(int $programId): void
    {
        $program = Program::with(['departments.college'])->find($programId);
        $department = $program?->departments->first();

        if (!$department) {
            return;
        }

        $this->collegeId = $department->college_id;
        $this->departments = Department::where('college_id', $this->collegeId)
            ->orderBy('name')
            ->get();

        $this->departmentId = $department->id;
        $this->programs = Program::whereHas('departments', function ($query) {
            $query->where('department_id', $this->departmentId);
        })->orderBy('name')->get();
    }
}
