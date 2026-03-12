<?php

namespace App\Http\Controllers;

use App\Models\UserAssignment;
use App\Services\OrganizationalHierarchy\OrganizationalHierarchyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


// MOSTLY: this controller should be just small, pero naging mahaba siya because of if conditions checker.
class OrganizationalHierarchyController extends Controller
{
    public function __construct(
        protected OrganizationalHierarchyService $organizationalHierarchyService
    ) {
    }

    // ############################################################
    // ################### DEAN FUNCTIONS ###################
    // ############################################################

    // colleges with deans
    public function collegesIndex()
    {
        return view('OrganizationalHierarchy.colleges', $this->organizationalHierarchyService->collegesIndexData());
    }

    // Assign dean to a college (one dean per college, one college per dean)
    public function assignDean(Request $request)
    {
        $request->validate([
            'college_id' => 'required|exists:colleges,id',
            'user_id' => 'required|exists:users,id',
        ]);
        $result = $this->organizationalHierarchyService->assignDean(
            (int) $request->input('college_id'),
            (int) $request->input('user_id')
        );

        return back()->with('toast', $result['toast']);
    }

    // Remove dean assignment from a college
    public function removeDean(Request $request)
    {
        $request->validate([
            'college_id' => 'required|exists:colleges,id',
            'user_id' => 'required|exists:users,id',
        ]);
        $result = $this->organizationalHierarchyService->removeDean(
            (int) $request->input('college_id'),
            (int) $request->input('user_id')
        );

        return back()->with('toast', $result['toast']);
    }

    // ############################################################
    // ################### CHAIR FUNCTIONS ###################
    // ############################################################

    // Display departments with chair assignments for a college
    public function departmentsIndex($collegeId)
    {
        /** @var \App\Models\User|null $actor */
        $actor = Auth::user();

        return view(
            'OrganizationalHierarchy.departments',
            $this->organizationalHierarchyService->departmentsIndexData($actor, (int) $collegeId)
        );
    }

    // Assign chair to a department (one chair per department, one department per chair)
    public function assignChair(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'user_id' => 'required|exists:users,id',
        ]);
        /** @var \App\Models\User|null $actor */
        $actor = Auth::user();

        $result = $this->organizationalHierarchyService->assignChair(
            (int) $request->input('department_id'),
            (int) $request->input('user_id'),
            $actor
        );

        return back()->with('toast', $result['toast']);
    }

    // Remove chair assignment from a department
    public function removeChair(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'user_id' => 'required|exists:users,id',
        ]);
        /** @var \App\Models\User|null $actor */
        $actor = Auth::user();

        $result = $this->organizationalHierarchyService->removeChair(
            (int) $request->input('department_id'),
            (int) $request->input('user_id'),
            $actor
        );

        return back()->with('toast', $result['toast']);
    }

    // ############################################################
    // ################### FACULTY FUNCTIONS ###################
    // ############################################################
    // Assign faculty to a department
    public function assignFaculty(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'user_id' => 'required|exists:users,id',
        ]);
        /** @var \App\Models\User|null $actor */
        $actor = Auth::user();

        $result = $this->organizationalHierarchyService->assignFaculty(
            (int) $request->input('department_id'),
            (int) $request->input('user_id'),
            $actor
        );

        return back()->with('toast', $result['toast']);
    }

    // Remove faculty assignment from a department
    public function removeFaculty(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'user_id' => 'required|exists:users,id',
        ]);
        /** @var \App\Models\User|null $actor */
        $actor = Auth::user();

        $result = $this->organizationalHierarchyService->removeFaculty(
            (int) $request->input('department_id'),
            (int) $request->input('user_id'),
            $actor
        );

        return back()->with('toast', $result['toast']);
    }


    // Redirect role entry point directly to management pages.
    public function hierarchyView()
    {
        /** @var \App\Models\User|null $user */ // this is just for IDE type hinting, can be removed without affecting functionality (linted)
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        if ($user->hasRole('admin')) {
            return redirect()->route('organizational.colleges.index');
        }

        $deanAssignment = UserAssignment::where('user_id', $user->id)
            ->where('context', 'dean')
            ->first();

        if ($deanAssignment) {
            return redirect()->route('organizational.departments.index', $deanAssignment->college_id);
        }

        $chairAssignment = UserAssignment::where('user_id', $user->id)
            ->where('context', 'chair')
            ->with('department')
            ->first();

        if ($chairAssignment && $chairAssignment->department) {
            return redirect()->route('organizational.departments.index', $chairAssignment->department->college_id);
        }

        return view('OrganizationalHierarchy.no-assignment');
    }

}
