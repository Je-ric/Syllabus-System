<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\Department;
use App\Models\DepartmentObjective;
use App\Services\GoalObjectiveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ObjectiveController extends Controller
{
    public function __construct(private GoalObjectiveService $service) {}

    public function objective_index(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $isAdmin = $user?->hasRole('admin');

        $selectedCollegeId    = $request->input('college_id');
        $selectedDepartmentId = $request->input('department_id');

        if (!$selectedCollegeId || !$selectedDepartmentId) {
            $assignment = $user?->getPrimaryDepartmentAssignment();

            if ($assignment?->department) {
                $selectedDepartmentId = $assignment->department_id;
                $selectedCollegeId    = $assignment->department->college_id;
            }
        }

        // Non-admin: restrict to assigned department only
        if (!$isAdmin && $selectedDepartmentId) {
            $assignment = $user?->getPrimaryDepartmentAssignment();
            if (!$assignment || (int) $assignment->department_id !== (int) $selectedDepartmentId) {
                $selectedDepartmentId = $assignment?->department_id;
                $selectedCollegeId    = $assignment?->department?->college_id;
            }
        }

        $colleges = $isAdmin
            ? College::orderBy('name')->get()
            : College::whereHas('departments.userAssignments', fn($q) =>
                $q->where('user_id', $user->id)->whereIn('context', ['chair', 'faculty']))
                ->orderBy('name')->get();

        $departments = collect();
        $objectives  = collect();

        if ($selectedCollegeId) {
            $departments = $isAdmin
                ? Department::where('college_id', $selectedCollegeId)->orderBy('name')->get()
                : Department::where('college_id', $selectedCollegeId)
                    ->whereHas('userAssignments', fn($q) =>
                        $q->where('user_id', $user->id)->whereIn('context', ['chair', 'faculty']))
                    ->orderBy('name')->get();

            if ($selectedDepartmentId) {
                $objectives = DepartmentObjective::where('department_id', $selectedDepartmentId)
                    ->with('department')
                    ->orderBy('dept_obj_code')
                    ->get();
            }
        }

        return view('GoalObjective.objective', compact(
            'colleges',
            'departments',
            'objectives',
            'selectedCollegeId',
            'selectedDepartmentId'
        ));
    }

    public function objective_store(Request $request)
    {
        $request->validate([
            'college_id'     => ['required', 'exists:colleges,id'],
            'department_id'  => [
                'required',
                Rule::exists('departments', 'id')->where('college_id', $request->college_id),
            ],
            'objective_text' => ['required', 'string'],
        ]);

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            $assignment = $user->getPrimaryDepartmentAssignment();
            if (!$assignment || (int) $assignment->department_id !== (int) $request->department_id) {
                return redirect()->route('objective.index')
                    ->with('toast', ['message' => 'You can only manage objectives for your assigned department.', 'type' => 'warning']);
            }
        }

        $department = Department::findOrFail($request->department_id);
        $this->service->storeObjective($department, $request->objective_text);

        return redirect()
            ->route('objective.index', [
                'college_id'    => $request->college_id,
                'department_id' => $request->department_id,
            ])
            ->with('toast', ['message' => 'Objective added successfully.', 'type' => 'success']);
    }

    public function objective_update(Request $request, DepartmentObjective $objective)
    {
        $request->validate(['objective_text' => ['required', 'string']]);

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            $assignment = $user->getPrimaryDepartmentAssignment();
            if (!$assignment || (int) $assignment->department_id !== (int) $objective->department_id) {
                return redirect()->route('objective.index')
                    ->with('toast', ['message' => 'You can only manage objectives for your assigned department.', 'type' => 'warning']);
            }
        }

        $this->service->updateObjective($objective, $request->objective_text);

        return redirect()
            ->route('objective.index', [
                'college_id'    => $objective->department->college_id,
                'department_id' => $objective->department_id,
            ])
            ->with('toast', ['message' => 'Objective updated successfully.', 'type' => 'success']);
    }

    public function objective_destroy(DepartmentObjective $objective)
    {
        $collegeId    = $objective->department->college_id;
        $departmentId = $objective->department_id;

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            $assignment = $user->getPrimaryDepartmentAssignment();
            if (!$assignment || (int) $assignment->department_id !== (int) $departmentId) {
                return redirect()->route('objective.index')
                    ->with('toast', ['message' => 'You can only manage objectives for your assigned department.', 'type' => 'warning']);
            }
        }

        try {
            $this->service->destroyObjective($objective);
        } catch (\Throwable) {
            return redirect()->back()->withErrors(['error' => 'Failed to delete objective. Please try again.']);
        }

        return redirect()
            ->route('objective.index', [
                'college_id'    => $collegeId,
                'department_id' => $departmentId,
            ])
            ->with('toast', ['message' => 'Objective deleted and codes re-sequenced.', 'type' => 'success']);
    }
}
