<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\College;
use App\Models\Course;
use App\Models\Department;
use App\Models\Program;
use App\Models\UserAssignment;
use Illuminate\Support\Facades\DB;

// Used in: UniversityStructureController

class UniversityStructureService
{
    // COLLEGE

    public function storeCollege(array $data): College
    {
        $college = College::create(['name' => $data['name']]);

        AuditLog::record(
            action: 'created',
            module: 'Academic Structure',
            referenceId: $college->id,
            description: "Created college {$college->name}."
        );

        return $college;
    }

    public function updateCollege(College $college, array $data): void
    {
        $college->update(['name' => $data['name']]);

        AuditLog::record(
            action: 'updated',
            module: 'Academic Structure',
            referenceId: $college->id,
            description: "Updated college to {$college->name}."
        );
    }

    // @throws \Throwable
    public function destroyCollege(College $college): string|null
    {
        $programIds = $college->departments()
            ->with('programs')
            ->get()
            ->flatMap(fn($d) => $d->programs->pluck('id'));

        $courseCount = Course::whereIn('program_id', $programIds)->count();
        if ($courseCount > 0) {
            return "Cannot delete \"{$college->name}\": {$courseCount} course(s) exist under its programs. Delete all courses first.";
        }

        DB::beginTransaction();
        try {
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
            throw $e;
        }

        return null;
    }

    // DEPARTMENT

    public function storeDepartment(array $data): Department
    {
        $department = Department::create([
            'name'       => $data['name'],
            'college_id' => $data['college_id'],
        ]);

        AuditLog::record(
            action: 'created',
            module: 'Academic Structure',
            referenceId: $department->id,
            description: "Created department {$department->name} under college #{$department->college_id}."
        );

        return $department;
    }

    public function updateDepartment(Department $department, array $data): void
    {
        $department->update([
            'name'       => $data['name'],
            'college_id' => $data['college_id'],
        ]);

        AuditLog::record(
            action: 'updated',
            module: 'Academic Structure',
            referenceId: $department->id,
            description: "Updated department {$department->name} under college #{$department->college_id}."
        );
    }

    // @throws \Throwable
    public function destroyDepartment(Department $department): string|null
    {
        $courseCount = Course::whereIn('program_id', $department->programs->pluck('id'))->count();
        if ($courseCount > 0) {
            return "Cannot delete \"{$department->name}\": {$courseCount} course(s) exist under its programs. Delete all courses first.";
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
            throw $e;
        }

        return null;
    }

    // PROGRAM

    // @throws \Throwable
    public function storeProgram(array $data): Program
    {
        DB::beginTransaction();
        try {
            $program = Program::create([
                'name'              => $data['name'],
                'bor_approval_no'   => $data['bor_approval_no'] ?? null,
                'bor_approval_date' => $data['bor_approval_date'] ?? null,
            ]);

            // Attach primary department
            $program->departments()->attach($data['primary_department_id'], ['role' => 'primary']);

            // Attach supporting departments if any
            if (!empty($data['supporting_department_ids'])) {
                foreach ($data['supporting_department_ids'] as $supportingDeptId) {
                    // Skip if the supporting department is the same as primary
                    if ($supportingDeptId != $data['primary_department_id']) {
                        $program->departments()->attach($supportingDeptId, ['role' => 'supporting']);
                    }
                }
            }

            AuditLog::record(
                action: 'created',
                module: 'Academic Structure',
                referenceId: $program->id,
                description: "Created program {$program->name} with primary department #{$data['primary_department_id']}."
            );

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return $program;
    }

    // @throws \Throwable
    // Returns a string error message if the department change is blocked, null otherwise.
    public function updateProgram(Program $program, array $data): string|null
    {
        $currentDeptId = $program->departments()->wherePivot('role', 'primary')->value('departments.id');
        if ((int) $data['primary_department_id'] !== (int) $currentDeptId && $program->courses()->exists()) {
            return 'Cannot change primary department: this program has courses assigned to it. Remove all courses first.';
        }

        DB::beginTransaction();
        try {
            $program->update([
                'name'              => $data['name'],
                'bor_approval_no'   => $data['bor_approval_no'] ?? null,
                'bor_approval_date' => $data['bor_approval_date'] ?? null,
            ]);

            // Prepare departments array for sync
            $departmentsToSync = [
                $data['primary_department_id'] => ['role' => 'primary']
            ];

            // Add supporting departments
            if (!empty($data['supporting_department_ids'])) {
                foreach ($data['supporting_department_ids'] as $supportingDeptId) {
                    // Skip if the supporting department is the same as primary
                    if ($supportingDeptId != $data['primary_department_id']) {
                        $departmentsToSync[$supportingDeptId] = ['role' => 'supporting'];
                    }
                }
            }

            $program->departments()->sync($departmentsToSync);

            AuditLog::record(
                action: 'updated',
                module: 'Academic Structure',
                referenceId: $program->id,
                description: "Updated program {$program->name} and set primary department #{$data['primary_department_id']}."
            );

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return null;
    }

    // @throws \Throwable
    public function destroyProgram(Program $program): string|null
    {
        $courseCount = $program->courses()->count();
        if ($courseCount > 0) {
            return "Cannot delete \"{$program->name}\": {$courseCount} course(s) are assigned to it. Delete all courses first.";
        }

        DB::beginTransaction();
        try {
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
            throw $e;
        }

        return null;
    }
}
