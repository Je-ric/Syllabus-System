<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\CollegeGoal;
use App\Services\GoalObjectiveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    public function __construct(private GoalObjectiveService $service) {}

    public function goal_index(Request $request)
    {
        $colleges = College::orderBy('name')->get();

        $selectedCollegeId = $request->input('college_id');

        if (!$selectedCollegeId) {
            /** @var \App\Models\User|null $user */
            $user              = Auth::user();
            $assignment        = $user?->getPrimaryCollegeAssignment();
            $selectedCollegeId = $assignment?->college_id;
        }

        $goals = collect();
        if ($selectedCollegeId) {
            $goals = CollegeGoal::where('college_id', $selectedCollegeId)
                ->orderBy('college_goals_code')
                ->get();
        }

        return view('GoalObjective.goal', compact('colleges', 'goals', 'selectedCollegeId'));
    }

    public function goal_store(Request $request)
    {
        $request->validate([
            'college_id' => ['required', 'exists:colleges,id'],
            'goal_text'  => ['required', 'string'],
        ]);

        $college = College::findOrFail($request->college_id);
        $this->service->storeGoal($college, $request->goal_text);

        return redirect()
            ->route('goal.index', ['college_id' => $request->college_id])
            ->with('toast', ['message' => 'Goal added successfully.', 'type' => 'success']);
    }

    public function goal_update(Request $request, CollegeGoal $goal)
    {
        $request->validate(['goal_text' => ['required', 'string']]);

        $this->service->updateGoal($goal, $request->goal_text);

        return redirect()
            ->route('goal.index', ['college_id' => $goal->college_id])
            ->with('toast', ['message' => 'Goal updated successfully.', 'type' => 'success']);
    }

    public function goal_destroy(Request $request, CollegeGoal $goal)
    {
        $collegeId = $goal->college_id;

        try {
            $this->service->destroyGoal($goal);
        } catch (\Throwable) {
            return redirect()->back()->withErrors(['error' => 'Failed to delete goal. Please try again.']);
        }

        return redirect()
            ->route('goal.index', ['college_id' => $collegeId])
            ->with('toast', ['message' => 'Goal deleted and codes re-sequenced.', 'type' => 'success']);
    }
}
