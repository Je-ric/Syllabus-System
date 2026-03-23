<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveCollegeRequest;
use App\Http\Requests\SaveDepartmentRequest;
use App\Http\Requests\SaveProgramRequest;
use App\Models\College;
use App\Models\Department;
use App\Models\Program;
use App\Models\AuditLog;
use App\Models\Course;
use App\Models\UserAssignment;
use Illuminate\Support\Facades\DB;

class AcademicStructureController extends Controller
{
    public function index()
    {
        return view('AcademicStructure.index', [
            'colleges' => College::orderBy('name')->get(),
            'departments' => Department::withRelations()->orderBy('name')->get(),
            'programs' => Program::all()->sortBy('name'),
        ]);
    }

    // =======================
    //  COLLEGE
    // =======================

    public function storeCollege(SaveCollegeRequest $request)
    {
        $validated = $request->validated();

        $college = College::create([
            'name' => $validated['name'],
        ]);

        // LOGS
        AuditLog::record(
            action: 'created',
            module: 'Academic Structure',
            referenceId: $college->id,
            description: "Created college {$college->name}."
        );

        return back()->with('toast', [
            'message' => 'College added successfully.',
            'type' => 'success'
        ]);
    }

    public function updateCollege(SaveCollegeRequest $request, College $college)
    {
        $validated = $request->validated();

        $college->update([
            'name' => $validated['name'],
        ]);

        // LOGS
        AuditLog::record(
            action: 'updated',
            module: 'Academic Structure',
            referenceId: $college->id,
            description: "Updated college to {$college->name}."
        );

        return back()->with('toast', [
            'message' => 'College updated successfully.',
            'type' => 'success'
        ]);
    }

    public function destroyCollege(College $college)
    {
        // #1 — block if any course or syllabus exists under this college
        $programIds = $college->departments()
            ->with('programs')
            ->get()
            ->flatMap(fn($d) => $d->programs->pluck('id'));

        $courseCount = Course::whereIn('program_id', $programIds)->count();
        if ($courseCount > 0) {
            return back()->with('toast', [
                'message' => "Cannot delete \"{$college->name}\": {$courseCount} course(s) exist under its programs. Delete all courses first.",
                'type' => 'error',
            ]);
        }

        DB::beginTransaction();
        try {
            // Detach programs from departments, delete objectives, departments, goals, then college
            foreach ($college->departments as $department) {
                UserAssignment::where('department_id', $department->id)->delete();
                $department->programs()->detach();
                $department->objectives()->delete();
                $department->delete();
            }
            UserAssignment::where('college_id', $college->id)->delete();
            $college->goals()->delete();
            $college->delete();

            AuditLog::record(
                action: 'deleted',
                module: 'Academic Structure',
                referenceId: $college->id,
                description: "Deleted college {$college->name} and its departments."
            );

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('toast', ['message' => 'Failed to delete college. Please try again.', 'type' => 'error']);
        }

        return back()->with('toast', ['message' => 'College deleted successfully.', 'type' => 'success']);
    }

    // =======================
    //  DEPARTMENT
    // =======================

    public function storeDepartment(SaveDepartmentRequest $request)
    {
        $validated = $request->validated();

        $department = Department::create([
            'name' => $validated['name'],
            'college_id' => $validated['college_id'],
        ]);

        // LOGS
        AuditLog::record(
            action: 'created',
            module: 'Academic Structure',
            referenceId: $department->id,
            description: "Created department {$department->name} under college #{$department->college_id}."
        );

        return back()->with('toast', [
            'message' => 'Department added successfully.',
            'type' => 'success'
        ]);
    }

    public function updateDepartment(SaveDepartmentRequest $request, Department $department)
    {
        $validated = $request->validated();

        $department->update([
            'name' => $validated['name'],
            'college_id' => $validated['college_id'],
        ]);

        // LOGS
        AuditLog::record(
            action: 'updated',
            module: 'Academic Structure',
            referenceId: $department->id,
            description: "Updated department {$department->name} under college #{$department->college_id}."
        );

        return back()->with('toast', [
            'message' => 'Department updated successfully.',
            'type' => 'success'
        ]);
    }

