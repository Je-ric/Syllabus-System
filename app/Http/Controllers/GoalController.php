<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\College;
use App\Models\Department;
use App\Models\CollegeGoal;
use App\Models\DepartmentObjective;
use Illuminate\Validation\Rule;

class GoalController extends Controller
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
            // 'college_goals_code' => [
            //     'required',
            //     'string',
            //     Rule::unique('college_goals', 'college_goals_code')
            //         ->where('college_id', $request->college_id),
            // ],
            'goal_text' => ['required', 'string'],
        ]);

        $college = College::findOrFail($request->college_id);
        $count = $college->goals()->count();

        // a (1), b (2), c (3), ...
        $collegeGoalsCode = chr(ord('a') + $count);

        CollegeGoal::create([
            'college_id' => $request->college_id,
            'college_goals_code' => $collegeGoalsCode, // auto sana (a,b,c per college)
                // if we will do that, we need to check the college, then check its existing goals to get the count which convert to a-z.
            'goal_text' => $request->goal_text,
        ]);
        

        return redirect()
            ->route('goal.index', ['college_id' => $request->college_id])
            ->with('success', 'Goal added successfully.');
    }

    
    public function goal_update(Request $request, CollegeGoal $goal)
    {
        $request->validate([
            // 'college_goals_code' => [
            //     'required',
            //     Rule::unique('college_goals', 'college_goals_code')
            //         ->where('college_id', $goal->college_id)
            //         ->ignore($goal->id),
            // ],
            'goal_text' => ['required', 'string'],
        ]);

        $goal->update([
            // 'college_goals_code' => $request->college_goals_code,
            'goal_text' => $request->goal_text,
        ]);

        return redirect()
            ->route('goal.index', ['college_id' => $goal->college_id])
            ->with('success', 'Goal updated successfully.');
    }

    public function goal_destroy(Request $request, CollegeGoal $goal)
    {
        $college_id = $goal->college_id;

        $goal->delete(); // delete the selected goal first, before reindexing

        // get goals of the same college
        $remainingGoals = CollegeGoal::where('college_id', $college_id)
                            ->orderBy('college_goals_code')
                            ->get();

        $count = 0; // Reindex codes
        foreach ($remainingGoals as $g) {
            $newCode = chr(ord('a') + $count); // a (1), b (2), c (3) ...
            if ($g->college_goals_code !== $newCode) { // only update if code is different
                $g->college_goals_code = $newCode; // update code
                $g->save();
            }
            $count++; // use for re-indexing
        }

        return redirect()
            ->route('goal.index', ['college_id' => $college_id])
            ->with('success', 'Goal deleted and codes reset successfully.');
    }

}
