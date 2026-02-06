<?php

namespace App\Livewire\Programs;

use Livewire\Component;
use App\Models\College;
use App\Models\Department;
use App\Models\Program;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class ProgramSelector extends Component
{
    /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\College> */
    public $colleges = [];

    /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Department> */
    public $departments = [];

    /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Program> */
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
        $this->redirectRoute = $redirectRoute;
        $this->autoRedirect = $autoRedirect;

        // If programId is explicitly provided via query param, use that (highest priority)
        if ($programId) {
            $this->programId = $programId;
            $this->preselectFromProgram($this->programId);
        } else {
            // Otherwise, try to preselect based on user assignments
            $preselectedProgramId = $this->preselectFromUserAssignments();

            // If a program was preselected, check if we should redirect
            // Only redirect if autoRedirect is enabled, redirectRoute is set,
            // and we're not already on a page with this program (to avoid loops)
            if ($preselectedProgramId && $this->autoRedirect && $this->redirectRoute) {
                $currentProgramId = request('program_id');
                // Only redirect if we're not already showing this program
                if ($currentProgramId != $preselectedProgramId) {
                    $this->redirectWithProgramId($preselectedProgramId);
                }
            }
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



    // Redirect to the redirectRoute with the program ID in query string
    // This is called from mount, so we use $this->redirect() which works in mount
    private function redirectWithProgramId(int $programId): void
    {
        // Handle special cases
        if ($this->redirectRoute === 'courses.index') {
            $this->redirect(route('courses.index', ['program_id' => $programId]), navigate: true);
            return;
        }
        if ($this->redirectRoute === 'syllabus.create') {
            $this->redirect(route('syllabus.create', ['program_id' => $programId]), navigate: true);
            return;
        }
        if ($this->redirectRoute === 'programs.show') {
            $this->redirect(route('programs.show', ['program' => $programId]), navigate: true);
            return;
        }
        // Generic route handling - try with program_id query param
        $this->redirect(route($this->redirectRoute, ['program_id' => $programId]), navigate: true);
    }

    /**
     * Preselect college, department, and optionally program based on user's assignments
     * Priority: Department assignment (chair/faculty) > College assignment (dean)
     * Returns the preselected program ID if one was preselected, null otherwise
     */

    // This function checks the authenticated user's assignments to determine if they should have a program preselected.
    // It first checks if the user has a department assignment (either as chair or faculty).
    // If so, it preselects the college and department associated with that assignment, and loads the programs
    private function preselectFromUserAssignments(): ?int
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        // Check for department assignment first (chair or faculty)
        /** @var \App\Models\UserAssignment|null $deptAssignment */
        $deptAssignment = $user->getPrimaryDepartmentAssignment();

        if ($deptAssignment && $deptAssignment->department) {
            /** @var \App\Models\Department $department */
            $department = $deptAssignment->department;
            /** @var \App\Models\College $college */
            $college = $department->college;

            // Preselect college and department
            $this->collegeId = $college->id;
            $this->departments = Department::where('college_id', $this->collegeId)
                ->orderBy('name')
                ->get();
            $this->departmentId = $department->id;

            // Load programs for this department
            $this->programs = Program::whereHas('departments', function ($query) {
                $query->where('department_id', $this->departmentId);
            })->orderBy('name')->get();

            // If only 1 program in this department, preselect it
            if ($this->programs->count() === 1) {
                $program = $this->programs->first();
                if ($program) {
                    $this->programId = $program->id;
                    $this->dispatch('programSelected', programId: $this->programId);
                    return $this->programId;
                }
            }

            return null;
        }

        // Check for college assignment (dean)
        /** @var \App\Models\UserAssignment|null $collegeAssignment */
        $collegeAssignment = $user->getPrimaryCollegeAssignment();

        if ($collegeAssignment && $collegeAssignment->college) {
            /** @var \App\Models\College $college */
            $college = $collegeAssignment->college;

            // Preselect college
            $this->collegeId = $college->id;
            $this->departments = Department::where('college_id', $this->collegeId)
                ->orderBy('name')
                ->get();

            // If only 1 department in this college, preselect it
            if ($this->departments->count() === 1) {
                $department = $this->departments->first();
                if ($department) {
                    $this->departmentId = $department->id;

                    // Load programs for this department
                    $this->programs = Program::whereHas('departments', function ($query) {
                        $query->where('department_id', $this->departmentId);
                    })->orderBy('name')->get();

                    // If only 1 program in this department, preselect it
                    if ($this->programs->count() === 1) {
                        $program = $this->programs->first();
                        if ($program) {
                            $this->programId = $program->id;
                            $this->dispatch('programSelected', programId: $this->programId);
                            return $this->programId;
                        }
                    }
                }
            }
        }

        return null;
    }

    // preselectFromProgram() → Restores college, department, and program when loading the page with an existing program.
    // preselectFromUserAssignments() → Preselects based on user's role assignments (chair/faculty/dean).
    // updatedCollegeId() → Reacts when the user changes the college and resets dependent selections.
    // updatedDepartmentId() → Reacts when the user changes the department and reloads available programs.
    // updatedProgramId() → Reacts when the user selects a program and syncs it with the rest of the app.
}
