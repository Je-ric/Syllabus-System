<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\College;
use App\Models\Department;
use App\Models\CollegeGoal;
use App\Models\DepartmentObjective;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ObjectiveController extends Controller
{
    // =======================
    //  DEPARTMENT OBJECTIVES
    // =======================

    public function objective_index(Request $request)
    {
        $colleges = College::orderBy('name')->get();

        $selectedCollegeId = $request->input('college_id');
        $selectedDepartmentId = $request->input('department_id');

        if (!$selectedCollegeId || !$selectedDepartmentId) {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();
            $assignment = $user?->getPrimaryDepartmentAssignment();
            if ($assignment?->department) {
                $selectedDepartmentId = $assignment->department_id;
                $selectedCollegeId = $assignment->department->college_id;
            }
        }

        $departments = collect();
        $objectives = collect();

        if ($selectedCollegeId) { // If a college is selected
            $departments = Department::where('college_id', $selectedCollegeId) // Fetch departments for that college
                ->orderBy('name')
                ->get();

            if ($selectedDepartmentId) { // If a department is selected
                $objectives = DepartmentObjective::where('department_id', $selectedDepartmentId) // Fetch objectives for that department
                    ->with('department')
                    ->orderBy('dept_obj_code')
                    ->get();
            }
        }

        return view('GoalObjective.objective',
                compact(
                        'colleges',
                        'departments',
                                    'objectives',
                                    'selectedCollegeId',
                                    'selectedDepartmentId')
                            );
    }

    public function objective_store(Request $request)
    {
        $request->validate([
            'college_id' => ['required', 'exists:colleges,id'],
            'department_id' => [
                'required',
                Rule::exists('departments', 'id')
                    ->where('college_id', $request->college_id),
            ],
            'objective_text' => ['required', 'string'],
        ]);

        $department = Department::findOrFail($request->department_id);

        DepartmentObjective::create([
            'department_id' => $request->department_id,
            'dept_obj_code' => $department->getNextObjectiveCode(), // use model helper (Department.php)
            'objective_text' => $request->objective_text,
        ]);

        return redirect()
            ->route('objective.index', [
                'college_id' => $request->college_id,
                'department_id' => $request->department_id,
            ])
            ->with('toast', [
            'message' => 'Objective added successfully.',
            'type' => 'success'
        ]);
    }

    public function objective_update(Request $request, DepartmentObjective $objective)
    {
        $request->validate([
            'objective_text' => ['required', 'string'],
        ]);

        $objective->update([
            'objective_text' => $request->objective_text,
        ]);

        return redirect()
            ->route('objective.index', [
                'college_id' => $objective->department->college_id,
                'department_id' => $objective->department_id,
            ])
            ->with('toast', [
            'message' => 'Objective updated successfully.',
            'type' => 'success'
        ]);
    }

    public function objective_destroy(DepartmentObjective $objective)
    {
        $department = $objective->department;
        $objective->delete();

        $department->resequenceObjectiveCodes(); // Department.php

        return redirect()
            ->route('objective.index', [
                'college_id' => $department->college_id,
                'department_id' => $department->id,
            ])
            ->with('toast', [
            'message' => 'Objective deleted successfully.',
            'type' => 'success'
        ]);
    }
}
