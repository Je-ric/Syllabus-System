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
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $isAdmin = $user?->hasRole('admin');

        $colleges = $isAdmin
            ? College::orderBy('name')->get()
            : College::whereHas('userAssignments', fn($q) => $q->where('user_id', $user->id)->where('context', 'dean'))
                ->orderBy('name')->get();

        $selectedCollegeId = $request->input('college_id');

        if (!$selectedCollegeId) {
            $assignment        = $user?->getPrimaryCollegeAssignment();
            $selectedCollegeId = $assignment?->college_id;
        }

        // Non-admin: restrict to assigned college only
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

        return view('GoalObjective.goal', compact('colleges', 'goals', 'selectedCollegeId'));
    }

    public function goal_store(Request $request)
    {
        $request->validate([
            'college_id' => ['required', 'exists:colleges,id'],
            'goal_text'  => ['required', 'string'],
        ]);

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            $assignment = $user->getPrimaryCollegeAssignment();
            if (!$assignment || (int) $assignment->college_id !== (int) $request->college_id) {
                return redirect()->route('goal.index')
                    ->with('toast', ['message' => 'You can only manage goals for your assigned college.', 'type' => 'warning']);
            }
        }

        $college = College::findOrFail($request->college_id);
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
        if (!$user->hasRole('admin')) {
            $assignment = $user->getPrimaryCollegeAssignment();
            if (!$assignment || (int) $assignment->college_id !== (int) $goal->college_id) {
                return redirect()->route('goal.index')
                    ->with('toast', ['message' => 'You can only manage goals for your assigned college.', 'type' => 'warning']);
            }
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
        if (!$user->hasRole('admin')) {
            $assignment = $user->getPrimaryCollegeAssignment();
            if (!$assignment || (int) $assignment->college_id !== (int) $collegeId) {
                return redirect()->route('goal.index')
                    ->with('toast', ['message' => 'You can only manage goals for your assigned college.', 'type' => 'warning']);
            }
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
