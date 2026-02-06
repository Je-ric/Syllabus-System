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
    /**
     * Display colleges with dean assignments
     */
    public function collegesIndex()
    {
        $colleges = College::with('departments')->get();

        // Get users who could be deans (active with admin/dean role)
        $potentialDeans = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['admin', 'dean']);
        })
        ->where('account_status', 'active')
        ->orderBy('name')
        ->get();

        // Get all dean assignments grouped by college
        $deanAssignments = UserAssignment::where('context', 'dean')
            ->with('user')
            ->get()
            ->groupBy('college_id');

        return view('OrganizationalHierarchy.colleges', compact('colleges', 'potentialDeans', 'deanAssignments'));
    }

    /**
     * Assign dean to a college (allow multiple colleges)
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
        if ($user->assignments()->where('context', 'chair')->exists()) {
            return back()->with('toast', [
                'message' => 'User cannot be both dean and chair. Remove their chair assignment first.',
                'type' => 'error'
            ]);
        }

        // Check if already dean of this college
        if ($user->assignments()
            ->where('context', 'dean')
            ->where('college_id', $college->id)
            ->exists()) {
            return back()->with('toast', [
                'message' => "User is already dean of {$college->name}.",
                'type' => 'info'
            ]);
        }

        // Create new dean assignment (allow multiple colleges)
        UserAssignment::create([
            'user_id' => $user->id,
            'college_id' => $college->id,
            'department_id' => null,
            'context' => 'dean',
        ]);

        // Also assign faculty role if not already assigned
        $facultyRole = Role::where('name', 'faculty')->firstOrCreate(['name' => 'faculty']);
        if (!$user->roles()->where('role_id', $facultyRole->id)->exists()) {
            $user->roles()->attach($facultyRole->id);
        }

        // Also create faculty assignment for this college
        UserAssignment::firstOrCreate(
            [
                'user_id' => $user->id,
                'college_id' => $college->id,
                'department_id' => null,
                'context' => 'faculty',
            ]
        );

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
        $potentialChairs = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['admin', 'chair']);
        })
        ->where('account_status', 'active')
        ->orderBy('name')
        ->get();

        // Get all chair assignments for this college's departments
        $departmentIds = $college->departments->pluck('id')->toArray();
        $chairAssignments = UserAssignment::whereIn('department_id', $departmentIds)
            ->where('context', 'chair')
            ->with('user')
            ->get()
            ->groupBy('department_id');

        return view('OrganizationalHierarchy.departments', compact('college', 'potentialChairs', 'chairAssignments'));
    }

    /**
     * Assign chair to a department (allow multiple departments)
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
        if ($user->assignments()->where('context', 'dean')->exists()) {
            return back()->with('toast', [
                'message' => 'User cannot be both dean and chair. Remove their dean assignment first.',
                'type' => 'error'
            ]);
        }

        // Check if already chair of this department
        if ($user->assignments()
            ->where('context', 'chair')
            ->where('department_id', $department->id)
            ->exists()) {
            return back()->with('toast', [
                'message' => "User is already chair of {$department->name}.",
                'type' => 'info'
            ]);
        }

        // Create new chair assignment (allow multiple departments)
        UserAssignment::create([
            'user_id' => $user->id,
            'college_id' => null,
            'department_id' => $department->id,
            'context' => 'chair',
        ]);

        // Also assign faculty role if not already assigned
        $facultyRole = Role::where('name', 'faculty')->firstOrCreate(['name' => 'faculty']);
        if (!$user->roles()->where('role_id', $facultyRole->id)->exists()) {
            $user->roles()->attach($facultyRole->id);
        }

        // Also create faculty assignment for this department
        UserAssignment::firstOrCreate(
            [
                'user_id' => $user->id,
                'college_id' => null,
                'department_id' => $department->id,
                'context' => 'faculty',
            ]
        );

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
     * Display hierarchy view: Dean sees their college, departments, chairs, and faculty
     */
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
}
