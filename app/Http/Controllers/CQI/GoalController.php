<?php

namespace App\Http\Controllers\CQI;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\CollegeGoal;
use App\Services\CQI\GoalObjectiveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    public function __construct(private GoalObjectiveService $service) {}

    public function goal_index(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $isAdmin = $user?->hasRole('admin');

        $colleges = $isAdmin
            ? College::orderBy('name')->get()
            : $this->service->getAccessibleGoalColleges($user);

        $selectedCollegeId = $request->input('college_id');

        if (!$selectedCollegeId) {
            if ($isAdmin) {
                $selectedCollegeId = $colleges->first()?->id;
            } else {
                $selectedCollegeId = $user?->getPrimaryCollegeAssignment()?->college_id;
            }
        }

        // Non-admin: keep the assigned college selected when one exists
        if (!$isAdmin && $selectedCollegeId) {
            $assignment = $user?->getPrimaryCollegeAssignment();
            if (!$assignment || (int) $assignment->college_id !== (int) $selectedCollegeId) {
                $selectedCollegeId = $assignment?->college_id;
            }
        }

        $goals = collect();
        if ($selectedCollegeId) {
            $goals = CollegeGoal::where('college_id', $selectedCollegeId)
                ->orderBy('college_goals_code')
                ->get();
        }

        // Dean with no college assignment
        $noAssignment = !$isAdmin && $user?->hasRole('dean') && !$user?->getPrimaryCollegeAssignment();

        return view('GoalObjective.goal', compact('colleges', 'goals', 'selectedCollegeId', 'noAssignment'));
    }

    public function goal_store(Request $request)
    {
        $request->validate([
            'college_id' => ['required', 'exists:colleges,id'],
            'goal_text'  => ['required', 'string'],
        ]);

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $college = College::findOrFail($request->college_id);

        if (!$this->service->canManageGoal($user, $college)) {
            return redirect()->route('goal.index')
                ->with('toast', ['message' => 'You can only manage goals for your available college scope.', 'type' => 'warning']);
        }

        $this->service->storeGoal($college, $request->goal_text);

        return redirect()
            ->route('goal.index', ['college_id' => $request->college_id])
            ->with('toast', ['message' => 'Goal added successfully.', 'type' => 'success']);
    }

    public function goal_update(Request $request, CollegeGoal $goal)
    {
        $request->validate(['goal_text' => ['required', 'string']]);

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$this->service->canManageGoal($user, $goal->college)) {
            return redirect()->route('goal.index')
                ->with('toast', ['message' => 'You can only manage goals for your available college scope.', 'type' => 'warning']);
        }

        $this->service->updateGoal($goal, $request->goal_text);

        return redirect()
            ->route('goal.index', ['college_id' => $goal->college_id])
            ->with('toast', ['message' => 'Goal updated successfully.', 'type' => 'success']);
    }

    public function goal_destroy(Request $request, CollegeGoal $goal)
    {
        $collegeId = $goal->college_id;

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$this->service->canManageGoal($user, $goal->college)) {
            return redirect()->route('goal.index')
                ->with('toast', ['message' => 'You can only manage goals for your available college scope.', 'type' => 'warning']);
        }

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
