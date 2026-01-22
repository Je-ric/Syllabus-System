<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\College;
use App\Models\Department;
use App\Models\CollegeGoal;
use App\Models\DepartmentObjective;
use Illuminate\Validation\Rule;

class GoalObjectiveController extends Controller
{
    // =======================
    //  COLLEGE GOALS
    // =======================

    public function goal_index(Request $request)
    {
        $colleges = College::orderBy('name')->get();

        $goals = collect();
        if ($request->filled('college_id')) { // If a college is selected
            $goals = CollegeGoal::where('college_id', $request->college_id) // Fetch goals for that college
                ->orderBy('college_goals_code')
                ->get();
        }

        return view('GoalObjective.goal', compact('colleges', 'goals'));
    }

    public function goal_store(Request $request)
    {
        $request->validate([
            'college_id' => ['required', 'exists:colleges,id'],
            'college_goals_code' => [
                'required',
                'string',
                Rule::unique('college_goals', 'college_goals_code')
                    ->where('college_id', $request->college_id),
            ],
            'goal_text' => ['required', 'string'],
        ]);

        CollegeGoal::create([
            'college_id' => $request->college_id,
            'college_goals_code' => $request->college_goals_code,
            'goal_text' => $request->goal_text,
        ]);

        return redirect()
            ->route('goal.index', ['college_id' => $request->college_id])
            ->with('success', 'Goal added successfully.');
    }

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
            'dept_obj_code' => [
                'required',
                Rule::unique('department_objectives', 'dept_obj_code')
                    ->where('department_id', $request->department_id),
            ],
            'objective_text' => ['required', 'string'],
        ]);

        DepartmentObjective::create([
            'department_id' => $request->department_id,
            'dept_obj_code' => $request->dept_obj_code,
            'objective_text' => $request->objective_text,
        ]);

        return redirect()
            ->route('objective.index', [
                'college_id' => $request->college_id,
                'department_id' => $request->department_id,
            ])
            ->with('success', 'Objective added successfully.');
    }
}
