<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\UserAssignment;
use App\Services\UserAssignments\UserAssignmentsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


// This controller is thin — all business logic lives in UserAssignmentsService and UserAssignmentsChecker.
class UserAssignmentsController extends Controller
{
    public function __construct(
        protected UserAssignmentsService $userAssignmentsService
    ) {
    }

    // ############################################################
    // ################### DEAN FUNCTIONS ###################
    // ############################################################

    public function collegesIndex()
    {
        return view('UserManagement.UserAssignments.colleges', $this->userAssignmentsService->collegesIndexData());
    }

    public function assignDean(Request $request)
    {
        $request->validate([
            'college_id' => 'required|exists:colleges,id',
            'user_id'    => 'required|exists:users,id',
        ]);

        $result = $this->userAssignmentsService->assignDean(
            (int) $request->input('college_id'),
            (int) $request->input('user_id')
        );

        return back()->with('toast', $result['toast']);
    }

    public function removeDean(Request $request)
    {
        $request->validate([
            'college_id' => 'required|exists:colleges,id',
            'user_id'    => 'required|exists:users,id',
        ]);

        $result = $this->userAssignmentsService->removeDean(
            (int) $request->input('college_id'),
            (int) $request->input('user_id')
        );

        return back()->with('toast', $result['toast']);
    }

    // ############################################################
    // ################### CHAIR FUNCTIONS ###################
    // ############################################################

    public function departmentsIndex($collegeId)
    {
        /** @var \App\Models\User|null $actor */
        $actor = Auth::user();

        return view(
            'UserManagement.UserAssignments.departments',
            $this->userAssignmentsService->departmentsIndexData($actor, (int) $collegeId)
        );
    }

    public function assignChair(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'user_id'       => 'required|exists:users,id',
        ]);

        /** @var \App\Models\User|null $actor */
        $actor = Auth::user();

        $result = $this->userAssignmentsService->assignChair(
            (int) $request->input('department_id'),
            (int) $request->input('user_id'),
            $actor
        );

        return back()->with('toast', $result['toast']);
    }

    public function removeChair(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'user_id'       => 'required|exists:users,id',
        ]);

        /** @var \App\Models\User|null $actor */
        $actor = Auth::user();

        $result = $this->userAssignmentsService->removeChair(
            (int) $request->input('department_id'),
            (int) $request->input('user_id'),
            $actor
        );

        return back()->with('toast', $result['toast']);
    }

    // ############################################################
    // ################### FACULTY FUNCTIONS ###################
    // ############################################################

    public function assignFaculty(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'user_id'       => 'required|exists:users,id',
        ]);

        /** @var \App\Models\User|null $actor */
        $actor = Auth::user();

        $result = $this->userAssignmentsService->assignFaculty(
            (int) $request->input('department_id'),
            (int) $request->input('user_id'),
            $actor
        );

        return back()->with('toast', $result['toast']);
    }

    public function removeFaculty(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'user_id'       => 'required|exists:users,id',
        ]);

        /** @var \App\Models\User|null $actor */
        $actor = Auth::user();

        $result = $this->userAssignmentsService->removeFaculty(
            (int) $request->input('department_id'),
            (int) $request->input('user_id'),
            $actor
        );

        return back()->with('toast', $result['toast']);
    }

    // Redirect role entry point directly to the appropriate management page.
    public function hierarchyView()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        if ($user->hasRole('admin')) {
            return redirect()->route('user-assignments.colleges.index');
        }

        $deanAssignment = UserAssignment::where('user_id', $user->id)
            ->where('context', 'dean')
            ->first();

        if ($deanAssignment) {
            return redirect()->route('user-assignments.departments.index', $deanAssignment->college_id);
        }

        $chairAssignment = UserAssignment::where('user_id', $user->id)
            ->where('context', 'chair')
            ->with('department')
            ->first();

        if ($chairAssignment && $chairAssignment->department) {
            return redirect()->route('user-assignments.departments.index', $chairAssignment->department->college_id);
        }

        return view('UserManagement.UserAssignments.no-assignment');
    }
}