    public function destroyDepartment(Department $department)
    {
        // #1 — block if any course exists under this department's programs
        $programIds = $department->programs->pluck('id');
        $courseCount = Course::whereIn('program_id', $programIds)->count();

        if ($courseCount > 0) {
            return back()->with('toast', [
                'message' => "Cannot delete \"{$department->name}\": {$courseCount} course(s) exist under its programs. Delete all courses first.",
                'type' => 'error',
            ]);
        }

        DB::beginTransaction();
        try {
            UserAssignment::where('department_id', $department->id)->delete();
            $department->programs()->detach();
            $department->objectives()->delete();
            $department->delete();

            AuditLog::record(
                action: 'deleted',
                module: 'Academic Structure',
                referenceId: $department->id,
                description: "Deleted department {$department->name}."
            );

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('toast', ['message' => 'Failed to delete department. Please try again.', 'type' => 'error']);
        }

        return back()->with('toast', ['message' => 'Department deleted successfully.', 'type' => 'success']);
    }

    // =======================
    //  PROGRAM
    // =======================

    public function storeProgram(SaveProgramRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            $program = Program::create([
                'name' => $validated['name'],
                'bor_approval_no' => $validated['bor_approval_no'] ?? null,
                'bor_approval_date' => $validated['bor_approval_date'] ?? null,
            ]);

            // Insert into program_departments junction table
            $program->departments()->attach($validated['department_id'], ['role' => 'primary']);

            AuditLog::record(
                action: 'created',
                module: 'Academic Structure',
                referenceId: $program->id,
                description: "Created program {$program->name} under department #{$validated['department_id']}."
            );

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => 'Failed to add program. Please try again.',
            ])->withInput();
        }

        return back()->with('toast', [
            'message' => 'Program added successfully.',
            'type' => 'success'
        ]);
    }

    public function updateProgram(SaveProgramRequest $request, Program $program)
    {
        $validated = $request->validated();

        // #9 — block department change if courses exist (would silently break faculty context)
        $currentDeptId = $program->departments()->wherePivot('role', 'primary')->value('departments.id');
        if ((int) $validated['department_id'] !== (int) $currentDeptId && $program->courses()->exists()) {
            return back()->with('toast', [
                'message' => 'Cannot change department: this program has courses assigned to it. Remove all courses first.',
                'type' => 'error',
            ]);
        }

        DB::beginTransaction();

        try {
            $program->update([
                'name' => $validated['name'],
                'bor_approval_no' => $validated['bor_approval_no'] ?? null,
                'bor_approval_date' => $validated['bor_approval_date'] ?? null,
            ]);

            // Update program_departments junction table
            $program->departments()->sync([$validated['department_id'] => ['role' => 'primary']]);

            AuditLog::record(
                action: 'updated',
                module: 'Academic Structure',
                referenceId: $program->id,
                description: "Updated program {$program->name} and set department #{$validated['department_id']}."
            );

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => 'Failed to update program. Please try again.',
            ])->withInput();
        }

        return back()->with('toast', [
            'message' => 'Program updated successfully.',
            'type' => 'success'
        ]);
    }

    public function destroyProgram(Program $program)
    {
        // #1 — block if any course exists under this program
        $courseCount = $program->courses()->count();
        if ($courseCount > 0) {
            return back()->with('toast', [
                'message' => "Cannot delete \"{$program->name}\": {$courseCount} course(s) are assigned to it. Delete all courses first.",
                'type' => 'error',
            ]);
        }

        DB::beginTransaction();
        try {
            // Detach PEO-PO mappings, then delete POs, PEOs, department pivot, program
            foreach ($program->outcomes as $po) {
                $po->peos()->detach();
                $po->delete();
            }
            $program->peos()->delete();
            $program->departments()->detach();
            $program->delete();

            AuditLog::record(
                action: 'deleted',
                module: 'Academic Structure',
                referenceId: $program->id,
                description: "Deleted program {$program->name} and its PEOs/POs."
            );

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('toast', ['message' => 'Failed to delete program. Please try again.', 'type' => 'error']);
        }

        return back()->with('toast', ['message' => 'Program deleted successfully.', 'type' => 'success']);
    }
}