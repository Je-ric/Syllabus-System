<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\Department;
use App\Models\User;
use App\Models\UserAssignment;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrganizationalHierarchyController extends Controller
{
    // colleges with deans
    public function collegesIndex()
    {
        $colleges = College::with('departments')
                ->orderBy('name')
                ->get();

        // Get users who could be deans (active with admin/dean role)
        // DEAN roles (not assignments)
        // EXCLUDE users already assigned as deans of ANY college
        $potentialDeans = $this->getPotentialUsers(['admin', 'dean'], 'dean');

        // Get all dean assignments grouped by college
        $deanAssignments = UserAssignment::where('context', 'dean')
            ->with('user')
            ->get()
            ->groupBy('college_id');

        return view('OrganizationalHierarchy.colleges', compact('colleges', 'potentialDeans', 'deanAssignments'));
    }


    /**
     * Assign dean to a college (one dean per college, one college per dean)
     */
    public function assignDean(Request $request)
    {
        $request->validate([
            'college_id' => 'required|exists:colleges,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $college = College::findOrFail($request->college_id);
        $user = User::findOrFail($request->user_id);

        // Check if user has dean role
        if (!$user->hasRole('dean') && !$user->hasRole('admin')) {
            return back()->with('toast', [
                'message' => 'User must have dean role assigned.',
                'type' => 'error'
            ]);
        }

        // Prevent user from being both dean and chair
        if ($user->isAssignedAsChair()) {
            return back()->with('toast', [
                'message' => 'User cannot be both dean and chair. Remove their chair assignment first.',
                'type' => 'error'
            ]);
        }

        // Check if already dean of ANY college
        if ($user->isAssignedAsDean()) {
            $currentDean = $user->getPrimaryCollegeAssignment();
            return back()->with('toast', [
                'message' => "User is already dean of {$currentDean->college->name}. A dean can only be assigned to one college.",
                'type' => 'error'
            ]);
        }

        // Check if already dean of this college specifically (redundant but kept for safety)
        if ($user->assignments()
            ->where('context', 'dean')
            ->where('college_id', $college->id)
            ->exists()) {
            return back()->with('toast', [
                'message' => "User is already dean of {$college->name}.",
                'type' => 'info'
            ]);
        }

        // Create dean assignment
        UserAssignment::create([
            'user_id' => $user->id,
            'college_id' => $college->id,
            'department_id' => null,
            'context' => 'dean',
        ]);

        $user->ensureFacultyRoleAndAssignment($college->id, null);

        return back()->with('toast', [
            'message' => "{$user->name} is now dean of {$college->name}.",
            'type' => 'success'
        ]);
    }

    /**
     * Remove dean assignment from a college
     */
    public function removeDean(Request $request)
    {
        $request->validate([
            'college_id' => 'required|exists:colleges,id',
            'user_id' => 'required|exists:users,id',
        ]);

        UserAssignment::where('college_id', $request->college_id)
            ->where('user_id', $request->user_id)
            ->where('context', 'dean')
            ->delete();

        return back()->with('toast', [
            'message' => 'Dean assignment removed.',
            'type' => 'success'
        ]);
    }

    /**
     * Display departments with chair assignments for a college
     */
    public function departmentsIndex($collegeId)
    {
        $college = College::with('departments')->findOrFail($collegeId);

        // Get users who could be chairs (active with admin/chair role)
        // EXCLUDE users already assigned as chairs of ANY department
        $potentialChairs = $this->getPotentialUsers(['admin', 'chair'], 'chair');

        // Get potential faculty (users with faculty role, not already assigned to this department)
        $departmentIds = $college->departments->pluck('id')->toArray();
        $potentialFaculty = $this->getPotentialUsers(['admin', 'faculty']);

        // Get all chair assignments
        $chairAssignments = UserAssignment::whereIn('department_id', $departmentIds)
            ->where('context', 'chair')
            ->with('user')
            ->get()
            ->groupBy('department_id');

        // Get all faculty assignments
        $facultyAssignments = UserAssignment::whereIn('department_id', $departmentIds)
            ->where('context', 'faculty')
            ->with('user')
            ->get()
            ->groupBy('department_id');

        return view('OrganizationalHierarchy.departments', compact('college', 'potentialChairs', 'chairAssignments', 'potentialFaculty', 'facultyAssignments'));
    }

    /**
     * Assign chair to a department (one chair per department, one department per chair)
     */
    public function assignChair(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $department = Department::findOrFail($request->department_id);
        $user = User::findOrFail($request->user_id);

        // Check if user has chair role
        if (!$user->hasRole('chair') && !$user->hasRole('admin')) {
            return back()->with('toast', [
                'message' => 'User must have chair role assigned.',
                'type' => 'error'
            ]);
        }

        // Prevent user from being both dean and chair
        if ($user->isAssignedAsDean()) {
            return back()->with('toast', [
                'message' => 'User cannot be both dean and chair. Remove their dean assignment first.',
                'type' => 'error'
            ]);
        }

        // Check if already chair of ANY department
        if ($user->isAssignedAsChair()) {
            $currentChair = $user->getPrimaryDepartmentAssignment();
            return back()->with('toast', [
                'message' => "User is already chair of {$currentChair->department->name}. A chair can only be assigned to one department.",
                'type' => 'error'
            ]);
        }

        // Check if already chair of this department specifically (redundant but kept for safety)
        if ($user->assignments()
            ->where('context', 'chair')
            ->where('department_id', $department->id)
            ->exists()) {
            return back()->with('toast', [
                'message' => "User is already chair of {$department->name}.",
                'type' => 'info'
            ]);
        }

        // Create chair assignment
        UserAssignment::create([
            'user_id' => $user->id,
            'college_id' => null,
            'department_id' => $department->id,
            'context' => 'chair',
        ]);

        $user->ensureFacultyRoleAndAssignment(null, $department->id);

        return back()->with('toast', [
            'message' => "{$user->name} is now chair of {$department->name}.",
            'type' => 'success'
        ]);
    }

    /**
     * Remove chair assignment from a department
     */
    public function removeChair(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'user_id' => 'required|exists:users,id',
        ]);

        UserAssignment::where('department_id', $request->department_id)
            ->where('user_id', $request->user_id)
            ->where('context', 'chair')
            ->delete();

        return back()->with('toast', [
            'message' => 'Chair assignment removed.',
            'type' => 'success'
        ]);
    }

    /**
     * Assign faculty to a department
     */
    public function assignFaculty(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $department = Department::findOrFail($request->department_id);
        $user = User::findOrFail($request->user_id);

        // Check if user has faculty role
        if (!$user->hasRole('faculty') && !$user->hasRole('admin')) {
            return back()->with('toast', [
                'message' => 'User must have faculty role assigned.',
                'type' => 'error'
            ]);
        }

        // Check if already faculty of this department
        if ($user->assignments()
            ->where('context', 'faculty')
            ->where('department_id', $department->id)
            ->exists()) {
            return back()->with('toast', [
                'message' => "User is already faculty of {$department->name}.",
                'type' => 'info'
            ]);
        }

        // Create faculty assignment
        UserAssignment::create([
            'user_id' => $user->id,
            'college_id' => null,
            'department_id' => $department->id,
            'context' => 'faculty',
        ]);

        return back()->with('toast', [
            'message' => "{$user->name} assigned as faculty to {$department->name}.",
            'type' => 'success'
        ]);
    }

    /**
     * Remove faculty assignment from a department
     */
    public function removeFaculty(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'user_id' => 'required|exists:users,id',
        ]);

        UserAssignment::where('department_id', $request->department_id)
            ->where('user_id', $request->user_id)
            ->where('context', 'faculty')
            ->delete();

        return back()->with('toast', [
            'message' => 'Faculty assignment removed.',
            'type' => 'success'
        ]);
    }


    // Display hierarchy view: Dean sees their college, departments, chairs, and faculty
    public function hierarchyView()
    {
        $user = Auth::user();

        if ($user->isDean()) {
            // Get dean's college
            $deanAssignment = UserAssignment::where('user_id', $user->id)
                ->where('context', 'dean')
                ->first();

            if (!$deanAssignment) {
                return view('OrganizationalHierarchy.no-assignment');
            }

            $college = College::with('departments')->findOrFail($deanAssignment->college_id);

            // Get all department chairs and their faculty for this college
            $chairsWithFaculty = [];
            foreach ($college->departments as $department) {
                $chairAssignment = UserAssignment::where('department_id', $department->id)
                    ->where('context', 'chair')
                    ->with('user')
                    ->first();

                $faculty = UserAssignment::where('department_id', $department->id)
                    ->where('context', 'faculty')
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->get();

                $chairsWithFaculty[] = [
                    'department' => $department,
                    'chair' => $chairAssignment,
                    'faculty' => $faculty,
                ];
            }

            return view('OrganizationalHierarchy.hierarchy-dean', compact('college', 'chairsWithFaculty'));
        }

        // Check if user is a chair
        $chairAssignment = UserAssignment::where('user_id', $user->id)
            ->where('context', 'chair')
            ->with('department')
            ->first();

        if ($chairAssignment) {
            // Get chair's department

            $department = $chairAssignment->department;

            // Get all faculty in this department
            $faculty = UserAssignment::where('department_id', $department->id)
                ->where('context', 'faculty')
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();

            return view('OrganizationalHierarchy.hierarchy-chair', compact('department', 'faculty'));
        }

        return view('OrganizationalHierarchy.no-assignment');
    }

    private function getPotentialUsers(array $roles, ?string $excludeContext = null)
    {
        $query = User::whereHas('roles', function ($query) use ($roles) {
            $query->whereIn('name', $roles);
        })
        ->where('account_status', 'active');

        if ($excludeContext) {
            $query->whereNotIn('id', function ($query) use ($excludeContext) {
                $query->select('user_id')
                    ->from('user_assignments')
                    ->where('context', $excludeContext);
            });
        }

        return $query->orderBy('name')->get();
    }

}
