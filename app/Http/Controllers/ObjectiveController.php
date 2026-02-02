<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\College;
use App\Models\Department;
use App\Models\CollegeGoal;
use App\Models\DepartmentObjective;
use Illuminate\Validation\Rule;

class ObjectiveController extends Controller
{
    // =======================
    //  DEPARTMENT OBJECTIVES
    // =======================

    public function objective_index(Request $request)
    {
        $colleges = College::orderBy('name')->get();

        $departments = collect();
        $objectives = collect();

        if ($request->filled('college_id')) { // If a college is selected
            $departments = Department::where('college_id', $request->college_id) // Fetch departments for that college
                ->orderBy('name')
                ->get();

            if ($request->filled('department_id')) { // If a department is selected
                $objectives = DepartmentObjective::where('department_id', $request->department_id) // Fetch objectives for that department
                    ->with('department')
                    ->orderBy('dept_obj_code')
                    ->get();
            }
        }

        return view('GoalObjective.objective',
                compact(
                        'colleges',
                        'departments',
                                    'objectives')
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
        $count = $department->objectives()->count();

        // similar to goals (READ in GoalController)
        $deptObjCode = chr(ord('a') + $count);

        DepartmentObjective::create([
            'department_id' => $request->department_id,
            'dept_obj_code' => $deptObjCode,
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
        // similar approach to goal_destroy (READ in GoalController)
        $department_id = $objective->department_id;
        $objective->delete();

        // Get remaining objectives of the department in selected college
        $remainingObjectives = DepartmentObjective::where('department_id', $department_id)
            ->orderBy('dept_obj_code')
            ->get();

        // Reindex
        $count = 0;
        foreach ($remainingObjectives as $obj) {
            $newCode = chr(ord('a') + $count);
            if ($obj->dept_obj_code !== $newCode) {
                $obj->dept_obj_code = $newCode;
                $obj->save();
            }
            $count++;
        }

        return redirect()
            ->route('objective.index', [
                'college_id' => $objective->department->college_id,
                'department_id' => $objective->department_id,
            ])
            ->with('toast', [
            'message' => 'Objective deleted successfully.',
            'type' => 'success'
        ]);
    }
}
