<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\College;
use App\Models\CollegeGoal;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GoalController extends Controller
{
    // =======================
    //  COLLEGE GOALS
    // =======================

    public function goal_index(Request $request)
    {
        $colleges = College::orderBy('name')->get();

        $selectedCollegeId = $request->input('college_id');
        if (!$selectedCollegeId) {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();
            $assignment = $user?->getPrimaryCollegeAssignment();
            $selectedCollegeId = $assignment?->college_id;
        }

        $goals = collect();
        if ($selectedCollegeId) { // If a college is selected
            $goals = CollegeGoal::where('college_id', $selectedCollegeId) // Fetch goals for that college
                ->orderBy('college_goals_code')
                ->get();
        }

        return view('GoalObjective.goal', compact('colleges', 'goals', 'selectedCollegeId'));
    }

    public function goal_store(Request $request)
    {
        $request->validate([
            'college_id' => ['required', 'exists:colleges,id'],
            'goal_text' => ['required', 'string'],
        ]);

        $college = College::findOrFail($request->college_id);

        $goal = CollegeGoal::create([
            'college_id' => $request->college_id,
            'college_goals_code' => $college->getNextGoalCode(), // use model helper (College.php)
            'goal_text' => $request->goal_text,
        ]);

        // LOGS
        AuditLog::record(
            action: 'created',
            module: 'Goal',
            referenceId: $goal->id,
            description: "Created goal {$goal->college_goals_code} for college {$college->name}."
        );

        return redirect()
            ->route('goal.index', ['college_id' => $request->college_id])
            ->with('toast', [
            'message' => 'Goal added successfully.',
            'type' => 'success'
        ]);
    }

    // auto code re-sequence
    public function goal_update(Request $request, CollegeGoal $goal)
    {
        $request->validate([
            'goal_text' => ['required', 'string'],
        ]);

        $goal->update([
            'goal_text' => $request->goal_text,
        ]);

        $college = $goal->college;

        // LOGS
        AuditLog::record(
            action: 'updated',
            module: 'Goal',
            referenceId: $goal->id,
            description: "Updated goal {$goal->college_goals_code} for college {$college?->name}."
        );

        return redirect()
            ->route('goal.index', ['college_id' => $goal->college_id])
            ->with('toast', [
            'message' => 'Goal updated successfully.',
            'type' => 'success'
        ]);
    }

    public function goal_destroy(Request $request, CollegeGoal $goal)
    {
        DB::beginTransaction();

        try {
            $college = $goal->college;
            $goalCode = $goal->college_goals_code;
            $goal->delete();
            $college->resequenceGoalCodes(); // College.php

            // LOGS
            AuditLog::record(
                action: 'deleted',
                module: 'Goal',
                referenceId: $goal->id,
                description: "Deleted goal {$goalCode} for college {$college->name}."
            );

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()->withErrors([
                'error' => 'Failed to delete goal. Please try again.',
            ]);
        }

        return redirect()
            ->route('goal.index', ['college_id' => $college->id])
            ->with('toast', [
            'message' => 'Goal deleted and codes reset successfully.',
            'type' => 'success'
        ]);
    }

}
