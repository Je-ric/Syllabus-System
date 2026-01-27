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

    public function mount($programId = null)
    {
        $this->colleges = College::orderBy('name')->get();
        $this->programId = $programId;

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
    }

    public function updatedDepartmentId()
    {
        $this->programs = Program::whereHas('departments', function ($query) {
            $query->where('department_id', $this->departmentId);
        })->orderBy('name')->get();

        $this->reset('programId');
    }

    public function updatedProgramId()
    {
        if ($this->programId) {
            return redirect()->route('programs.show', $this->programId);
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
